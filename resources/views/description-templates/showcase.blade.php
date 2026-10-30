@php [$secs, $introSection] = \App\Support\ProductDescription::normalize($d); @endphp
@php $lead = \App\Support\ProductDescription::lead($d); $rest = collect($d['intro'])->skip(1)->values()->all(); @endphp
@if ($lead)
    <div class="pd-hero"><span class="pd-hero-ico">{!! \App\Support\ProductDescription::svg('sparkles', 26) !!}</span><p>{!! $lead !!}</p></div>
@endif
@if ($rest)<div class="pd-intro">@include('description-templates._blocks', ['blocks' => $rest])</div>@endif
<div class="pd-bento">
    @foreach ($secs as $i => $s)
        @php $wide = collect($s['blocks'])->contains(fn ($b) => in_array($b['type'], ['specs', 'quote'])) || count($s['blocks']) > 2; @endphp
        <article class="pd-tile {{ $wide ? 'pd-tile--wide' : '' }} pd-tone-{{ $i % 4 }}">
            <div class="pd-tile-head">{!! \App\Support\ProductDescription::svg($s['icon'], 20) !!}<h3 class="pd-h">{{ $s['title'] }}</h3></div>
            @include('description-templates._blocks', ['blocks' => $s['blocks']])
        </article>
    @endforeach
</div>
