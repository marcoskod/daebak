@extends('layouts.shop')

@section('title', t('auth.reset_title'))

@section('content')
<x-page-hero :title="t('auth.reset_title')" image="auth" :crumbs="[['label' => t('auth.reset_title')]]" />
<section class="card stack" style="max-width:480px;width:100%;margin-inline:auto">
    <form method="POST" action="{{ route('password.store') }}" class="stack">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="field"><x-input-label for="email" :value="t('auth.email')" /><x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" /><x-input-error :messages="$errors->get('email')" /></div>
        <div class="field"><x-input-label for="password" :value="t('auth.password')" /><x-text-input id="password" type="password" name="password" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password')" /></div>
        <div class="field"><x-input-label for="password_confirmation" :value="t('auth.confirm_password')" /><x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password_confirmation')" /></div>
        <x-primary-button class="btn-block">{{ t('auth.reset_button') }}</x-primary-button>
    </form>
</section>
@endsection
