<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    public function index(): View
    {
        return view('shop.suivi-commande', ['order' => null]);
    }

    public function find(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'order_number' => ['required', 'string', 'max:40'],
            'customer_email' => ['required', 'email'],
        ]);

        $order = Order::query()
            ->where('order_number', strtoupper(trim($data['order_number'])))
            ->where('customer_email', $data['customer_email'])
            ->with('items')
            ->first();

        if (! $order) {
            return back()->withInput()->with('error', t('tracking.not_found'));
        }

        return view('shop.suivi-commande', compact('order'));
    }
}
