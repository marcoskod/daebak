@extends('layouts.shop')

@section('title', t('size_guide.title'))
@section('description', t('size_guide.text'))

@section('content')
<x-page-hero :title="t('size_guide.title')" :subtitle="t('size_guide.text')" image="shipping" :crumbs="[['label' => t('footer.size_guide')]]" />

<section class="card reveal stack"><h2 style="font-size:20px;font-weight:800"><i class="fa-solid fa-shirt" style="color:var(--brand)"></i> {{ t('size_guide.apparel') }}</h2>@include('partials.size-chart')</section>

<section class="card reveal stack"><h2 style="font-size:20px;font-weight:800"><i class="fa-solid fa-ruler-combined" style="color:var(--brand)"></i> {{ t('size_guide.other_title') }}</h2>
    <div class="table-wrap"><table class="table"><thead><tr><th>{{ t('studio.col_product') }}</th><th>{{ t('size_guide.dimensions') }}</th></tr></thead>
        <tbody>@foreach (tg('size_guide.other') as $r)<tr><td><b>{{ $r['product'] }}</b></td><td>{{ $r['dims'] }}</td></tr>@endforeach</tbody></table></div>
</section>
@endsection
