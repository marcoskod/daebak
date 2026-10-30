@extends('layouts.shop')

@section('title', t('home.meta_title'))
@section('description', t('brand.description'))

@push('styles')<link rel="stylesheet" href="{{ asset('css/categories.css') }}?v={{ @filemtime(public_path('css/categories.css')) }}">@endpush

@section('content')
{{-- HERO (hauteur élastique 340–400 px) --}}
<section class="hero reveal">
    <div class="hero-copy">
        <span class="eyebrow"><i class="fa-solid fa-sparkles"></i> {{ t('home.hero.eyebrow') }}</span>
        <h1>{!! t('home.hero.title') !!}</h1>
        <p class="hero-sub">{{ t('home.hero.text') }}</p>
        <form class="hero-search" action="{{ route('shop.catalogue') }}" method="GET" role="search">
            <input type="search" name="q" placeholder="{{ t('home.hero.search') }}" aria-label="{{ t('common.search') }}">
            <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i><span class="sr-only">{{ t('common.search') }}</span></button>
        </form>
        <div class="hero-tags">
            @foreach (['hangul', 'palace', 'seoul', 'kawaii'] as $k)
                <a class="chip" href="{{ route('shop.collection', $k) }}"><i class="{{ $collections[$k]['icon'] }}"></i> {{ t("collections.$k.title") }}</a>
            @endforeach
        </div>
    </div>
    <div class="hero-badge"><i class="fa-solid fa-leaf"></i><span>{{ t('home.hero.badge') }}</span></div>
    <img class="hero-art" src="{{ page_image('hero_art', 'images/brand/hero-collage.svg') }}" alt="" width="620" height="440">
</section>

{{-- CATÉGORIES (bandeau défilant) --}}
<section class="section reveal" aria-label="{{ t('home.categories.title') }}">
    <div class="cat-strip">
        @foreach ($categories as $c)
            <a class="cat-tab {{ $c->thumb_url ? 'has-thumb' : '' }}" href="{{ route('shop.catalogue', ['category' => $c->slug]) }}"><x-cat-thumb :category="$c" />{{ $c->localized_name }}<small>{{ t('shop.items_count', ['n' => $c->products_total]) }}</small></a>
        @endforeach
        <a class="cat-tab" href="{{ route('shop.catalogue', ['sale' => 1]) }}"><i class="fa-solid fa-tags" style="color:var(--accent)"></i>{{ t('home.categories.sale') }}<small>{{ t('home.categories.sale_hint') }}</small></a>
        <a class="cat-tab" href="{{ route('shop.catalogue', ['sort' => 'newest']) }}"><i class="fa-solid fa-wand-magic-sparkles"></i>{{ t('home.categories.new') }}<small>{{ t('home.categories.new_hint') }}</small></a>
    </div>
</section>

{{-- TENDANCES --}}
<section class="section">
    <div class="sec-head reveal">
        <div><span class="eyebrow">{{ t('home.trending.eyebrow') }}</span><h2>{{ t('home.trending.title') }}</h2><p>{{ t('home.trending.text') }}</p></div>
        <div class="rail-ctl">
            <button class="btn btn-ghost btn-icon btn-sm" data-rail-prev="rail-trending" aria-label="{{ t('common.previous') }}"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="btn btn-ghost btn-icon btn-sm" data-rail-next="rail-trending" aria-label="{{ t('common.next') }}"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
    </div>
    <div class="rail" id="rail-trending">
        @foreach ($bestsellers as $product)
            @include('shop.partials.product-card', ['product' => $product])
        @endforeach
    </div>
</section>

{{-- COLLECTIONS (bento) --}}
<section class="section">
    <div class="sec-head reveal">
        <div><span class="eyebrow">{{ t('home.collections.eyebrow') }}</span><h2>{{ t('home.collections.title') }}</h2><p>{{ t('home.collections.text') }}</p></div>
        <a href="{{ route('shop.collections') }}" class="link-arrow">{{ t('common.see_all') }} <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="bento">
        @foreach ($collections as $k => $col)
            <a class="tile tile-{{ $col['tone'] }} reveal" href="{{ route('shop.collection', $k) }}">
                <span class="pill pill-brand"><i class="{{ $col['icon'] }}"></i> {{ t("collections.$k.tag") }}</span>
                <h3>{{ t("collections.$k.title") }}</h3>
                <p>{{ t("collections.$k.text") }}</p>
                <img src="{{ asset($col['cover']) }}" alt="" loading="lazy" width="320" height="320">
            </a>
        @endforeach
    </div>
</section>

{{-- COMMENT ÇA MARCHE --}}
<section class="section">
    <div class="sec-head reveal"><div><span class="eyebrow">{{ t('home.how.eyebrow') }}</span><h2>{{ t('home.how.title') }}</h2><p>{{ t('home.how.text') }}</p></div><a href="{{ route('pages.how') }}" class="link-arrow">{{ t('home.how.link') }} <i class="fa-solid fa-arrow-right"></i></a></div>
    <div class="how-steps">
        @foreach ([['fa-solid fa-hand-pointer', 'pick'], ['fa-solid fa-palette', 'design'], ['fa-solid fa-print', 'print'], ['fa-solid fa-box-open', 'ship']] as [$icon, $k])
            <div class="how-step reveal"><span class="ico"><i class="{{ $icon }}"></i></span><h3>{{ t("home.how.$k.title") }}</h3><p class="muted">{{ t("home.how.$k.text") }}</p></div>
        @endforeach
    </div>
</section>

{{-- LOOKBOOK (photos libres de droit) --}}
<section class="section">
    <div class="sec-head reveal"><div><span class="eyebrow">{{ t('lookbook.eyebrow') }}</span><h2>{{ t('lookbook.title') }}</h2><p>{{ t('lookbook.text') }}</p></div>
        <div class="rail-ctl"><button class="btn btn-ghost btn-icon btn-sm" data-rail-prev="rail-look" aria-label="{{ t('common.previous') }}"><i class="fa-solid fa-chevron-left"></i></button><button class="btn btn-ghost btn-icon btn-sm" data-rail-next="rail-look" aria-label="{{ t('common.next') }}"><i class="fa-solid fa-chevron-right"></i></button></div></div>
    <div class="rail look-rail" id="rail-look">
        @foreach (site('lookbook') as $look)
            <a class="look reveal" href="{{ route($look['route'][0], $look['route'][1] ?? []) }}">
                <img src="https://images.unsplash.com/photo-{{ $look['photo'] }}?auto=format&fit=crop&w=640&h=800&q=75" alt="{{ t('lookbook.items.'.$look['key'].'.title') }}" loading="lazy" width="640" height="800" referrerpolicy="no-referrer" onerror="this.closest('.look').style.display='none'">
                <span class="look-cap"><b>{{ t('lookbook.items.'.$look['key'].'.title') }}</b><small>{{ t('lookbook.items.'.$look['key'].'.cta') }} <i class="fa-solid fa-arrow-right"></i></small></span>
            </a>
        @endforeach
    </div>
    <p class="muted" style="font-size:12px"><i class="fa-regular fa-image"></i> {{ t('lookbook.credit') }} <a href="https://unsplash.com/license" target="_blank" rel="noopener" style="text-decoration:underline">Unsplash License</a></p>
</section>

{{-- CHIFFRES --}}
<section class="card reveal">
    <div class="grid-4 keep" style="gap:24px">
        <div class="stat"><strong data-count="{{ $stats['products'] }}" data-suffix="+">0</strong><span>{{ t('home.stats.products') }}</span></div>
        <div class="stat"><strong data-count="40" data-suffix="+">0</strong><span>{{ t('home.stats.countries') }}</span></div>
        <div class="stat"><strong data-count="{{ $stats['reviews'] }}" data-suffix="+">0</strong><span>{{ t('home.stats.reviews') }}</span></div>
        <div class="stat"><strong data-count="5" data-suffix="d">0</strong><span>{{ t('home.stats.production') }}</span></div>
    </div>
</section>

{{-- NOUVEAUTÉS --}}
<section class="section">
    <div class="sec-head reveal"><div><span class="eyebrow">{{ t('home.new.eyebrow') }}</span><h2>{{ t('home.new.title') }}</h2></div><a href="{{ route('shop.catalogue', ['sort' => 'newest']) }}" class="link-arrow">{{ t('common.see_all') }} <i class="fa-solid fa-arrow-right"></i></a></div>
    <div class="product-grid">
        @foreach ($newest->take(8) as $product)
            @include('shop.partials.product-card', ['product' => $product])
        @endforeach
    </div>
</section>

{{-- STUDIO CTA --}}
<section class="band reveal">
    <div style="position:relative;z-index:1">
        <span class="eyebrow" style="color:var(--leaf-soft)">{{ t('home.studio.eyebrow') }}</span>
        <h2>{{ t('home.studio.title') }}</h2>
        <p>{{ t('home.studio.text') }}</p>
    </div>
    <div class="row" style="position:relative;z-index:1">
        <a href="{{ route('pages.studio') }}" class="btn btn-light btn-lg"><i class="fa-solid fa-wand-magic-sparkles"></i> {{ t('home.studio.cta') }}</a>
        <a href="{{ route('pages.creators') }}" class="btn btn-lg" style="background:rgba(255,255,255,.14);color:#fff">{{ t('home.studio.cta2') }}</a>
    </div>
</section>

{{-- AVIS --}}
<section class="section">
    <div class="sec-head reveal"><div><span class="eyebrow">{{ t('home.reviews.eyebrow') }}</span><h2>{{ t('home.reviews.title') }}</h2></div></div>
    <div class="grid-3">
        @foreach ($reviews->take(3) as $r)
            <figure class="quote-card reveal" style="margin:0">
                <div class="stars">@for ($i = 0; $i < $r->rating; $i++)<i class="fa-solid fa-star"></i>@endfor</div>
                <p>“{{ $r->body }}”</p>
                <figcaption class="row"><span class="avatar">{{ mb_substr($r->author, 0, 1) }}</span><span><b>{{ $r->author }}</b><br><small class="muted"><span class="fi fi-{{ strtolower($r->country) }} fis rounded"></span> {{ t('countries.'.$r->country) }} · {{ $r->product?->localized_name }}</small></span></figcaption>
            </figure>
        @endforeach
    </div>
</section>

{{-- JOURNAL --}}
@if ($posts->count())
<section class="section">
    <div class="sec-head reveal"><div><span class="eyebrow">{{ t('blog.home.eyebrow') }}</span><h2>{{ t('blog.home.title') }}</h2><p>{{ t('blog.home.text') }}</p></div><a href="{{ route('blog.index') }}" class="link-arrow">{{ t('blog.home.cta') }} <i class="fa-solid fa-arrow-right"></i></a></div>
    <div class="blog-grid">@foreach ($posts as $post)@include('blog.partials.post-card', ['post' => $post])@endforeach</div>
</section>
@endif

{{-- NEWSLETTER --}}
<section class="card-flat reveal" style="display:flex;gap:20px;align-items:center;justify-content:space-between;flex-wrap:wrap">
    <div style="max-width:46ch"><h2 style="font-size:24px;font-weight:800">{{ t('home.newsletter.title') }}</h2><p class="muted" style="margin-top:6px">{{ t('home.newsletter.text') }}</p></div>
    <form class="news-form" action="{{ route('newsletter.store') }}" method="POST" data-ajax data-reset>
        @csrf
        <input type="email" name="email" required placeholder="{{ t('home.newsletter.placeholder') }}" aria-label="{{ t('home.newsletter.placeholder') }}">
        <button class="btn btn-primary" type="submit">{{ t('home.newsletter.button') }}</button>
    </form>
</section>
@endsection
