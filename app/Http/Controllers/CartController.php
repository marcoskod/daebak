<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        return view('shop.panier', $this->cartData());
    }

    protected function cartData(): array
    {
        $items = Cart::items();
        $subtotal = $items->sum('subtotal');

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'threshold' => (float) site('shipping.free_threshold'),
            'estimate' => Cart::shippingCost($subtotal, null),
        ];
    }

    public function add(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'nullable|integer|min:1|max:20',
            'size' => 'nullable|string|max:40',
            'color' => 'nullable|string|max:40',
        ]);

        $product = Product::where('is_active', true)->findOrFail($data['product_id']);

        if ($product->isOutOfStock()) {
            return response()->json(['success' => false, 'message' => t('cart.out_of_stock')], 422);
        }

        // Valide les options choisies ; retombe sur les valeurs par défaut du produit.
        $size = $data['size'] ?? null;
        $color = $data['color'] ?? null;
        if ($product->sizes()) {
            $size = in_array($size, $product->sizes(), true) ? $size : $product->sizes()[0];
        } else {
            $size = null;
        }
        $colorKeys = array_column($product->colors(), 'key');
        $color = in_array($color, $colorKeys, true) ? $color : ($colorKeys[0] ?? null);

        Cart::add($product->id, $data['qty'] ?? 1, $size, $color);

        return response()->json([
            'success' => true,
            'message' => t('cart.added', ['name' => $product->localized_name]),
            'cart_count' => Cart::count(),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'key' => 'required|string|max:120',
            'qty' => 'required|integer|min:0|max:20',
        ]);

        Cart::update($data['key'], $data['qty']);

        return response()->json([
            'success' => true,
            'cart_count' => Cart::count(),
            'html' => view('shop.partials.cart-content', $this->cartData())->render(),
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        return $this->update($request->merge(['qty' => 0]));
    }
}
