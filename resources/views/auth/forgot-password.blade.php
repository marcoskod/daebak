@extends('layouts.shop')

@section('title', t('auth.forgot_title'))

@section('content')
<x-page-hero :title="t('auth.forgot_title')" :subtitle="t('auth.forgot_text')" image="auth" :crumbs="[['label' => t('auth.forgot_title')]]" />
<section class="card stack" style="max-width:480px;width:100%;margin-inline:auto">
    <x-auth-session-status :status="session('status')" />
    <form method="POST" action="{{ route('password.email') }}" class="stack">
        @csrf
        <div class="field"><x-input-label for="email" :value="t('auth.email')" /><x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus /><x-input-error :messages="$errors->get('email')" /></div>
        <x-primary-button class="btn-block"><i class="fa-regular fa-paper-plane"></i> {{ t('auth.send_link') }}</x-primary-button>
    </form>
</section>
@endsection
