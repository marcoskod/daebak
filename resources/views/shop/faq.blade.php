@extends('layouts.shop')

@section('title', t('faq.title'))
@section('description', t('faq.subtitle'))

@section('content')
<x-page-hero :title="t('faq.title')" :subtitle="t('faq.subtitle')" image="faq" :crumbs="[['label' => t('footer.faq')]]" />

<div class="stack" style="max-width:860px;width:100%;margin-inline:auto;gap:18px">
    <label class="search-pill" style="background:var(--card);border-color:var(--line)"><i class="fa-solid fa-magnifying-glass" style="color:var(--faint)"></i><input id="faq-search" type="search" placeholder="{{ t('faq.search') }}" aria-label="{{ t('faq.search') }}"></label>
    <div id="faq-list" class="acc">
        @foreach (tg('faq.items') as $i => $item)
            <details class="reveal" {{ $i === 0 ? 'open' : '' }}><summary>{{ $item['q'] }}<i class="fa-solid fa-chevron-down"></i></summary><div>{{ $item['a'] }}</div></details>
        @endforeach
    </div>
    <div class="card-flat row" style="justify-content:space-between">
        <div><b>{{ t('faq.more_title') }}</b><p class="muted" style="font-size:14px">{{ t('faq.more_text') }}</p></div>
        <div class="row"><button type="button" class="btn btn-primary" data-open-chat><i class="fa-solid fa-comment-dots"></i> {{ t('faq.chat') }}</button><a href="{{ route('pages.contact') }}" class="btn btn-ghost">{{ t('footer.contact') }}</a></div>
    </div>
</div>
@endsection
