@extends('layouts.shop')

@php
    $name = $product->localized_name;
    $cat = $product->category;
    $crumbs = [['label' => t('nav.shop'), 'url' => route('shop.catalogue')]];
    if ($cat?->parent) { $crumbs[] = ['label' => $cat->parent->localized_name, 'url' => route('shop.catalogue', ['category' => $cat->parent->slug])]; }
    if ($cat) { $crumbs[] = ['label' => $cat->localized_name, 'url' => route('shop.catalogue', ['category' => $cat->slug])]; }
    $crumbs[] = ['label' => $name];

    $colors = collect($product->colors())->map(fn ($c) => $c + ['url' => asset($c['image'])])->values();
    $gallery = $product->gallery;
    $sizes = $product->sizes();
    $sizePrices = $product->variants['size_prices'] ?? null;
    $provider = $product->providerInfo();
    $zones = site('shipping.zones');
    $days = site('shipping.business_days');
    $total = max(1, $product->reviews_count ?: $reviews->count());
    $isApparel = in_array($product->product_type, ['tee', 'hoodie'], true);

    $productJs = [
        'id' => $product->id, 'price' => $product->current_price, 'old' => $product->sale_price ? (float) $product->price : null,
        'sizes' => $sizes, 'sizePrices' => $sizePrices,
        'colors' => $colors->map(fn ($c) => ['key' => $c['key'], 'image' => $c['url']])->all(), 'gallery' => $gallery,
        'shipDays' => $days, 'shipCost' => $zones, 'freeShip' => site('shipping.free_threshold'),
        'production' => [$product->production_min, $product->production_max],
    ];
@endphp

@section('title', $name)
@section('description', \Illuminate\Support\Str::limit($product->localized_short ?: strip_tags($product->localized_description), 155))
@section('og_image', $product->image_url)

@section('content')
<x-page-hero :title="$cat?->localized_name ?? t('nav.shop')" image="product" :crumbs="$crumbs" tag="p" />

<div class="pdp">
    <div class="stack" style="gap:24px;min-width:0">
        <div class="gallery reveal">
            <div class="thumbs">
                @foreach ($gallery as $i => $img)
                    <button type="button" class="thumb {{ $i === 0 ? 'is-active' : '' }}" aria-label="{{ t('product.view_image', ['n' => $i + 1]) }}"><img src="{{ $img }}" alt="" loading="lazy" width="76" height="76"></button>
                @endforeach
            </div>
            <div class="gallery-main">
                <img id="gallery-main-img" src="{{ $gallery[0] }}" alt="{{ $name }}" width="720" height="720">
                @if (count($gallery) > 1)
                    <button type="button" id="gal-prev" class="gallery-nav prev" aria-label="{{ t('common.previous') }}"><i class="fa-solid fa-chevron-left"></i></button>
                    <button type="button" id="gal-next" class="gallery-nav next" aria-label="{{ t('common.next') }}"><i class="fa-solid fa-chevron-right"></i></button>
                @endif
                <div class="pcard-tags" style="left:16px;top:16px">
                    @if ($product->sale_price)<span class="pill pill-danger">-{{ $product->discount_percent }}%</span>@endif
                    @if ($product->badge && $product->badge !== 'sale')<span class="pill pill-dark">{{ t('product.badge.'.$product->badge) }}</span>@endif
                </div>
            </div>
        </div>

        {{-- Description : template responsive choisi dans l'admin (ou ?tpl=nom pour prévisualiser) --}}
        <section class="card reveal" id="description" aria-labelledby="desc-title">
            <div class="row" style="justify-content:space-between;margin-bottom:14px">
                <h2 id="desc-title" style="font-size:22px;font-weight:800">{{ t('product.description') }}</h2>
                @if (auth()->user()?->is_admin)
                    <div class="dd">
                        <button type="button" class="btn btn-soft btn-sm" data-dd-toggle><i class="fa-solid fa-swatchbook"></i> {{ t('product.template') }}: {{ \App\Support\ProductDescription::templateText($template) }}</button>
                        <div class="dd-menu down" style="left:auto;right:0;min-width:230px">
                            @foreach (\App\Support\ProductDescription::templates() as $key => $tpl)
                                <a href="{{ route('shop.produit', [$product->slug, 'tpl' => $key]) }}#description" class="{{ $key === $template ? 'is-current' : '' }}">{{ \App\Support\ProductDescription::templateText($key) }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            <x-product-description :text="$product->localized_description" :template="$template" />
        </section>

        {{-- Avis --}}
        <section class="card reveal" id="reviews">
            <h2 style="font-size:22px;font-weight:800;margin-bottom:16px">{{ t('product.reviews') }}</h2>
            <div class="rating-box">
                <div class="rating-big"><strong>{{ number_format($product->rating, 1) }}</strong><div class="stars" style="justify-content:center;font-size:16px;margin:6px 0">@for ($i = 1; $i <= 5; $i++)<i class="fa-{{ $i <= round($product->rating) ? 'solid' : 'regular' }} fa-star"></i>@endfor</div><span class="muted">{{ t('product.reviews_count', ['n' => $product->reviews_count]) }}</span></div>
                <div class="bars">
                    @foreach ($histogram as $stars => $n)
                        @php $pct = $reviews->count() ? round($n / $reviews->count() * 100) : 0; @endphp
                        <div class="bar-row"><span>{{ $stars }} <i class="fa-solid fa-star" style="color:#f2b632;font-size:10px"></i></span><div class="bar"><span style="width:{{ $pct }}%"></span></div><span>{{ $pct }}%</span></div>
                    @endforeach
                </div>
            </div>
            <div style="margin-top:12px">
                @foreach ($reviews->take(6) as $r)
                    <article class="review">
                        <header><span class="avatar">{{ mb_substr($r->author, 0, 1) }}</span><b>{{ $r->author }}</b><span class="fi fi-{{ strtolower($r->country) }} fis rounded"></span><span class="pill pill-ok"><i class="fa-solid fa-circle-check"></i> {{ t('product.verified') }}</span><span class="muted" style="margin-left:auto;font-size:12.5px">{{ $r->created_at->locale(app()->getLocale())->diffForHumans() }}</span></header>
                        <div class="stars">@for ($i = 0; $i < $r->rating; $i++)<i class="fa-solid fa-star"></i>@endfor</div>
                        <strong>{{ $r->title }}</strong>
                        <p class="muted">{{ $r->body }}</p>
                    </article>
                @endforeach
                @if ($reviews->first()?->is_sample)<p class="muted" style="font-size:12px;margin-top:8px"><i class="fa-solid fa-circle-info"></i> {{ t('product.sample_reviews') }}</p>@endif
            </div>
        </section>
    </div>

    {{-- Colonne d'achat (style carte de réservation Airbnb) --}}
    <aside class="buybox reveal">
        <div class="stack">
            <div class="row">
                @if ($provider)<span class="provider-chip" style="--pc:{{ $provider['color'] }}"><i class="{{ $provider['icon'] }}"></i> {{ t('product.fulfilled_by', ['name' => $provider['name']]) }}</span>@endif
                <span class="spacer"></span>
                <button type="button" class="btn btn-ghost btn-icon btn-sm" data-wish="{{ $product->id }}" aria-label="{{ t('product.add_wishlist') }}"><i class="fa-regular fa-heart"></i></button>
                <button type="button" id="share-btn" class="btn btn-ghost btn-icon btn-sm" aria-label="{{ t('product.share') }}"><i class="fa-solid fa-arrow-up-from-bracket"></i></button>
            </div>
            <h1>{{ $name }}</h1>
            <a href="#reviews" class="row" style="gap:8px"><span class="stars" style="font-size:14px">@for ($i = 1; $i <= 5; $i++)<i class="fa-{{ $i <= round($product->rating) ? 'solid' : 'regular' }} fa-star"></i>@endfor</span><b>{{ number_format($product->rating, 1) }}</b><span class="muted" style="text-decoration:underline">{{ t('product.reviews_count', ['n' => $product->reviews_count]) }}</span></a>
            <p class="muted">{{ $product->localized_short }}</p>
        </div>

        <div class="buy-card">
            <div class="price" style="align-items:center">
                <strong id="pdp-price" style="font-size:30px;letter-spacing:-.03em">{{ money($product->current_price) }}</strong>
                @if ($product->sale_price)<s id="pdp-old" style="font-size:16px">{{ money($product->price) }}</s><span class="pill pill-danger">-{{ $product->discount_percent }}%</span>@endif
            </div>
            @if (current_currency() !== 'USD')<p class="muted" style="font-size:12px;margin-top:-8px">{{ t('product.charged_usd', ['amount' => '$'.number_format($product->current_price, 2)]) }}</p>@endif

            @if ($colors->count() > 1)
                <div>
                    <div class="opt-title"><span>{{ t('product.color') }}: <b id="color-label" style="font-weight:600">{{ t('colors.'.$colors[0]['key']) }}</b></span></div>
                    <div class="row" style="gap:10px">
                        @foreach ($colors as $i => $c)
                            <button type="button" class="color-btn {{ $i === 0 ? 'is-active' : '' }}" data-color="{{ $c['key'] }}" data-label="{{ t('colors.'.$c['key']) }}" data-image="{{ $c['url'] }}" style="background:{{ $c['hex'] }}" aria-label="{{ t('colors.'.$c['key']) }}"></button>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (count($sizes))
                <div>
                    <div class="opt-title"><span>{{ t('product.size') }}: <b id="size-label" style="font-weight:600">{{ $sizes[0] }}</b></span>@if ($isApparel)<button type="button" data-modal-open="size-modal">{{ t('product.size_guide') }}</button>@endif</div>
                    <div class="sizes">
                        @foreach ($sizes as $i => $s)
                            <button type="button" class="size-btn {{ $i === 0 ? 'is-active' : '' }}" data-size="{{ $s }}">{{ $s }}</button>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="row" style="justify-content:space-between">
                <div class="qty"><button type="button" id="qty-minus" aria-label="−"><i class="fa-solid fa-minus"></i></button><input id="qty-input" type="text" inputmode="numeric" value="1" aria-label="{{ t('cart.quantity') }}"><button type="button" id="qty-plus" aria-label="+"><i class="fa-solid fa-plus"></i></button></div>
                <div style="text-align:right"><span class="muted" style="font-size:12px;display:block">{{ t('cart.total') }}</span><strong id="pdp-total" style="font-size:20px">{{ money($product->current_price) }}</strong></div>
            </div>

            <div class="stack" style="gap:10px">
                <button type="button" class="btn btn-primary btn-lg btn-block" data-pdp-add><i class="fa-solid fa-bag-shopping"></i> {{ t('product.add_to_cart') }}</button>
                <button type="button" class="btn btn-soft btn-lg btn-block" data-pdp-buy><i class="fa-solid fa-bolt"></i> {{ t('product.buy_now') }}</button>
            </div>

            <div class="eta">
                <div class="row" style="justify-content:space-between"><b><i class="fa-solid fa-truck-fast" style="color:var(--leaf)"></i> {{ t('product.delivery_estimate') }}</b><b id="eta-dates" style="color:var(--leaf)"></b></div>
                <label class="sr-only" for="eta-country">{{ t('checkout.country') }}</label>
                <select id="eta-country" class="select" style="height:42px">
                    @foreach (site('shipping.countries') as $code)<option value="{{ $code }}">{{ t('countries.'.$code) }}</option>@endforeach
                </select>
                <div class="eta-steps">
                    <div><i class="fa-solid fa-print"></i><b>{{ t('product.eta_production') }}</b><span id="eta-prod" class="muted"></span></div>
                    <div><i class="fa-solid fa-plane"></i><b>{{ t('product.eta_shipping') }}</b><span id="eta-ship" class="muted"></span></div>
                    <div><i class="fa-solid fa-box-open"></i><b>{{ t('product.eta_total') }}</b><span id="eta-total" class="muted"></span></div>
                </div>
                <div class="muted" style="font-size:12.5px">{{ t('product.shipping_cost') }}: <b id="eta-cost" style="color:var(--ink)"></b> · {{ t('product.free_over', ['amount' => money(site('shipping.free_threshold'))]) }}</div>
            </div>

            <div class="trust">
                <div><i class="fa-solid fa-print"></i> {{ t('product.trust_pod') }}</div>
                <div><i class="fa-solid fa-leaf"></i> {{ t('product.trust_eco') }}</div>
                <div><i class="fa-solid fa-shield-halved"></i> {{ t('product.trust_secure') }}</div>
                <div><i class="fa-solid fa-rotate-left"></i> {{ t('product.trust_returns') }}</div>
            </div>
        </div>
    </aside>
</div>

@if ($related->count())
    <section class="section">
        <div class="sec-head reveal"><div><h2>{{ t('product.related') }}</h2></div>
            <div class="rail-ctl"><button class="btn btn-ghost btn-icon btn-sm" data-rail-prev="rail-related" aria-label="{{ t('common.previous') }}"><i class="fa-solid fa-chevron-left"></i></button><button class="btn btn-ghost btn-icon btn-sm" data-rail-next="rail-related" aria-label="{{ t('common.next') }}"><i class="fa-solid fa-chevron-right"></i></button></div>
        </div>
        <div class="rail" id="rail-related">@foreach ($related as $p)@include('shop.partials.product-card', ['product' => $p])@endforeach</div>
    </section>
@endif

{{-- CTA collant (mobile / tablette) --}}
<div class="sticky-cta">
    <div><b id="sticky-price">{{ money($product->current_price) }}</b><span class="muted" style="font-size:12px;display:block">{{ t('product.ships_in', ['a' => $product->production_min, 'b' => $product->production_max]) }}</span></div>
    <button type="button" class="btn btn-primary" data-pdp-sticky><i class="fa-solid fa-bag-shopping"></i> {{ t('product.add_to_cart') }}</button>
</div>

@if ($isApparel)
<div id="size-modal" class="modal" role="dialog" aria-modal="true" aria-label="{{ t('product.size_guide') }}">
    <div class="modal-box">
        <button type="button" class="btn btn-soft btn-icon btn-sm modal-close" data-modal-close aria-label="{{ t('common.close') }}"><i class="fa-solid fa-xmark"></i></button>
        <h2 style="font-size:22px;font-weight:800;margin-bottom:6px">{{ t('size_guide.title') }}</h2>
        <p class="muted" style="margin-bottom:14px">{{ t('size_guide.text') }}</p>
        @include('partials.size-chart')
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>window.PRODUCT = @json($productJs);</script>
@endpush
