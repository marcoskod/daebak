@props(['title', 'subtitle' => null, 'image' => 'catalogue', 'crumbs' => [], 'tag' => 'h1'])
<section class="page-hero" style="background-image:url('{{ page_image('hero', 'images/heroes/'.$image.'.svg') }}')">
    <div class="page-hero-in">
        <div style="min-width:0">
            @if (count($crumbs))
                <nav class="crumbs" aria-label="{{ t('common.breadcrumb') }}">
                    <a href="{{ route('shop.home') }}">{{ t('nav.home') }}</a>
                    @foreach ($crumbs as $c)
                        <i class="fa-solid fa-chevron-right"></i>
                        @if (! empty($c['url']))<a href="{{ $c['url'] }}">{{ $c['label'] }}</a>@else<span style="color:var(--ink)">{{ $c['label'] }}</span>@endif
                    @endforeach
                </nav>
            @endif
            <{{ $tag }}>{{ $title }}</{{ $tag }}>
            @if ($subtitle)<p>{{ $subtitle }}</p>@endif
        </div>
        @isset($side)<div class="page-hero-side">{{ $side }}</div>@endisset
    </div>
</section>
