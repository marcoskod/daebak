@if (empty($skipProducts))
    @include('blog.templates._products')
@endif

<nav class="post-nav" aria-label="{{ t('blog.nav') }}">
    @if ($prev)<a class="card" href="{{ route('blog.show', $prev->slug) }}"><small class="muted"><i class="fa-solid fa-arrow-left"></i> {{ t('blog.prev') }}</small><b>{{ $prev->title }}</b></a>@else<span></span>@endif
    @if ($next)<a class="card" href="{{ route('blog.show', $next->slug) }}" style="text-align:right"><small class="muted">{{ t('blog.next') }} <i class="fa-solid fa-arrow-right"></i></small><b>{{ $next->title }}</b></a>@endif
</nav>

@if ($related->count())
    <section class="section">
        <div class="sec-head reveal"><h2>{{ t('blog.related_posts') }}</h2></div>
        <div class="blog-grid">@foreach ($related as $p)@include('blog.partials.post-card', ['post' => $p])@endforeach</div>
    </section>
@endif
