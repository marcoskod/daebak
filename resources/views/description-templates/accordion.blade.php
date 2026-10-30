@php [$secs, $introSection] = \App\Support\ProductDescription::normalize($d); @endphp
@php $items = $introSection ? array_merge([$introSection], $secs) : $secs; @endphp
<div class="pd-acc">
    @foreach ($items as $i => $s)
        <details class="pd-acc-item" {{ $i === 0 ? 'open' : '' }}>
            <summary><span class="pd-acc-ico">{!! \App\Support\ProductDescription::svg($s['icon'], 18) !!}</span><span class="pd-acc-title">{{ $s['title'] }}</span><span class="pd-acc-chev">{!! \App\Support\ProductDescription::svg('chevron', 18) !!}</span></summary>
            <div class="pd-acc-body">@include('description-templates._blocks', ['blocks' => $s['blocks']])</div>
        </details>
    @endforeach
</div>
