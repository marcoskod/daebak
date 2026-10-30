<article class="bcard reveal">
    <a href="{{ route('blog.show', $post->slug) }}" class="bcard-media" tabindex="-1" aria-hidden="true"><img src="{{ $post->cover_url }}" alt="" loading="lazy" width="600" height="315"></a>
    <div class="bcard-body">
        <div class="bcard-meta"><span class="pill pill-brand">{{ t('blog.categories.'.$post->category) }}</span><span>{{ t('blog.min_read', ['n' => $post->reading_time]) }}</span></div>
        <h3 class="bcard-title"><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h3>
        <p class="bcard-excerpt">{{ $post->excerpt }}</p>
        <div class="bcard-foot"><span class="avatar" style="width:28px;height:28px;font-size:12px">{{ mb_substr($post->author ?? 'D', 0, 1) }}</span><span>{{ $post->author }} · {{ $post->published_at?->locale(app()->getLocale())->isoFormat('LL') }}</span></div>
    </div>
</article>
