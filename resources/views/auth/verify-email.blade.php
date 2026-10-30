@extends('layouts.shop')

@section('title', t('auth.verify_title'))

@section('content')
<x-page-hero :title="t('auth.verify_title')" image="auth" :crumbs="[['label' => t('auth.verify_title')]]" />
<section class="card stack" style="max-width:520px;width:100%;margin-inline:auto">
    <p class="muted">{{ t('auth.verify_text') }}</p>
    @if (session('status') === 'verification-link-sent')<div class="alert alert-ok" role="status"><i class="fa-solid fa-circle-check"></i> {{ t('auth.verify_sent') }}</div>@endif
    <div class="row">
        <form method="POST" action="{{ route('verification.send') }}">@csrf<x-primary-button><i class="fa-regular fa-envelope"></i> {{ t('auth.resend') }}</x-primary-button></form>
        <form method="POST" action="{{ route('logout') }}">@csrf<x-secondary-button type="submit">{{ t('auth.logout') }}</x-secondary-button></form>
    </div>
</section>
@endsection
