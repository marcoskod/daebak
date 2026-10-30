@php
    $gallery = $product->images ?: [];
    $alt = $gallery[1] ?? null;               // 2ᵉ visuel au survol : le design « à plat » est en position suivante (-art)
    foreach ($gallery as $g) { if (str_contains($g, '-art.svg')) { $alt = $g; break; } }
    $needsOptions = count($product->sizes()) > 0 || count($product->colors()) > 1;
    $hasSale = (bool) $product->sale_price;
@endphp
<article class="pcard reveal">
    <div class="pcard-media">
        <div class="pcard-tags">
            @if ($hasSale)<span class="pill pill-danger">-{{ $product->discount_percent }}%</span>@endif
            @if ($product->badge && $product->badge !== 'sale')<span class="pill pill-dark">{{ t('product.badge.'.$product->badge) }}</span>@endif
        </div>
        <button type="button" class="pcard-heart" data-wish="{{ $product->id }}" aria-label="{{ t('product.add_wishlist') }}" aria-pressed="false"><i class="fa-regular fa-heart"></i></button>
        <img src="{{ $product->image_url }}" alt="{{ $product->localized_name }}" loading="lazy" width="460" height="460">
        @if ($alt)<img class="alt" src="{{ asset($alt) }}" alt="" loading="lazy" aria-hidden="true">@endif
        <div class="pcard-quick">
            <button type="button" class="btn btn-light btn-block btn-sm" data-add-to-cart data-product-id="{{ $product->id }}" data-needs-options="{{ $needsOptions ? 1 : 0 }}" data-url="{{ route('shop.produit', $product->slug) }}">
                <i class="fa-solid {{ $needsOptions ? 'fa-sliders' : 'fa-bag-shopping' }}"></i> {{ $needsOptions ? t('product.choose_options') : t('product.add_to_cart') }}
            </button>
        </div>
    </div>
    <div class="pcard-body">
        <div class="pcard-cat"><span>{{ $product->category?->localized_name }}</span>
            @if ($product->reviews_count)<span class="pcard-meta"><span class="stars"><i class="fa-solid fa-star"></i></span><b style="color:var(--ink)">{{ number_format($product->rating, 1) }}</b><span style="font-weight:500;letter-spacing:0;text-transform:none">({{ $product->reviews_count }})</span></span>@endif
        </div>
        <h3 class="pcard-title"><a href="{{ route('shop.produit', $product->slug) }}">{{ $product->localized_name }}</a></h3>
        <div class="pcard-meta">
            @if (count($product->colors()) > 1)
                <span class="swatches" aria-label="{{ t('product.colors') }}">@foreach (array_slice($product->colors(), 0, 4) as $c)<span class="swatch" style="background:{{ $c['hex'] }}"></span>@endforeach</span>
            @endif
            <span><i class="fa-solid fa-truck-fast" style="color:var(--leaf)"></i> {{ t('product.ships_in', ['a' => $product->production_min, 'b' => $product->production_max]) }}</span>
        </div>
        <div class="price">
            @if (count($product->sizes()) && ! empty($product->variants['size_prices']))<span class="from">{{ t('product.from') }}</span>@endif
            <strong>{{ money($product->current_price) }}</strong>
            @if ($hasSale)<s>{{ money($product->price) }}</s>@endif
        </div>
    </div>
</article>
