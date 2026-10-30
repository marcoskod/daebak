@php $lead = \App\Support\ProductDescription::lead($d); $rest = collect($d['intro'])->skip(1)->values()->all(); @endphp
@if ($lead)
    <p class="pd-lead pd-dropcap">{!! $lead !!}</p>
@endif
@if ($rest)<div class="pd-cols">@include('description-templates._blocks', ['blocks' => $rest])</div>@endif
@foreach ($d['sections'] as $i => $s)
    <section class="pd-section pd-ed">
        <div class="pd-ed-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="pd-ed-body">
            <h3 class="pd-h">{{ $s['title'] }}</h3>
            <div class="pd-cols">@include('description-templates._blocks', ['blocks' => $s['blocks']])</div>
        </div>
    </section>
@endforeach
