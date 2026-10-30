@forelse ($products as $product)
    @include('shop.partials.product-card', ['product' => $product])
@empty
    <div class="empty" style="grid-column:1/-1">
        <i class="fa-regular fa-face-frown-open"></i>
        <h3>{{ t('shop.no_results_title') }}</h3>
        <p class="muted">{{ t('shop.no_results_text') }}</p>
        <a href="{{ route('shop.catalogue') }}" class="btn btn-soft">{{ t('shop.reset_filters') }}</a>
    </div>
@endforelse
