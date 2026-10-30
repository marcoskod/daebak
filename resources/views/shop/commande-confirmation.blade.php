@extends('layouts.shop')

@section('title', t('confirm.title'))

@section('content')
<x-page-hero :title="t('confirm.title')" :subtitle="t('confirm.subtitle')" image="confirmation" :crumbs="[['label' => t('confirm.title')]]" />

<div class="cart-layout">
    <section class="card stack">
        <div class="row" style="gap:16px">
            <span style="width:64px;height:64px;border-radius:50%;background:var(--ok-soft);color:var(--ok);display:grid;place-items:center;font-size:28px"><i class="fa-solid fa-circle-check"></i></span>
            <div><h2 style="font-size:22px;font-weight:800">{{ t('confirm.thanks', ['name' => $order->customer_name]) }}</h2><p class="muted">{{ t('confirm.number') }} <b style="color:var(--ink)">{{ $order->order_number }}</b> · {{ t('confirm.email_sent', ['email' => $order->customer_email]) }}</p></div>
        </div>
        @include('shop.partials.order-steps', ['order' => $order])
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>{{ t('confirm.item') }}</th><th>{{ t('cart.quantity') }}</th><th style="text-align:right">{{ t('cart.total') }}</th></tr></thead>
                <tbody>
                    @foreach ($order->items as $it)
                        <tr><td><b>{{ $it->product_name }}</b><br><small class="muted">{{ collect($it->options ?? [])->map(fn ($v, $k) => $k === 'color' ? t('colors.'.$v) : $v)->implode(' · ') }}</small></td><td>{{ $it->quantity }}</td><td style="text-align:right">{{ money($it->line_total) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="row"><a href="{{ route('tracking.index') }}" class="btn btn-soft"><i class="fa-solid fa-truck-fast"></i> {{ t('nav.tracking') }}</a><a href="{{ route('shop.catalogue') }}" class="btn btn-primary">{{ t('cart.continue') }}</a></div>
    </section>
    <aside class="card summary">
        <h2 style="font-size:20px;font-weight:800">{{ t('confirm.details') }}</h2>
        <div class="sum-row"><span>{{ t('cart.subtotal') }}</span><span>{{ money($order->subtotal) }}</span></div>
        <div class="sum-row"><span>{{ t('cart.shipping') }}</span><span>{{ $order->shipping_cost > 0 ? money($order->shipping_cost) : t('js.free') }}</span></div>
        <div class="sum-row total"><span>{{ t('cart.total') }}</span><span>{{ money($order->total) }}</span></div>
        <div class="sum-row"><span>{{ t('checkout.payment') }}</span><span>{{ $order->payment_method_label }}</span></div>
        <div class="sum-row"><span>{{ t('confirm.status') }}</span><span class="pill pill-{{ $order->payment_status === 'paid' ? 'ok' : 'warn' }}">{{ t('order.payment.'.$order->payment_status) }}</span></div>
        <div class="stack" style="gap:2px"><b>{{ t('checkout.shipping') }}</b><span class="muted">{{ $order->shipping_address }}<br>{{ $order->shipping_postal_code }} {{ $order->shipping_city }}, {{ t('countries.'.$order->shipping_country) }}</span></div>
    </aside>
</div>
@endsection
