@extends('layouts.shop')

@section('title', t('creators.title'))
@section('description', t('creators.subtitle'))

@section('content')
<x-page-hero :title="t('creators.title')" :subtitle="t('creators.subtitle')" image="creators" :crumbs="[['label' => t('nav.creators')]]" />

<section class="card-flat reveal stack" style="max-width:900px">
    <span class="eyebrow">{{ t('creators.story_eyebrow') }}</span>
    <h2 style="font-size:26px;font-weight:800">{{ t('creators.story_title') }}</h2>
    <p class="muted">{{ t('creators.story_text') }}</p>
</section>

<section class="section">
    <div class="sec-head reveal"><div><h2>{{ t('creators.list_title') }}</h2><p>{{ t('creators.sample_note') }}</p></div></div>
    <div class="grid-3">
        @foreach (tg('creators.list') as $i => $c)
            <article class="card creator reveal">
                <img src="{{ asset('images/creators/creator-'.($i + 1).'.svg') }}" alt="{{ $c['name'] }}" loading="lazy" width="96" height="96">
                <div><h3>{{ $c['name'] }}</h3><span class="pill pill-brand" style="margin-top:6px">{{ $c['style'] }}</span></div>
                <p class="muted" style="font-size:14px">{{ $c['bio'] }}</p>
                <small class="muted"><i class="fa-solid fa-location-dot"></i> {{ $c['city'] }}</small>
            </article>
        @endforeach
    </div>
</section>

<section class="card stack reveal" id="apply" style="max-width:760px;width:100%;margin-inline:auto">
    <h2 style="font-size:22px;font-weight:800">{{ t('creators.apply_title') }}</h2>
    <p class="muted">{{ t('creators.apply_text') }}</p>
    <form action="{{ route('pages.contact.store') }}" method="POST" data-ajax data-reset class="stack">
        @csrf
        <input type="hidden" name="type" value="creator"><input type="hidden" name="subject" value="{{ t('creators.apply_title') }}">
        <div class="form-grid">
            <div class="field"><label for="cr-name">{{ t('checkout.name') }} *</label><input class="input" id="cr-name" name="name" required></div>
            <div class="field"><label for="cr-email">{{ t('checkout.email') }} *</label><input class="input" type="email" id="cr-email" name="email" required></div>
            <div class="field full"><label for="cr-link">{{ t('creators.portfolio') }}</label><input class="input" type="url" id="cr-link" name="link" placeholder="https://"></div>
            <div class="field full"><label for="cr-msg">{{ t('creators.about_you') }} *</label><textarea class="textarea" id="cr-msg" name="message" required maxlength="3000"></textarea></div>
        </div>
        <button class="btn btn-primary btn-lg" type="submit"><i class="fa-solid fa-palette"></i> {{ t('creators.send') }}</button>
    </form>
</section>
@endsection
