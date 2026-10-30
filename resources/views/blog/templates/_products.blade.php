@if ($products->count())
    <section class="section">
        <div class="sec-head reveal"><div><span class="eyebrow">{{ t('blog.shop_eyebrow') }}</span><h2>{{ t('blog.related_products') }}</h2></div></div>
        <div class="product-grid">@foreach ($products as $product)@include('shop.partials.product-card', ['product' => $product])@endforeach</div>
    </section>
@endif
