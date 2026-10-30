@extends('layouts.shop')

@section('title', t('account.profile'))

@section('content')
<x-page-hero :title="t('account.profile')" image="account" :crumbs="[['label' => t('account.title'), 'url' => route('dashboard')], ['label' => t('account.profile')]]" />
<div class="stack" style="max-width:720px;width:100%;margin-inline:auto">
    <section class="card">@include('profile.partials.update-profile-information-form')</section>
    <section class="card">@include('profile.partials.update-password-form')</section>
    <section class="card">@include('profile.partials.delete-user-form')</section>
</div>
@endsection
