@php [$secs, $introSection] = \App\Support\ProductDescription::normalize($d); @endphp
@if ($d['has_sections'] && $d['intro'])
    <div class="pd-intro">@include('description-templates._blocks', ['blocks' => $d['intro']])</div>
@endif
<ol class="pd-timeline">
    @foreach ($secs as $i => $s)
        <li class="pd-step">
            <span class="pd-step-dot">{{ $i + 1 }}</span>
            <div class="pd-step-body">
                <h3 class="pd-h">{{ $s['title'] }}</h3>
                @include('description-templates._blocks', ['blocks' => $s['blocks']])
            </div>
        </li>
    @endforeach
</ol>
