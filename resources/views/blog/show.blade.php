@extends('layouts.shop')

@section('title', $post->title)
@section('description', $post->excerpt)
@section('og_image', $post->cover_url)

@php
    $tpl = $post->templateFor(request()->query('tpl'));
    $date = $post->published_at?->locale(app()->getLocale())->isoFormat('LL');
@endphp
@push('styles')<link rel="stylesheet" href="{{ asset('css/desc-templates.css') }}">@if ($tpl !== 'classic')<link rel="stylesheet" href="{{ asset('css/blog-templates.css') }}?v={{ @filemtime(public_path('css/blog-templates.css')) }}">@endif @endpush

@section('content')
<x-page-hero :title="t('blog.title')" image="about" tag="p" :crumbs="[['label' => t('blog.nav'), 'url' => route('blog.index')], ['label' => t('blog.categories.'.$post->category), 'url' => route('blog.index', ['category' => $post->category])], ['label' => \Illuminate\Support\Str::limit($post->title, 48)]]" />

@include('blog.templates.'.config('blog_templates.templates.'.$tpl.'.view', 'classic'))

@include('blog.templates._tail', ['skipProducts' => $tpl === 'gallery-showcase' && $products->count() > 0])

@push('scripts')
<script type="application/ld+json">{!! json_encode(['@'.'context' => 'https://schema.org', '@'.'type' => 'Article', 'headline' => $post->title, 'description' => $post->excerpt, 'image' => $post->cover_url, 'datePublished' => $post->published_at?->toIso8601String(), 'author' => ['@'.'type' => 'Person', 'name' => $post->author], 'publisher' => ['@'.'type' => 'Organization', 'name' => site('brand.name')]], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
<script>document.getElementById('share-btn').addEventListener('click',function(){var d={title:document.title,url:location.href};if(navigator.share){navigator.share(d).catch(function(){});}else{navigator.clipboard&&navigator.clipboard.writeText(location.href);Daebak.toast(Daebak.t('js.link_copied'),'info');}});</script>
@endpush
@endsection
