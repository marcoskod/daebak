@extends('layouts.shop')

@section('title', t('demo.title'))

@section('content')
<x-page-hero :title="t('demo.title')" :subtitle="t('demo.subtitle')" image="checkout" :crumbs="[['label' => t('checkout.title')], ['label' => t('demo.title')]]" />

<section class="card stack" style="max-width:560px;margin-inline:auto;width:100%">
    <span class="pill pill-warn" style="width:fit-content"><i class="fa-solid fa-flask"></i> {{ t('demo.badge') }}</span>
    <h2 style="font-size:22px;font-weight:800">{{ t('demo.order') }} {{ $order->order_number }}</h2>
    <div class="sum-row total"><span>{{ t('cart.total') }}</span><span>{{ money($order->total) }}</span></div>
    <p class="muted">{{ t('demo.text') }}</p>
    <form id="demo-pay-form" action="{{ route('checkout.demo.pay', $order) }}" method="POST" class="stack">
        @csrf
        <button type="submit" class="btn btn-primary btn-lg btn-block"><i class="fa-solid fa-circle-check"></i> {{ t('demo.confirm') }}</button>
        <a href="{{ route('shop.home') }}" class="btn btn-ghost btn-block">{{ t('demo.cancel') }}</a>
    </form>
</section>
@endsection
