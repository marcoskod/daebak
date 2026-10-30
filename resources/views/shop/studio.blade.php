@extends('layouts.shop')

@section('title', t('studio.title'))
@section('description', t('studio.subtitle'))

@section('content')
<x-page-hero :title="t('studio.title')" :subtitle="t('studio.subtitle')" image="studio" :crumbs="[['label' => t('nav.studio')]]" />

<div class="pdp pdp-studio">
    <div class="stack" style="gap:18px">
        <section class="card stack reveal">
            <h2 style="font-size:22px;font-weight:800">{{ t('studio.how_title') }}</h2>
            @foreach (tg('studio.how') as $i => $s)
                <div class="row" style="flex-wrap:nowrap;align-items:flex-start;gap:14px"><span class="avatar" style="flex:none">{{ $i + 1 }}</span><div><b>{{ $s['title'] }}</b><p class="muted" style="font-size:14px">{{ $s['text'] }}</p></div></div>
            @endforeach
        </section>
        <section class="card stack reveal">
            <h2 style="font-size:22px;font-weight:800">{{ t('studio.specs_title') }}</h2>
            <div class="table-wrap"><table class="table"><thead><tr><th>{{ t('studio.col_product') }}</th><th>{{ t('studio.col_area') }}</th><th>{{ t('studio.col_format') }}</th></tr></thead>
                <tbody>@foreach (tg('studio.specs') as $r)<tr><td><b>{{ $r['product'] }}</b></td><td>{{ $r['area'] }}</td><td>{{ $r['format'] }}</td></tr>@endforeach</tbody></table></div>
            <div class="alert alert-info"><i class="fa-solid fa-lightbulb"></i> {{ t('studio.tip') }}</div>
        </section>
    </div>

    <section class="card stack reveal sticky-card">
        <h2 style="font-size:22px;font-weight:800">{{ t('studio.form_title') }}</h2>
        <form action="{{ route('pages.contact.store') }}" method="POST" data-ajax data-reset class="stack">
            @csrf
            <input type="hidden" name="type" value="studio">
            <input type="hidden" name="subject" value="{{ t('studio.form_title') }}">
            <div class="form-grid">
                <div class="field"><label for="st-name">{{ t('checkout.name') }} *</label><input class="input" id="st-name" name="name" required autocomplete="name"></div>
                <div class="field"><label for="st-email">{{ t('checkout.email') }} *</label><input class="input" type="email" id="st-email" name="email" required autocomplete="email"></div>
                <div class="field full"><label for="st-product">{{ t('studio.product') }}</label>
                    <select class="select" id="st-product" name="product_type">@foreach (tg('studio.products') as $p)<option>{{ $p }}</option>@endforeach</select></div>
                <div class="field full"><label for="st-link">{{ t('studio.link') }}</label><input class="input" type="url" id="st-link" name="link" placeholder="https://"></div>
                <div class="field full"><label for="st-msg">{{ t('studio.idea') }} *</label><textarea class="textarea" id="st-msg" name="message" required maxlength="3000" placeholder="{{ t('studio.idea_placeholder') }}"></textarea></div>
            </div>
            <button class="btn btn-primary btn-lg" type="submit"><i class="fa-solid fa-paper-plane"></i> {{ t('studio.send') }}</button>
        </form>
    </section>
</div>
@endsection
