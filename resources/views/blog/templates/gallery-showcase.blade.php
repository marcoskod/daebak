<article class="post post--showcase" itemscope itemtype="https://schema.org/Article">
@include('blog.templates._cover', ['coverClass' => ' post-cover--tall'])
@include('blog.templates._head', ['headClass' => ' post-head--center'])
    @if ($products->count())
        <section class="post-showcase-products" aria-labelledby="showcase-h">
            <div class="sec-head"><div><span class="eyebrow">{{ t('blog.shop_eyebrow') }}</span><h2 id="showcase-h">{{ t('blog.related_products') }}</h2></div></div>
            <div class="product-grid">@foreach ($products as $product)@include('shop.partials.product-card', ['product' => $product])@endforeach</div>
        </section>
    @endif
    <div class="post-layout">
@include('blog.templates._toc')
@include('blog.templates._body')
    </div>
</article>
