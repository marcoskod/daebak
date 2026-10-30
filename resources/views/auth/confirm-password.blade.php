@extends('layouts.shop')

@section('title', t('auth.confirm_title'))

@section('content')
<x-page-hero :title="t('auth.confirm_title')" image="auth" :crumbs="[['label' => t('auth.confirm_title')]]" />
<section class="card stack" style="max-width:480px;width:100%;margin-inline:auto">
    <p class="muted">{{ t('auth.confirm_text') }}</p>
    <form method="POST" action="{{ route('password.confirm') }}" class="stack">
        @csrf
        <div class="field"><x-input-label for="password" :value="t('auth.password')" /><x-text-input id="password" type="password" name="password" required autocomplete="current-password" /><x-input-error :messages="$errors->get('password')" /></div>
        <x-primary-button class="btn-block">{{ t('auth.confirm_button') }}</x-primary-button>
    </form>
</section>
@endsection
