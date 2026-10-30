    <header class="post-head reveal{{ $headClass ?? '' }}">
        <div class="bcard-meta"><a href="{{ route('blog.index', ['category' => $post->category]) }}" class="pill pill-brand">{{ t('blog.categories.'.$post->category) }}</a><span>{{ t('blog.min_read', ['n' => $post->reading_time]) }}</span><span><i class="fa-regular fa-eye"></i> {{ number_format($post->views) }}</span></div>
        <h1 itemprop="headline">{{ $post->title }}</h1>
        <p class="post-lead">{{ $post->excerpt }}</p>
        <div class="row"><span class="avatar">{{ mb_substr($post->author ?? 'D', 0, 1) }}</span><span><b itemprop="author">{{ $post->author }}</b><br><time class="muted" style="font-size:13px" datetime="{{ $post->published_at?->toIso8601String() }}" itemprop="datePublished">{{ $date }}</time></span>
            <span class="spacer"></span>
            <button type="button" class="btn btn-ghost btn-sm" id="share-btn"><i class="fa-solid fa-arrow-up-from-bracket"></i> {{ t('blog.share') }}</button></div>
    </header>
