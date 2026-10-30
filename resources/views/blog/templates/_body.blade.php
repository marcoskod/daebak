        <div class="post-body{{ $bodyClass ?? '' }}" itemprop="articleBody">
            @include('description-templates._blocks', ['blocks' => $parsed['intro']])
            @foreach ($parsed['sections'] as $s)
                @if (! empty($wrapSections))
                    <section class="post-sec" aria-labelledby="{{ $s['anchor'] }}">
                        <span class="post-sec-n" aria-hidden="true">{{ str_pad((string) ($loop->iteration), 2, '0', STR_PAD_LEFT) }}</span>
                        <div class="post-sec-in">
                            <h2 id="{{ $s['anchor'] }}">{{ $s['title'] }}</h2>
                            @include('description-templates._blocks', ['blocks' => $s['blocks']])
                        </div>
                    </section>
                @else
                    <h2 id="{{ $s['anchor'] }}">{{ $s['title'] }}</h2>
                    @include('description-templates._blocks', ['blocks' => $s['blocks']])
                @endif
            @endforeach
            @if ($post->tags)<div class="row" style="margin-top:22px">@foreach ($post->tags as $tag)<a class="chip" href="{{ route('blog.index', ['q' => $tag]) }}">#{{ $tag }}</a>@endforeach</div>@endif
        </div>
