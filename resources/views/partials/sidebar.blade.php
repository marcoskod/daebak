@php
    $locales = site('locales');
    $cur = current_currency();
    $curs = array_keys(site('currency.rates'));
@endphp
<aside id="sidebar" class="sidebar" aria-label="{{ t('common.main_navigation') }}">
    <div class="sidebar-head">
        <a href="{{ route('shop.home') }}" class="brand">
            <img src="{{ asset(site('brand.logo_mark')) }}" alt="" width="38" height="38">
            <span>{{ site('brand.name') }}<small>{{ site('brand.native_name') }}</small></span>
        </a>
        <button type="button" class="btn btn-soft btn-icon btn-sm drawer-close" data-drawer-close aria-label="{{ t('common.close_menu') }}"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="sidebar-scroll">
        <form action="{{ route('shop.catalogue') }}" method="GET" class="search-pill" role="search">
            <i class="fa-solid fa-magnifying-glass" style="color:var(--faint)"></i>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ t('common.search_placeholder') }}" aria-label="{{ t('common.search') }}" autocomplete="off" data-search>
            <div class="search-pop" role="listbox"></div>
        </form>

        <a href="{{ route('shop.panier') }}" class="btn btn-primary btn-block" style="justify-content:space-between">
            <span><i class="fa-solid fa-bag-shopping"></i>&nbsp; {{ t('nav.cart') }}</span>
            <span class="pill" style="background:rgba(255,255,255,.2);color:inherit" data-cart-count>{{ \App\Support\Cart::count() }}</span>
        </a>

        <nav class="nav-list" aria-label="{{ t('common.main_navigation') }}">
            @foreach (site('nav') as $item)
                @php($active = collect(explode(',', $item['match']))->contains(fn ($p) => request()->routeIs(trim($p))))
                <a href="{{ route($item['route']) }}" class="nav-link {{ $active ? 'is-active' : '' }}" @if($active) aria-current="page" @endif>
                    <i class="{{ $item['icon'] }}"></i><span>{{ t($item['label'] ?? 'nav.'.$item['key']) }}</span>
                </a>
            @endforeach
            <a href="{{ route('shop.wishlist') }}" class="nav-link {{ request()->routeIs('shop.wishlist') ? 'is-active' : '' }}">
                <i class="fa-regular fa-heart"></i><span>{{ t('nav.wishlist') }}</span><span class="pill pill-brand" style="margin-left:auto;display:none" data-wish-count>0</span>
            </a>
        </nav>

        <div class="side-cta">
            <strong>{{ t('nav.cta_title') }}</strong>
            <p>{{ t('nav.cta_text') }}</p>
            <a href="{{ route('pages.studio') }}" class="btn btn-light btn-sm btn-block"><i class="fa-solid fa-wand-magic-sparkles"></i> {{ t('nav.cta_button') }}</a>
        </div>
    </div>

    <div class="side-foot">
        <div class="side-tools">
            <div class="dd">
                <button type="button" class="dd-btn" data-dd-toggle aria-haspopup="true" aria-expanded="false" aria-label="{{ t('common.language') }}">
                    <span class="row" style="gap:8px;flex-wrap:nowrap"><span class="fi fi-{{ $locales[app()->getLocale()]['flag'] }} fis rounded"></span>{{ strtoupper(app()->getLocale()) }}</span>
                    <i class="fa-solid fa-chevron-up" style="font-size:10px"></i>
                </button>
                <div class="dd-menu" role="menu">
                    @foreach ($locales as $code => $info)
                        <a href="{{ route('lang.switch', $code) }}" class="{{ app()->getLocale() === $code ? 'is-current' : '' }}" role="menuitem" hreflang="{{ $code }}"><span class="fi fi-{{ $info['flag'] }} fis rounded"></span>{{ $info['label'] }}</a>
                    @endforeach
                </div>
            </div>
            <div class="dd">
                <button type="button" class="dd-btn" data-dd-toggle aria-haspopup="true" aria-expanded="false" aria-label="{{ t('common.currency') }}">
                    <span>{{ site('currency.symbols.'.$cur) }} {{ $cur }}</span><i class="fa-solid fa-chevron-up" style="font-size:10px"></i>
                </button>
                <div class="dd-menu" role="menu" style="min-width:150px;left:auto;right:0">
                    @foreach ($curs as $c)
                        <a href="{{ route('currency.switch', $c) }}" class="{{ $cur === $c ? 'is-current' : '' }}" role="menuitem">{{ site('currency.symbols.'.$c) }} {{ $c }}</a>
                    @endforeach
                </div>
            </div>
            <button type="button" class="theme-btn" data-theme-toggle aria-label="{{ t('common.toggle_theme') }}" style="flex:0 0 44px"><i class="fa-solid fa-moon"></i><i class="fa-solid fa-sun"></i></button>
        </div>

        @auth
            <a href="{{ route('dashboard') }}" class="side-user"><i class="fa-solid fa-user"></i><span>{{ auth()->user()->name }}</span><i class="fa-solid fa-chevron-right" style="width:auto;background:none;font-size:11px"></i></a>
        @else
            <div class="row" style="gap:8px;flex-wrap:nowrap">
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm" style="flex:1"><i class="fa-regular fa-user"></i> {{ t('nav.login') }}</a>
                <a href="{{ route('register') }}" class="btn btn-soft btn-sm" style="flex:1">{{ t('nav.register') }}</a>
            </div>
        @endauth
    </div>
</aside>
