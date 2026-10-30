@extends('layouts.shop')

@section('title', t('auth.register_title'))

@section('content')
<x-page-hero :title="t('auth.register_title')" :subtitle="t('auth.register_sub')" image="auth" :crumbs="[['label' => t('nav.register')]]" />
<section class="card stack" style="max-width:480px;width:100%;margin-inline:auto">
    <form method="POST" action="{{ route('register') }}" class="stack">
        @csrf
        <div class="field"><x-input-label for="name" :value="t('auth.name')" /><x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" /><x-input-error :messages="$errors->get('name')" /></div>
        <div class="field"><x-input-label for="email" :value="t('auth.email')" /><x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" /><x-input-error :messages="$errors->get('email')" /></div>
        <div class="field"><x-input-label for="password" :value="t('auth.password')" /><x-text-input id="password" type="password" name="password" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password')" /></div>
        <div class="field"><x-input-label for="password_confirmation" :value="t('auth.confirm_password')" /><x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" /></div>
        <x-primary-button class="btn-block"><i class="fa-solid fa-user-plus"></i> {{ t('auth.submit_register') }}</x-primary-button>
    </form>
    <p class="muted" style="text-align:center">{{ t('auth.have_account') }} <a href="{{ route('login') }}" style="color:var(--brand);font-weight:800">{{ t('nav.login') }}</a></p>
</section>
@endsection
