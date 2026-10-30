@foreach ($blocks as $b)
    @if ($b['type'] === 'p')
        <p class="pd-p">{!! $b['html'] !!}</p>
    @elseif ($b['type'] === 'ul')
        <ul class="pd-list">@foreach ($b['items'] as $item)<li><span class="pd-tick">{!! \App\Support\ProductDescription::svg('check', 14) !!}</span><span>{!! $item !!}</span></li>@endforeach</ul>
    @elseif ($b['type'] === 'specs')
        <dl class="pd-specs">@foreach ($b['rows'] as $row)<div><dt>{!! $row[0] !!}</dt><dd>{!! $row[1] !!}</dd></div>@endforeach</dl>
    @elseif ($b['type'] === 'quote')
        <blockquote class="pd-quote">{!! $b['html'] !!}</blockquote>
    @endif
@endforeach
