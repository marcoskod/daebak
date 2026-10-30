@extends('layouts.shop')

@section('title', t('account.title'))

@section('content')
<x-page-hero :title="t('account.welcome', ['name' => explode(' ', auth()->user()->name)[0]])" :subtitle="t('account.title')" image="account" :crumbs="[['label' => t('account.title')]]">
    <x-slot:side>
        @if (auth()->user()->is_admin)<a href="{{ url('/admin') }}" class="btn btn-soft btn-sm"><i class="fa-solid fa-gauge"></i> {{ t('account.admin') }}</a>@endif
        <a href="{{ route('profile.edit') }}" class="btn btn-ghost btn-sm"><i class="fa-regular fa-user"></i> {{ t('account.profile') }}</a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost btn-sm"><i class="fa-solid fa-right-from-bracket"></i> {{ t('auth.logout') }}</button></form>
    </x-slot:side>
</x-page-hero>

<section class="card stack">
    <h2 style="font-size:20px;font-weight:800">{{ t('account.recent') }}</h2>
    @if ($orders->isEmpty())
        <div class="empty"><i class="fa-solid fa-box-open"></i><p class="muted">{{ t('account.none') }}</p><a href="{{ route('shop.catalogue') }}" class="btn btn-primary">{{ t('cart.continue') }}</a></div>
    @else
        <div class="table-wrap"><table class="table">
            <thead><tr><th>{{ t('account.order') }}</th><th>{{ t('account.date') }}</th><th>{{ t('account.status') }}</th><th style="text-align:right">{{ t('account.total') }}</th></tr></thead>
            <tbody>@foreach ($orders as $o)
                <tr><td><b>{{ $o->order_number }}</b></td><td>{{ $o->created_at->locale(app()->getLocale())->isoFormat('LL') }}</td><td><span class="pill pill-brand">{{ $o->status_label }}</span></td><td style="text-align:right">{{ money($o->total) }}</td></tr>
            @endforeach</tbody>
        </table></div>
    @endif
</section>
@endsection
