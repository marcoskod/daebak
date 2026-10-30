<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head><meta charset="utf-8"><title>{{ t('mail.order_subject', ['brand' => site('brand.name'), 'number' => $order->order_number]) }}</title></head>
<body style="margin:0;background:#faf9f7;font-family:'Urbanist','Noto Sans KR',Arial,sans-serif;color:#1a1c1b;line-height:1.6">
<table width="100%" cellpadding="0" cellspacing="0"><tr><td align="center" style="padding:24px 12px">
<table width="560" cellpadding="0" cellspacing="0" style="max-width:560px;background:#fff;border-radius:20px;overflow:hidden">
<tr><td style="background:#442a22;color:#fff;padding:22px 28px;font-size:20px;font-weight:800">{{ site('brand.name') }} <span style="color:#fed7ca;font-size:12px;letter-spacing:.2em">{{ site('brand.native_name') }}</span></td></tr>
<tr><td style="padding:28px">
    <p>{{ t('mail.hello', ['name' => $order->customer_name]) }}</p>
    <p>{{ t('mail.order_thanks', ['number' => $order->order_number, 'brand' => site('brand.name')]) }}</p>
    <table width="100%" cellpadding="8" style="border-collapse:collapse">
        @foreach ($order->items as $item)
            <tr style="border-bottom:1px solid #eee"><td>{{ $item->quantity }}× {{ $item->product_name }}<br><small style="color:#777">{{ collect($item->options ?? [])->map(fn ($v, $k) => $k === 'color' ? t('colors.'.$v) : $v)->implode(' · ') }}</small></td><td align="right">${{ number_format($item->line_total, 2) }}</td></tr>
        @endforeach
        <tr><td><strong>{{ t('mail.order_total') }}</strong></td><td align="right"><strong>${{ number_format($order->total, 2) }} USD</strong></td></tr>
    </table>
    <p style="margin-top:14px">{{ t('mail.order_paid') }} {{ t('mail.order_next') }}</p>
    <p><strong>{{ t('mail.order_ship_to') }}:</strong> {{ $order->shipping_address }}, {{ $order->shipping_postal_code }} {{ $order->shipping_city }}, {{ t('countries.'.$order->shipping_country) }}</p>
    <p><a href="{{ route('tracking.index') }}" style="display:inline-block;background:#442a22;color:#fff;text-decoration:none;padding:12px 22px;border-radius:999px;font-weight:700">{{ t('mail.order_track') }}</a></p>
    <p>{{ t('mail.signoff') }}<br>{{ t('mail.team', ['brand' => site('brand.name')]) }}</p>
</td></tr></table>
</td></tr></table>
</body></html>
