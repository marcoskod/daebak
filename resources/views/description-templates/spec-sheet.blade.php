@php
    $specSec = collect($d['sections'])->first(fn ($s) => collect($s['blocks'])->contains('type', 'specs'));
    $others = collect($d['sections'])->reject(fn ($s) => $s === $specSec)->values()->all();
@endphp
<div class="pd-sheet">
    <div class="pd-sheet-main">
        @if ($d['intro'])<div class="pd-intro">@include('description-templates._blocks', ['blocks' => $d['intro']])</div>@endif
        @foreach ($others as $s)
            <section class="pd-section">
                <h3 class="pd-h">{{ $s['title'] }}</h3>
                @include('description-templates._blocks', ['blocks' => $s['blocks']])
            </section>
        @endforeach
    </div>
    @if ($specSec)
        <aside class="pd-sheet-side">
            <h3 class="pd-h">{!! \App\Support\ProductDescription::svg('ruler', 18) !!} {{ $specSec['title'] }}</h3>
            @include('description-templates._blocks', ['blocks' => $specSec['blocks']])
        </aside>
    @endif
</div>
