@php [$secs, $introSection] = \App\Support\ProductDescription::normalize($d); @endphp
@php $tabs = $introSection ? array_merge([$introSection], $secs) : $secs; @endphp
<div class="pd-tabs" data-pd-tabs>
    <div class="pd-tablist" role="tablist">
        @foreach ($tabs as $i => $s)
            <button type="button" role="tab" class="pd-tab {{ $i === 0 ? 'is-active' : '' }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}" data-pd-tab="{{ $i }}">{!! \App\Support\ProductDescription::svg($s['icon'], 16) !!}<span>{{ $s['title'] }}</span></button>
        @endforeach
    </div>
    @foreach ($tabs as $i => $s)
        <div class="pd-panel {{ $i === 0 ? 'is-active' : '' }}" role="tabpanel" data-pd-panel="{{ $i }}">
            @include('description-templates._blocks', ['blocks' => $s['blocks']])
        </div>
    @endforeach
</div>
