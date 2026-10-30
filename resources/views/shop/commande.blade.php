@extends('layouts.shop')

@section('title', t('checkout.title'))

@section('content')
<x-page-hero :title="t('checkout.title')" :subtitle="t('checkout.subtitle')" image="checkout" :crumbs="[['label' => t('cart.title'), 'url' => route('shop.panier')], ['label' => t('checkout.title')]]">
    <x-slot:side><span class="pill pill-ok"><i class="fa-solid fa-lock"></i> {{ t('checkout.secure') }}</span></x-slot:side>
</x-page-hero>

<div id="checkout-errors" class="alert alert-err" hidden role="alert"></div>

<form id="checkout-form" class="checkout-layout" action="{{ route('checkout.store') }}" method="POST" data-subtotal="{{ $subtotal }}" novalidate>
    @csrf
    <div class="stack" style="gap:18px;min-width:0">
        <section class="card stack">
            <h2 style="font-size:19px;font-weight:800"><i class="fa-solid fa-user" style="color:var(--brand)"></i> {{ t('checkout.contact') }}</h2>
            <div class="form-grid">
                <div class="field"><label for="customer_name">{{ t('checkout.name') }} *</label><input class="input" id="customer_name" name="customer_name" required autocomplete="name" value="{{ old('customer_name', auth()->user()->name ?? '') }}"></div>
                <div class="field"><label for="customer_email">{{ t('checkout.email') }} *</label><input class="input" type="email" id="customer_email" name="customer_email" required autocomplete="email" value="{{ old('customer_email', auth()->user()->email ?? '') }}"></div>
                <div class="field full"><label for="customer_phone">{{ t('checkout.phone') }}</label><input class="input" id="customer_phone" name="customer_phone" autocomplete="tel" placeholder="+1 555 000 0000"></div>
            </div>
        </section>

        <section class="card stack">
            <h2 style="font-size:19px;font-weight:800"><i class="fa-solid fa-location-dot" style="color:var(--brand)"></i> {{ t('checkout.shipping') }}</h2>
            <div class="form-grid">
                <div class="field full"><label for="ship-country">{{ t('checkout.country') }} *</label>
                    <select class="select" id="ship-country" name="shipping_country" required>
                        @foreach (site('shipping.countries') as $code)<option value="{{ $code }}" @selected($country === $code)>{{ t('countries.'.$code) }}</option>@endforeach
                    </select></div>
                <div class="field full"><label for="shipping_address">{{ t('checkout.address') }} *</label><input class="input" id="shipping_address" name="shipping_address" required autocomplete="street-address"></div>
                <div class="field"><label for="shipping_city">{{ t('checkout.city') }} *</label><input class="input" id="shipping_city" name="shipping_city" required autocomplete="address-level2"></div>
                <div class="field"><label for="shipping_postal_code">{{ t('checkout.postal') }}</label><input class="input" id="shipping_postal_code" name="shipping_postal_code" autocomplete="postal-code"></div>
                <div class="field full"><label for="notes">{{ t('checkout.notes') }}</label><textarea class="textarea" id="notes" name="notes" maxlength="500" style="min-height:80px"></textarea></div>
            </div>
        </section>

        <section class="card stack">
            <h2 style="font-size:19px;font-weight:800"><i class="fa-solid fa-credit-card" style="color:var(--brand)"></i> {{ t('checkout.payment') }}</h2>
            @forelse ($methods as $i => $m)
                <label class="radio-card"><input type="radio" name="payment_method" value="{{ $m }}" {{ $i === 0 ? 'checked' : '' }}>
                    <i class="{{ $m === 'card' ? 'fa-solid fa-credit-card' : 'fa-brands fa-paypal' }}" style="font-size:22px;color:var(--brand)"></i>
                    <span>{{ t('checkout.pay.'.$m) }}<small>{{ t('checkout.pay.'.$m.'_hint') }}</small></span></label>
            @empty
                <div class="alert alert-err">{{ t('checkout.no_payment') }}</div>
            @endforelse
            <div class="alert alert-info"><i class="fa-solid fa-flask"></i> {{ t('checkout.demo_notice') }}</div>
            <label class="check"><input type="checkbox" name="accept_terms" value="1" required><span>{!! t('checkout.accept', ['terms' => '<a href="'.route('pages.cgv').'" target="_blank" style="text-decoration:underline">'.e(t('footer.terms')).'</a>']) !!}</span></label>
        </section>
    </div>

    <aside class="card summary">
        <h2 style="font-size:20px;font-weight:800">{{ t('cart.summary') }}</h2>
        <div class="stack" style="gap:12px">
            @foreach ($items as $item)
                <div class="row" style="flex-wrap:nowrap;gap:12px">
                    <span style="position:relative"><img src="{{ $item['image'] }}" alt="" width="60" height="60" style="width:60px;height:60px;border-radius:14px;object-fit:cover;background:var(--soft)"><span class="badge-dot" style="border-color:var(--card)">{{ $item['qty'] }}</span></span>
                    <div style="flex:1;min-width:0"><b style="font-size:14px;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $item['product']->localized_name }}</b><small class="muted">{{ $item['options_label'] }}</small></div>
                    <b>{{ money($item['subtotal']) }}</b>
                </div>
            @endforeach
        </div>
        <div class="sum-row"><span>{{ t('cart.subtotal') }}</span><b style="color:var(--ink)">{{ money($subtotal) }}</b></div>
        <div class="sum-row"><span>{{ t('cart.shipping') }}</span><b id="sum-ship" style="color:var(--ink)">—</b></div>
        <div class="sum-row total"><span>{{ t('cart.total') }}</span><span id="sum-total">{{ money($subtotal) }}</span></div>
        @if (current_currency() !== 'USD')<p class="muted" style="font-size:12px">{{ t('checkout.charged_usd') }}</p>@endif
        <button id="pay-btn" type="submit" class="btn btn-primary btn-lg btn-block" @disabled(! count($methods))><i class="fa-solid fa-lock"></i> {{ t('checkout.pay_now') }}</button>
        <div class="trust" style="grid-template-columns:1fr"><div><i class="fa-solid fa-shield-halved"></i> {{ t('product.trust_secure') }}</div><div><i class="fa-solid fa-print"></i> {{ t('product.trust_pod') }}</div></div>
    </aside>
</form>
@endsection
