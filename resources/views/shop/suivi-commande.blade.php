@extends('layouts.shop')

@section('title', t('tracking.title'))

@section('content')
<x-page-hero :title="t('tracking.title')" :subtitle="t('tracking.subtitle')" image="tracking" :crumbs="[['label' => t('tracking.title')]]" />

<div class="{{ $order ? 'cart-layout' : '' }}" style="{{ $order ? '' : 'max-width:620px;margin-inline:auto;width:100%' }}">
    <section class="card stack">
        @if ($order)
            <div class="row" style="justify-content:space-between"><h2 style="font-size:22px;font-weight:800">{{ $order->order_number }}</h2><span class="pill pill-brand">{{ $order->status_label }}</span></div>
            @include('shop.partials.order-steps', ['order' => $order])
            <div class="table-wrap"><table class="table"><thead><tr><th>{{ t('confirm.item') }}</th><th>{{ t('cart.quantity') }}</th><th style="text-align:right">{{ t('cart.total') }}</th></tr></thead><tbody>
                @foreach ($order->items as $it)
                    <tr><td><b>{{ $it->product_name }}</b><br><small class="muted">{{ collect($it->options ?? [])->map(fn ($v, $k) => $k === 'color' ? t('colors.'.$v) : $v)->implode(' · ') }}</small></td><td>{{ $it->quantity }}</td><td style="text-align:right">{{ money($it->line_total) }}</td></tr>
                @endforeach
            </tbody></table></div>
            <a href="{{ route('tracking.index') }}" class="btn btn-ghost" style="width:fit-content"><i class="fa-solid fa-magnifying-glass"></i> {{ t('tracking.another') }}</a>
        @else
            <form action="{{ route('tracking.find') }}" method="POST" class="stack">
                @csrf
                @if (session('error'))<div class="alert alert-err" role="alert"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>@endif
                <div class="field"><label for="order_number">{{ t('tracking.number') }}</label><input class="input" id="order_number" name="order_number" required placeholder="DB-2026-00001" value="{{ old('order_number') }}"></div>
                <div class="field"><label for="customer_email">{{ t('checkout.email') }}</label><input class="input" type="email" id="customer_email" name="customer_email" required value="{{ old('customer_email') }}"></div>
                <button class="btn btn-primary btn-lg" type="submit"><i class="fa-solid fa-truck-fast"></i> {{ t('tracking.button') }}</button>
            </form>
        @endif
    </section>
    @if ($order)
        <aside class="card summary">
            <h2 style="font-size:20px;font-weight:800">{{ t('confirm.details') }}</h2>
            <div class="sum-row"><span>{{ t('cart.subtotal') }}</span><span>{{ money($order->subtotal) }}</span></div>
            <div class="sum-row"><span>{{ t('cart.shipping') }}</span><span>{{ $order->shipping_cost > 0 ? money($order->shipping_cost) : t('js.free') }}</span></div>
            <div class="sum-row total"><span>{{ t('cart.total') }}</span><span>{{ money($order->total) }}</span></div>
            <div class="stack" style="gap:2px"><b>{{ t('checkout.shipping') }}</b><span class="muted">{{ $order->shipping_address }}<br>{{ $order->shipping_postal_code }} {{ $order->shipping_city }}, {{ t('countries.'.$order->shipping_country) }}</span></div>
        </aside>
    @endif
</div>
@endsection
