@if ($items->isEmpty())
    <div class="card empty">
        <i class="fa-solid fa-bag-shopping"></i>
        <h2>{{ t('cart.empty_title') }}</h2>
        <p class="muted">{{ t('cart.empty_text') }}</p>
        <a href="{{ route('shop.catalogue') }}" class="btn btn-primary btn-lg">{{ t('cart.continue') }}</a>
    </div>
@else
    <div class="cart-layout">
        <section class="card" style="padding-block:8px">
            @foreach ($items as $item)
                @php $p = $item['product']; @endphp
                <div class="cart-item" data-line="{{ $item['key'] }}">
                    <a href="{{ route('shop.produit', $p->slug) }}"><img src="{{ $item['image'] }}" alt="{{ $p->localized_name }}" loading="lazy" width="96" height="96"></a>
                    <div class="stack" style="gap:4px;min-width:0">
                        <h3><a href="{{ route('shop.produit', $p->slug) }}">{{ $p->localized_name }}</a></h3>
                        @if ($item['options_label'])<span class="opts">{{ $item['options_label'] }}</span>@endif
                        <span class="opts"><i class="fa-solid fa-print" style="color:var(--leaf)"></i> {{ t('product.ships_in', ['a' => $p->production_min, 'b' => $p->production_max]) }}</span>
                        <button type="button" class="opts" data-remove style="background:none;border:0;padding:0;text-align:left;color:var(--danger);font-weight:700;width:fit-content"><i class="fa-regular fa-trash-can"></i> {{ t('cart.remove') }}</button>
                    </div>
                    <div class="stack" style="justify-items:end;gap:8px">
                        <div class="qty"><button type="button" data-qty-step="-1" aria-label="−"><i class="fa-solid fa-minus"></i></button><input type="text" value="{{ $item['qty'] }}" readonly aria-label="{{ t('cart.quantity') }}"><button type="button" data-qty-step="1" aria-label="+"><i class="fa-solid fa-plus"></i></button></div>
                        <strong style="font-size:17px">{{ money($item['subtotal']) }}</strong>
                    </div>
                </div>
            @endforeach
        </section>

        <aside class="card summary">
            <h2 style="font-size:20px;font-weight:800">{{ t('cart.summary') }}</h2>
            @php $left = max(0, $threshold - $subtotal); @endphp
            <div class="free-ship">
                <span>@if ($left > 0)<i class="fa-solid fa-truck-fast" style="color:var(--leaf)"></i> {{ t('cart.free_left', ['amount' => money($left)]) }}@else<i class="fa-solid fa-circle-check" style="color:var(--ok)"></i> {{ t('cart.free_reached') }}@endif</span>
                <div class="progress"><span style="width:{{ min(100, $subtotal / max(1, $threshold) * 100) }}%"></span></div>
            </div>
            <div class="sum-row"><span>{{ t('cart.subtotal') }}</span><b style="color:var(--ink)">{{ money($subtotal) }}</b></div>
            <div class="sum-row"><span>{{ t('cart.shipping') }}</span><span>{{ t('cart.calculated_at_checkout') }}</span></div>
            <div class="sum-row total"><span>{{ t('cart.total') }}</span><span>{{ money($subtotal) }}</span></div>
            <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg btn-block"><i class="fa-solid fa-lock"></i> {{ t('cart.checkout') }}</a>
            <a href="{{ route('shop.catalogue') }}" class="btn btn-ghost btn-block">{{ t('cart.continue') }}</a>
            <div class="trust" style="grid-template-columns:1fr"><div><i class="fa-solid fa-shield-halved"></i> {{ t('product.trust_secure') }}</div><div><i class="fa-solid fa-rotate-left"></i> {{ t('product.trust_returns') }}</div></div>
        </aside>
    </div>
@endif
