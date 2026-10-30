@extends('layouts.shop')

@php $lt = t("legal.$key.title"); @endphp
@section('title', $lt)

@section('content')
<x-page-hero :title="$lt" :subtitle="t('legal.updated', ['date' => '2026-10-15'])" image="legal" :crumbs="[['label' => $lt]]" />

<article class="card prose reveal" style="max-width:860px;width:100%;margin-inline:auto">
    <div class="alert alert-info" style="margin-bottom:18px"><i class="fa-solid fa-circle-info"></i> {{ t('legal.template_notice') }}</div>
    <p>{{ str_replace([':brand', ':legal'], [site('brand.name'), site('brand.legal_name')], t("legal.$key.intro")) }}</p>
    @foreach (tg("legal.$key.sections") as $i => $s)
        <h2>{{ $i + 1 }}. {{ $s['title'] }}</h2>
        <p>{{ str_replace([':brand', ':legal', ':email', ':address'], [site('brand.name'), site('brand.legal_name'), site('contact.email'), site('contact.address')], $s['text']) }}</p>
    @endforeach
</article>
@endsection
