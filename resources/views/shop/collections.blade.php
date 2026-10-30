@extends('layouts.shop')

@push('styles')<link rel="stylesheet" href="{{ asset('css/categories.css') }}?v={{ @filemtime(public_path('css/categories.css')) }}">@endpush

@section('title', t('collections.title'))
@section('description', t('collections.subtitle'))

@section('content')
<x-page-hero :title="t('collections.title')" :subtitle="t('collections.subtitle')" image="collections" :crumbs="[['label' => t('nav.collections')]]" />

<section class="section">
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

<section class="section">
    <div class="sec-head reveal"><div><h2>{{ t('collections.by_type') }}</h2><p>{{ t('collections.by_type_text') }}</p></div></div>
    <div class="grid-4 keep">
        @foreach ($categories as $c)
            <a href="{{ route('shop.catalogue', ['category' => $c->slug]) }}" class="card reveal" style="display:grid;gap:12px;padding:14px">
                @if ($c->thumb_url)<img src="{{ $c->thumb_url }}" alt="{{ $c->localized_name }}" width="320" height="320" loading="lazy" decoding="async" style="border-radius:var(--r);aspect-ratio:1;object-fit:cover;width:100%">@else<span class="cat-card-ph" aria-hidden="true"><i class="{{ $c->icon }}"></i></span>@endif
                <div style="min-width:0"><strong class="truncate" style="font-size:16px;display:block"><i class="{{ $c->icon }}" style="color:var(--brand)"></i> {{ $c->localized_name }}</strong><p class="muted" style="font-size:13px;margin-top:2px">{{ t('shop.items_count', ['n' => $c->products_total]) }}</p></div>
            </a>
        @endforeach
    </div>
</section>
@endsection
