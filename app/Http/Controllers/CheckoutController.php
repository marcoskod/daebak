<?php

namespace App\Http\Controllers;

use App\Mail\OrderAdminMail;
use App\Mail\OrderConfirmedMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Services\PaymentService;
use App\Support\Cart;
use App\Support\DeferredMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(protected PaymentService $payments)
    {
    }

    /** Moyens de paiement actifs (réglages admin « Paramètres boutique »). */
    protected function enabledMethods(): array
    {
        return array_values(array_filter(site('payments'), fn ($m) => Setting::getBool("payment_{$m}_enabled")));
    }

    protected function defaultCountry(): string
    {
        return ['en' => 'US', 'fr' => 'FR', 'ko' => 'KR'][app()->getLocale()] ?? 'US';
    }

    public function index(): View|RedirectResponse
    {
        $items = Cart::items();
        if ($items->isEmpty()) {
            return redirect()->route('shop.panier')->with('error', t('cart.empty_title'));
        }

        return view('shop.commande', [
            'items' => $items,
            'subtotal' => Cart::total(),
            'methods' => $this->enabledMethods(),
            'country' => session('country', $this->defaultCountry()),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $items = Cart::items();
        if ($items->isEmpty()) {
            return response()->json(['success' => false, 'message' => t('cart.empty_title')], 422);
        }

        $methods = $this->enabledMethods();
        if (! $methods) {
            return response()->json(['success' => false, 'message' => t('checkout.no_payment')], 422);
        }

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:190'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_city' => ['required', 'string', 'max:120'],
            'shipping_postal_code' => ['nullable', 'string', 'max:20'],
            'shipping_country' => ['required', 'in:'.implode(',', site('shipping.countries'))],
            'payment_method' => ['required', 'in:'.implode(',', $methods)],
            'accept_terms' => ['accepted'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);
        unset($data['accept_terms']);

        $subtotal = Cart::total();
        $shipping = Cart::shippingCost($subtotal, $data['shipping_country']);
        $total = $subtotal + $shipping;
        session(['country' => $data['shipping_country']]);

        try {
            $order = DB::transaction(function () use ($data, $items, $subtotal, $shipping, $total) {
                $locked = Product::query()->whereIn('id', $items->pluck('product.id'))->orderBy('id')->lockForUpdate()->get()->keyBy('id');

                foreach ($items as $item) {
                    $product = $locked[$item['product']->id];
                    if ($item['qty'] > $product->stock_quantity) {
                        throw new \DomainException(t('cart.out_of_stock'));
                    }
                }

                $order = Order::create([
                    ...$data,
                    'order_number' => 'TMP-'.Str::random(20),
                    'user_id' => auth()->id(),
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shipping,
                    'total' => $total,
                    'currency' => 'USD',
                    'locale' => app()->getLocale(),
                    'status' => 'pending',
                    'payment_status' => 'pending',
                ]);
                $order->update(['order_number' => Order::numberFor($order->id)]);

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product']->id,
                        'product_name' => $item['product']->localized_name,
                        'product_sku' => $item['product']->sku,
                        'options' => array_filter(['size' => $item['size'], 'color' => $item['color']]),
                        'provider' => $item['product']->provider,
                        'price' => $item['unit_price'],
                        'quantity' => $item['qty'],
                        'line_total' => $item['subtotal'],
                    ]);
                    $locked[$item['product']->id]->adjustStock('out', $item['qty'], "Order {$order->order_number}");
                }

                return $order;
            });
        } catch (\DomainException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        Cart::clear();
        session(['last_order_id' => $order->id]);

        $checkout = $this->payments->createCheckout($order);
        $order->update(['payment_reference' => $checkout['reference']]);

        return response()->json([
            'success' => true,
            'simulated' => $checkout['simulated'],
            'redirect_url' => $checkout['url'],
            'order_number' => $order->order_number,
        ], 201);
    }

    public function confirmation(Order $order): View
    {
        $owns = auth()->check() && $order->user_id === auth()->id();
        abort_unless($owns || session('last_order_id') === $order->id, 403);

        return view('shop.commande-confirmation', compact('order'));
    }

    /** Page de paiement simulée (mode démo, aucune donnée bancaire n'est demandée ni transmise). */
    public function demo(Order $order): View|RedirectResponse
    {
        abort_unless(session('last_order_id') === $order->id, 403);
        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.confirmation', $order);
        }

        return view('shop.paiement-demo', compact('order'));
    }

    public function demoPay(Order $order): JsonResponse
    {
        abort_unless(session('last_order_id') === $order->id, 403);

        if ($order->payment_status !== 'paid') {
            $order->update(['payment_status' => 'paid', 'status' => 'processing']);
            $this->sendOrderConfirmationEmails($order);
        }

        return response()->json(['success' => true, 'redirect_url' => route('checkout.confirmation', $order)]);
    }

    protected function sendOrderConfirmationEmails(Order $order): void
    {
        $order->loadMissing('items');
        DeferredMail::send($order->customer_email, $order->customer_name, new OrderConfirmedMail($order));

        if ($adminEmail = config('mail.admin_address')) {
            DeferredMail::send($adminEmail, null, new OrderAdminMail($order));
        }
    }
}
