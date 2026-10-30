@extends('layouts.shop')

@section('title', t('auth.login_title'))

@section('content')
<x-page-hero :title="t('auth.login_title')" :subtitle="t('auth.login_sub')" image="auth" :crumbs="[['label' => t('nav.login')]]" />
<section class="card stack" style="max-width:480px;width:100%;margin-inline:auto">
    <x-auth-session-status :status="session('status')" />
    <form method="POST" action="{{ route('login') }}" class="stack">
        @csrf
        <div class="field"><x-input-label for="email" :value="t('auth.email')" /><x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" /><x-input-error :messages="$errors->get('email')" /></div>
        <div class="field"><x-input-label for="password" :value="t('auth.password')" /><x-text-input id="password" type="password" name="password" required autocomplete="current-password" /><x-input-error :messages="$errors->get('password')" /></div>
        <div class="row" style="justify-content:space-between">
            <label class="check"><input type="checkbox" name="remember"><span>{{ t('auth.remember') }}</span></label>
            @if (Route::has('password.request'))<a href="{{ route('password.request') }}" style="color:var(--brand);font-weight:700;font-size:14px">{{ t('auth.forgot') }}</a>@endif
        </div>
        <x-primary-button class="btn-block"><i class="fa-solid fa-right-to-bracket"></i> {{ t('auth.submit_login') }}</x-primary-button>
    </form>
    <p class="muted" style="text-align:center">{{ t('auth.no_account') }} <a href="{{ route('register') }}" style="color:var(--brand);font-weight:800">{{ t('auth.register_link') }}</a></p>
</section>
@endsection
