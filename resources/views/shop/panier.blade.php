@extends('layouts.shop')

@section('title', t('cart.title'))

@section('content')
<x-page-hero :title="t('cart.title')" :subtitle="t('cart.subtitle')" image="cart" :crumbs="[['label' => t('nav.shop'), 'url' => route('shop.catalogue')], ['label' => t('cart.title')]]" />

<div id="cart-root">
    @include('shop.partials.cart-content')
</div>
@endsection
