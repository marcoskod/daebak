@extends('layouts.shop')

@section('title', t('blog.title'))
@section('description', t('blog.subtitle'))

@section('content')
<x-page-hero :title="t('blog.title')" :subtitle="t('blog.subtitle')" image="about" :crumbs="[['label' => t('blog.nav')]]">
    <x-slot:side><span class="pill pill-brand">{{ t('blog.articles_count', ['n' => $total]) }}</span></x-slot:side>
</x-page-hero>

@if ($featured)
    <article class="bfeatured reveal">
        <a href="{{ route('blog.show', $featured->slug) }}" class="bfeatured-media" tabindex="-1" aria-hidden="true"><img src="{{ $featured->cover_url }}" alt="" width="900" height="473"></a>
        <div class="bfeatured-body">
            <div class="bcard-meta"><span class="pill pill-accent"><i class="fa-solid fa-star"></i> {{ t('blog.featured') }}</span><span class="pill pill-brand">{{ t('blog.categories.'.$featured->category) }}</span></div>
            <h2><a href="{{ route('blog.show', $featured->slug) }}">{{ $featured->title }}</a></h2>
            <p class="muted">{{ $featured->excerpt }}</p>
            <div class="row"><span class="muted" style="font-size:13px">{{ $featured->author }} · {{ $featured->published_at?->locale(app()->getLocale())->isoFormat('LL') }} · {{ t('blog.min_read', ['n' => $featured->reading_time]) }}</span></div>
            <a href="{{ route('blog.show', $featured->slug) }}" class="btn btn-primary" style="width:fit-content">{{ t('blog.read_more') }} <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </article>
@endif

<section class="section">
    <form action="{{ route('blog.index') }}" method="GET" class="card" style="padding:14px;display:grid;gap:12px;min-width:0" role="search">
        <div class="row">
            <label class="search-pill" style="flex:1;min-width:200px"><i class="fa-solid fa-magnifying-glass" style="color:var(--faint)"></i><input type="search" name="q" value="{{ $q }}" placeholder="{{ t('blog.search') }}" aria-label="{{ t('blog.search') }}"></label>
            @if ($category)<input type="hidden" name="category" value="{{ $category }}">@endif
            <button class="btn btn-primary" type="submit">{{ t('common.search') }}</button>
        </div>
        <div class="cat-strip" style="padding:0">
            <a href="{{ route('blog.index', array_filter(['q' => $q])) }}" class="chip {{ $category === '' ? 'is-active' : '' }}">{{ t('blog.all') }}</a>
            @foreach (\App\Models\BlogPost::CATEGORIES as $c)
                @if (($counts[$c] ?? 0) > 0)
                    <a href="{{ route('blog.index', array_filter(['category' => $c, 'q' => $q])) }}" class="chip {{ $category === $c ? 'is-active' : '' }}">{{ t('blog.categories.'.$c) }} <small style="opacity:.7">{{ $counts[$c] }}</small></a>
                @endif
            @endforeach
        </div>
    </form>

    @if ($posts->isEmpty())
        <div class="empty card"><i class="fa-regular fa-newspaper"></i><h3>{{ t('blog.no_posts') }}</h3><a href="{{ route('blog.index') }}" class="btn btn-soft">{{ t('shop.reset_filters') }}</a></div>
    @else
        <div class="blog-grid">@foreach ($posts as $post)@include('blog.partials.post-card', ['post' => $post])@endforeach</div>
        <div class="pagination">{{ $posts->links() }}</div>
    @endif
</section>

<section class="card-flat reveal" style="display:flex;gap:20px;align-items:center;justify-content:space-between;flex-wrap:wrap">
    <div style="max-width:46ch"><h2 style="font-size:24px;font-weight:800">{{ t('blog.newsletter_title') }}</h2><p class="muted" style="margin-top:6px">{{ t('blog.newsletter_text') }}</p></div>
    <form class="news-form" action="{{ route('newsletter.store') }}" method="POST" data-ajax data-reset>@csrf<input type="email" name="email" required placeholder="{{ t('home.newsletter.placeholder') }}" aria-label="{{ t('home.newsletter.placeholder') }}"><button class="btn btn-primary" type="submit">{{ t('home.newsletter.button') }}</button></form>
</section>
@endsection
