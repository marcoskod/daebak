        @if (count($parsed['sections']) > 1)
            <aside class="post-toc card{{ $tocClass ?? '' }}"><b>{{ t('blog.toc') }}</b>
                <nav aria-label="{{ t('blog.toc') }}">@foreach ($parsed['sections'] as $s)<a href="#{{ $s['anchor'] }}">{{ $s['title'] }}</a>@endforeach</nav></aside>
        @endif
