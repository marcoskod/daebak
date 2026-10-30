@extends('layouts.shop')

@section('title', t('how.title'))
@section('description', t('how.subtitle'))

@section('content')
<x-page-hero :title="t('how.title')" :subtitle="t('how.subtitle')" image="how" :crumbs="[['label' => t('nav.how')]]" />

<section class="section">
    <div class="how-steps">
        @foreach (tg('how.steps') as $i => $step)
            <div class="how-step reveal"><span class="ico"><i class="{{ ['fa-solid fa-hand-pointer', 'fa-solid fa-credit-card', 'fa-solid fa-print', 'fa-solid fa-box-open'][$i] ?? 'fa-solid fa-star' }}"></i></span><h3>{{ $step['title'] }}</h3><p class="muted">{{ $step['text'] }}</p></div>
        @endforeach
    </div>
</section>

<section class="section">
    <div class="sec-head reveal"><div><h2>{{ t('how.partners_title') }}</h2><p>{{ t('how.partners_text') }}</p></div></div>
    <div class="grid-4">
        @foreach (site('providers') as $key => $p)
            <div class="card reveal stack">
                <span style="width:52px;height:52px;border-radius:18px;display:grid;place-items:center;background:color-mix(in srgb,{{ $p['color'] }} 14%,transparent);color:{{ $p['color'] }};font-size:22px"><i class="{{ $p['icon'] }}"></i></span>
                <h3 style="font-size:18px">{{ $p['name'] }}</h3>
                <p class="muted" style="font-size:14px">{{ t("how.providers.$key") }}</p>
            </div>
        @endforeach
    </div>
</section>

<section class="card reveal stack">
    <h2 style="font-size:22px;font-weight:800">{{ t('how.table.title') }}</h2>
    <div class="table-wrap"><table class="table">
        <thead><tr><th>{{ t('how.table.product') }}</th><th>{{ t('how.table.production') }}</th><th>{{ t('how.table.method') }}</th></tr></thead>
        <tbody>@foreach (tg('how.table.rows') as $row)<tr><td><b>{{ $row['product'] }}</b></td><td>{{ $row['days'] }}</td><td>{{ $row['method'] }}</td></tr>@endforeach</tbody>
    </table></div>
    <p class="muted" style="font-size:13px">{{ t('how.table.note') }}</p>
</section>

<section class="section">
    <div class="sec-head reveal"><div><h2>{{ t('how.why_title') }}</h2></div></div>
    <div class="grid-3">
        @foreach (tg('how.why') as $i => $w)
            <div class="card-flat reveal stack"><i class="{{ ['fa-solid fa-leaf', 'fa-solid fa-earth-asia', 'fa-solid fa-hand-holding-heart'][$i] ?? 'fa-solid fa-star' }}" style="font-size:24px;color:var(--leaf)"></i><h3>{{ $w['title'] }}</h3><p class="muted">{{ $w['text'] }}</p></div>
        @endforeach
    </div>
</section>

<section class="band reveal"><div style="position:relative;z-index:1"><h2>{{ t('how.cta_title') }}</h2><p>{{ t('how.cta_text') }}</p></div><a href="{{ route('shop.catalogue') }}" class="btn btn-light btn-lg" style="position:relative;z-index:1">{{ t('home.hero.cta') }}</a></section>
@endsection
