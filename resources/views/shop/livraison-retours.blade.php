@extends('layouts.shop')

@section('title', t('shipping.title'))
@section('description', t('shipping.subtitle'))

@section('content')
<x-page-hero :title="t('shipping.title')" :subtitle="t('shipping.subtitle')" image="shipping" :crumbs="[['label' => t('footer.shipping')]]" />

<section class="card stack reveal">
    <h2 style="font-size:22px;font-weight:800">{{ t('shipping.table_title') }}</h2>
    <div class="table-wrap"><table class="table">
        <thead><tr><th>{{ t('checkout.country') }}</th><th>{{ t('shipping.transit') }}</th><th>{{ t('shipping.cost') }}</th><th>{{ t('shipping.free_from') }}</th></tr></thead>
        <tbody>
            @foreach ($countries as $c)
                @php($d = $days[$c] ?? $days['*'])
                <tr><td><span class="fi fi-{{ strtolower($c === 'GB' ? 'gb' : $c) }} fis rounded"></span> &nbsp;<b>{{ t('countries.'.$c) }}</b></td><td>{{ t('js.days', ['a' => $d[0], 'b' => $d[1]]) }}</td><td>{{ money($zones[$c] ?? $zones['*']) }}</td><td>{{ money(site('shipping.free_threshold')) }}</td></tr>
            @endforeach
        </tbody>
    </table></div>
    <p class="muted" style="font-size:13px">{{ t('shipping.note') }}</p>
</section>

<div class="grid-2">
    @foreach (tg('shipping.sections') as $i => $s)
        <section class="card reveal stack"><h3 style="font-size:18px"><i class="{{ ['fa-solid fa-print', 'fa-solid fa-rotate-left', 'fa-solid fa-triangle-exclamation', 'fa-solid fa-file-invoice-dollar'][$i] ?? 'fa-solid fa-circle-info' }}" style="color:var(--brand)"></i> {{ $s['title'] }}</h3><p class="muted">{{ $s['text'] }}</p></section>
    @endforeach
</div>
@endsection
