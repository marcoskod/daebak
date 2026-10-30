@extends('layouts.shop')

@push('styles')<link rel="stylesheet" href="{{ asset('css/categories.css') }}?v={{ @filemtime(public_path('css/categories.css')) }}">@endpush

@php
    $title = $collectionKey ? t("collections.$collectionKey.title") : ($activeCategory?->localized_name ?? t('shop.title'));
    $subtitle = $collectionKey ? t("collections.$collectionKey.text") : ($activeCategory?->localized_description ?: t('shop.subtitle'));
    $crumbs = [['label' => t('nav.shop'), 'url' => $collectionKey || $activeCategory ? route('shop.catalogue') : null]];
    if ($collectionKey) { $crumbs = [['label' => t('nav.collections'), 'url' => route('shop.collections')], ['label' => $title]]; }
    elseif ($activeCategory) { $crumbs[] = ['label' => $title]; }
    $activeParent = $categories->firstWhere('slug', $activeParentSlug);
@endphp

@section('title', $title)
@section('description', $subtitle)

@section('content')
<x-page-hero :title="$title" :subtitle="$subtitle" image="catalogue" :crumbs="$crumbs">
    <x-slot:side><span class="pill pill-brand"><span id="result-count">{{ $products->total() }}</span>&nbsp;{{ t('shop.results') }}</span></x-slot:side>
</x-page-hero>

<section id="catalogue" class="section">
    <form id="filters" class="card" action="{{ route('shop.catalogue') }}" method="GET" style="padding:14px;display:grid;gap:12px">
        <input type="hidden" name="category" value="{{ $activeSlug }}">
        <input type="hidden" name="collection" value="{{ $collectionKey }}">
        <input type="hidden" name="sale" value="{{ request('sale') }}">
        <div class="row" style="flex-wrap:wrap">
            <label class="search-pill" style="flex:1;min-width:220px;position:relative">
                <i class="fa-solid fa-magnifying-glass" style="color:var(--faint)"></i>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ t('common.search_placeholder') }}" aria-label="{{ t('common.search') }}">
            </label>
            <label class="sr-only" for="sort">{{ t('shop.sort') }}</label>
            <select id="sort" name="sort" class="select" style="flex:1 1 150px;min-width:0;width:auto;height:44px;border-radius:999px">
                @foreach (['popular', 'newest', 'rating', 'price_asc', 'price_desc'] as $s)
                    <option value="{{ $s }}" @selected($sort === $s)>{{ t("shop.sort_$s") }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-sliders"></i> {{ t('shop.apply') }}</button>
        </div>
        <div class="cat-strip" style="padding:0">
            <a href="{{ route('shop.catalogue') }}" class="chip {{ $activeSlug === '' ? 'is-active' : '' }}" data-cat="">{{ t('shop.all') }}</a>
            @foreach ($categories as $c)
                <a href="{{ route('shop.catalogue', ['category' => $c->slug]) }}" class="chip {{ $activeParentSlug === $c->slug ? 'is-active' : '' }}" data-cat="{{ $c->slug }}">@if ($c->thumb_url)<img class="chip-thumb" src="{{ $c->thumb_url }}" alt="" width="22" height="22" loading="lazy" decoding="async">@else<i class="{{ $c->icon }}"></i>@endif <span class="truncate">{{ $c->localized_name }}</span></a>
            @endforeach
        </div>
        @if ($activeParent && $activeParent->children->count())
            <div class="cat-strip" style="padding:0">
                @foreach ($activeParent->children as $child)
                    <a href="{{ route('shop.catalogue', ['category' => $child->slug]) }}" class="chip {{ $activeSlug === $child->slug ? 'is-active' : '' }}" data-cat="{{ $child->slug }}" style="height:30px;font-size:12px">@if ($child->thumb_url)<img class="chip-thumb chip-thumb-sm" src="{{ $child->thumb_url }}" alt="" width="18" height="18" loading="lazy" decoding="async">@endif <span class="truncate">{{ $child->localized_name }}</span></a>
                @endforeach
            </div>
        @endif
    </form>

    <div id="product-grid" class="product-grid">
        @include('shop.partials.product-grid', ['products' => $products])
    </div>
    <div id="pagination" class="pagination">{{ $products->links() }}</div>
</section>
@endsection
