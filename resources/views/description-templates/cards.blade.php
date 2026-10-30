@php [$secs, $introSection] = \App\Support\ProductDescription::normalize($d); @endphp
@if ($d['has_sections'] && $d['intro'])
    <div class="pd-intro">@include('description-templates._blocks', ['blocks' => $d['intro']])</div>
@endif
<div class="pd-grid">
    @foreach ($secs as $s)
        <article class="pd-card">
            <div class="pd-card-ico">{!! \App\Support\ProductDescription::svg($s['icon'], 22) !!}</div>
            <h3 class="pd-h">{{ $s['title'] }}</h3>
            @include('description-templates._blocks', ['blocks' => $s['blocks']])
        </article>
    @endforeach
</div>
