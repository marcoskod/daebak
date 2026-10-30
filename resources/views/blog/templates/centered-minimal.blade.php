<article class="post post--minimal" itemscope itemtype="https://schema.org/Article">
@include('blog.templates._head', ['headClass' => ' post-head--center'])
    <meta itemprop="image" content="{{ $post->cover_url }}">
    @if (count($parsed['sections']) > 1)
        <details class="post-toc-inline card">
            <summary>{{ t('blog.toc') }}</summary>
            <nav aria-label="{{ t('blog.toc') }}">@foreach ($parsed['sections'] as $s)<a href="#{{ $s['anchor'] }}">{{ $s['title'] }}</a>@endforeach</nav>
        </details>
    @endif
    <div class="post-layout">
@include('blog.templates._body')
    </div>
</article>
