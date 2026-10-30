@extends('layouts.shop')

@section('title', t('wishlist.title'))

@section('content')
<x-page-hero :title="t('wishlist.title')" :subtitle="t('wishlist.subtitle')" image="wishlist" :crumbs="[['label' => t('nav.wishlist')]]" />

<section class="section">
    <div id="wishlist-grid" class="product-grid">
        <div class="skel-card"><div class="skel"></div><div class="skel skel-line w60"></div></div>
        <div class="skel-card"><div class="skel"></div><div class="skel skel-line w60"></div></div>
        <div class="skel-card"><div class="skel"></div><div class="skel skel-line w60"></div></div>
    </div>
    <template id="wishlist-empty">
        <div class="empty" style="grid-column:1/-1">
            <i class="fa-regular fa-heart"></i>
            <h3>{{ t('wishlist.empty_title') }}</h3>
            <p class="muted">{{ t('wishlist.empty_text') }}</p>
            <a href="{{ route('shop.catalogue') }}" class="btn btn-primary">{{ t('cart.continue') }}</a>
        </div>
    </template>
</section>
@endsection
