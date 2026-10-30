@php
    $code = isset($exception) && method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
    $known = [401, 403, 404, 419, 429, 500, 503];
    $title = in_array($code, $known) ? t("errors.$code") : t('errors.generic');
    $text = $code === 404 ? t('errors.text_404') : ($code === 503 ? t('errors.text_503') : t('errors.generic_text'));
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="light">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} · {{ $title }} — {{ site('brand.name') }}</title>
    <link rel="icon" href="{{ asset(site('brand.favicon')) }}">
    <link rel="stylesheet" href="{{ asset('css/daebak.css') }}">
    <style>:root:not([data-theme="dark"]){--brand:{{ site('colors.primary') }};--brand-solid:{{ site('colors.primary') }}}body{display:grid;place-items:center;padding:16px;background-image:url('{{ asset('images/heroes/error.svg') }}');background-size:cover}.err-card{max-width:520px;width:100%;text-align:center;display:grid;gap:14px;justify-items:center}.err-code{font-size:88px;font-weight:800;letter-spacing:-.05em;color:var(--brand);line-height:1}</style>
</head>
<body>
    <main class="card err-card" role="main">
        <img src="{{ asset(site('brand.logo_mark')) }}" alt="" width="56" height="56" style="border-radius:16px">
        <div class="err-code">{{ $code }}</div>
        <h1 style="font-size:24px;font-weight:800">{{ $title }}</h1>
        <p class="muted">{{ $text }}</p>
        <a class="btn btn-primary btn-lg" href="{{ url('/') }}"><i class="fa-solid fa-house"></i> {{ t('errors.home') }}</a>
    </main>
</body>
</html>
