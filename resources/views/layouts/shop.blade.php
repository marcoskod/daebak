@php
    $brand = site('brand.name');
    $locales = site('locales');
    $cur = current_currency();
    $jsI18n = [];
    foreach (['js', 'chat'] as $grp) {
        foreach (tg($grp) as $k => $v) { if (is_string($v) && $k !== 'system_prompt') { $jsI18n["$grp.$k"] = $v; } }
    }
    $shipCfg = ['free' => site('shipping.free_threshold'), 'zones' => site('shipping.zones')];
    $colors = site('colors');
    $pageKey = \App\Support\PageEditor::currentKey();
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@hasSection('title')@yield('title') · @endif{{ $brand }} — {{ t('brand.tagline') }}</title>
    <meta name="description" content="@yield('description', t('brand.description'))">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="{{ $colors['primary'] }}">
    <meta property="og:title" content="@yield('title', $brand)">
    <meta property="og:description" content="@yield('description', t('brand.description'))">
    <meta property="og:image" content="@yield('og_image', asset(site('brand.logo')))">
    <link rel="icon" type="image/svg+xml" href="{{ asset(site('brand.favicon')) }}">
    <script>(function(){try{var m=localStorage.getItem('daebak-theme');if(!m){m='light';}document.documentElement.setAttribute('data-theme',m);}catch(e){}})();</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700;900&family=Urbanist:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icons/7.2.3/css/flag-icons.min.css" crossorigin="anonymous">
    <style>:root:not([data-theme="dark"]){--brand:{{ $colors['primary'] }};--brand-solid:{{ $colors['primary'] }};--brand-2:{{ $colors['primary_container'] }};--blush:{{ $colors['secondary_container'] }};--blush-ink:{{ $colors['secondary'] }};--leaf:{{ $colors['tertiary'] }};--accent:{{ $colors['accent'] }};--bg:{{ $colors['surface'] }}}</style>
    <link rel="stylesheet" href="{{ asset('css/daebak.css') }}?v={{ @filemtime(public_path('css/daebak.css')) }}">
    @stack('styles')
    @if ($pageCss = \App\Support\PageEditor::css($pageKey))<style id="page-overrides">{!! $pageCss !!}</style>@endif
</head>
<body data-page="{{ $pageKey }}">
<a href="#content" class="skip-link">{{ t('common.skip') }}</a>
<div id="top-progress" aria-hidden="true"></div>
<div id="toast-root" aria-live="polite"></div>

<div class="shell">
    @include('partials.sidebar')

    <div class="main">
        <header class="topbar">
            <button type="button" class="btn btn-soft btn-icon btn-sm menu-btn" data-drawer-open aria-label="{{ t('common.open_menu') }}" aria-controls="sidebar"><i class="fa-solid fa-bars-staggered"></i></button>
            <a href="{{ route('shop.home') }}" class="brand"><img src="{{ asset(site('brand.logo_mark')) }}" alt="" width="32" height="32"> {{ $brand }}</a>
            <button type="button" class="btn btn-soft btn-icon btn-sm" data-theme-toggle aria-label="{{ t('common.toggle_theme') }}"><i class="fa-solid fa-moon"></i><i class="fa-solid fa-sun"></i></button>
            <a href="{{ route('shop.wishlist') }}" class="btn btn-soft btn-icon btn-sm" aria-label="{{ t('nav.wishlist') }}"><i class="fa-regular fa-heart"></i><span class="badge-dot" data-wish-count style="display:none">0</span></a>
            <a href="{{ route('shop.panier') }}" class="btn btn-primary btn-icon btn-sm" aria-label="{{ t('nav.cart') }}"><i class="fa-solid fa-bag-shopping"></i><span class="badge-dot" data-cart-count style="{{ \App\Support\Cart::count() ? '' : 'display:none' }}">{{ \App\Support\Cart::count() }}</span></a>
        </header>

        <main id="content" class="main" style="gap:var(--gap)">
            @yield('content')
        </main>

        @include('partials.footer')
    </div>
</div>
<div id="drawer-backdrop" class="drawer-backdrop" aria-hidden="true"></div>

@if (site('chat.enabled'))
    @include('partials.chat')
@endif

<script>
    window.DAEBAK = {
        locale: @json(app()->getLocale()), currency: @json($cur), csrf: @json(csrf_token()),
        rates: @json(site('currency.rates')), symbols: @json(site('currency.symbols')), decimals: @json(site('currency.decimals')),
        shipping: @json($shipCfg), chatToggleMs: {{ (int) site('chat.idle_toggle_seconds', 5) * 1000 }},
        i18n: @json($jsI18n),
        routes: {
            cartAdd: @json(route('cart.add')), cartUpdate: @json(route('cart.update')), checkout: @json(route('checkout.index')),
            filter: @json(route('shop.catalogue.filter')), suggest: @json(route('shop.suggest')), wishlist: @json(route('shop.wishlist.items')),
            chatMessage: @json(route('chat.message')), chatHistory: @json(route('chat.history')), chatHandoff: @json(route('chat.handoff')), chatPoll: @json(route('chat.poll'))
        }
    };
</script>
@stack('scripts')
<script src="{{ asset('js/daebak.js') }}?v={{ @filemtime(public_path('js/daebak.js')) }}"></script>
@if (session('success') || session('error') || session('status'))
    <script>@if (session('success') || session('status'))Daebak.toast(@json(session('success') ?? session('status')), 'success');@endif @if (session('error'))Daebak.toast(@json(session('error')), 'error');@endif</script>
@endif
</body>
</html>
