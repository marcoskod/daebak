@extends('layouts.shop')

@section('title', t('contact.title'))
@section('description', t('contact.subtitle'))

@section('content')
<x-page-hero :title="t('contact.title')" :subtitle="t('contact.subtitle')" image="contact" :crumbs="[['label' => t('footer.contact')]]" />

<div class="cart-layout">
    <section class="card stack reveal">
        <h2 style="font-size:22px;font-weight:800">{{ t('contact.form_title') }}</h2>
        <form action="{{ route('pages.contact.store') }}" method="POST" data-ajax data-reset class="stack">
            @csrf
            <input type="hidden" name="type" value="contact">
            <div class="form-grid">
                <div class="field"><label for="c-name">{{ t('checkout.name') }} *</label><input class="input" id="c-name" name="name" required autocomplete="name"></div>
                <div class="field"><label for="c-email">{{ t('checkout.email') }} *</label><input class="input" type="email" id="c-email" name="email" required autocomplete="email"></div>
                <div class="field full"><label for="c-subject">{{ t('contact.subject') }}</label>
                    <select class="select" id="c-subject" name="subject">@foreach (tg('contact.subjects') as $s)<option>{{ $s }}</option>@endforeach</select></div>
                <div class="field full"><label for="c-msg">{{ t('contact.message') }} *</label><textarea class="textarea" id="c-msg" name="message" required maxlength="3000"></textarea></div>
            </div>
            <button class="btn btn-primary btn-lg" type="submit"><i class="fa-solid fa-paper-plane"></i> {{ t('contact.send') }}</button>
        </form>
    </section>
    <aside class="stack">
        <div class="card stack reveal">
            <div class="row" style="flex-wrap:nowrap"><span class="avatar"><i class="fa-regular fa-envelope"></i></span><div><b>{{ t('footer.contact_title') }}</b><br><a href="mailto:{{ site('contact.email') }}" class="muted">{{ site('contact.email') }}</a></div></div>
            <div class="row" style="flex-wrap:nowrap"><span class="avatar"><i class="fa-solid fa-phone"></i></span><div><b>{{ t('contact.phone') }}</b><br><span class="muted">{{ site('contact.phone') }}</span></div></div>
            <div class="row" style="flex-wrap:nowrap"><span class="avatar"><i class="fa-solid fa-location-dot"></i></span><div><b>{{ t('contact.studio') }}</b><br><span class="muted">{{ site('contact.address') }}</span></div></div>
            <div class="row" style="flex-wrap:nowrap"><span class="avatar"><i class="fa-regular fa-clock"></i></span><div><b>{{ t('contact.hours_title') }}</b><br><span class="muted">{{ t(site('contact.hours_key')) }}</span></div></div>
        </div>
        <div class="card-flat stack reveal"><b>{{ t('contact.fast_title') }}</b><p class="muted" style="font-size:14px">{{ t('contact.fast_text') }}</p><button type="button" class="btn btn-soft" data-open-chat><i class="fa-solid fa-comment-dots"></i> {{ t('faq.chat') }}</button></div>
    </aside>
</div>
@endsection
