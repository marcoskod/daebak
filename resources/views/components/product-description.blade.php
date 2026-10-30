@props(['text' => '', 'template' => 'classic'])
@php
    $parsed = \App\Support\ProductDescription::parse($text);
    if (! view()->exists('description-templates.'.$template)) { $template = 'classic'; }
@endphp
@once
    @push('styles')<link rel="stylesheet" href="{{ asset('css/desc-templates.css') }}?v={{ @filemtime(public_path('css/desc-templates.css')) }}">@endpush
    @push('scripts')<script src="{{ asset('js/desc-templates.js') }}?v={{ @filemtime(public_path('js/desc-templates.js')) }}" defer></script>@endpush
@endonce
<div class="pd pd--{{ $template }}" data-pd="{{ $template }}">
    @include('description-templates.'.$template, ['d' => $parsed])
</div>
