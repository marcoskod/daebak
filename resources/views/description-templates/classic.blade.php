@if ($d['intro'])
    <div class="pd-intro">@include('description-templates._blocks', ['blocks' => $d['intro']])</div>
@endif
@foreach ($d['sections'] as $s)
    <section class="pd-section">
        <h3 class="pd-h">{{ $s['title'] }}</h3>
        @include('description-templates._blocks', ['blocks' => $s['blocks']])
    </section>
@endforeach
