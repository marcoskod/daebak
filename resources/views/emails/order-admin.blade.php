<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head><meta charset="utf-8"><title>{{ t('mail.admin_order_title') }}</title></head>
<body style="font-family:'Urbanist','Noto Sans KR',Arial,sans-serif;color:#1a1c1b;line-height:1.6">
    <h2>{{ t('mail.admin_order_title') }}</h2>
    <table cellpadding="4">
        <tr><td><strong>#</strong></td><td>{{ $order->order_number }}</td></tr>
        <tr><td><strong>{{ t('mail.customer') }}</strong></td><td>{{ $order->customer_name }} — {{ $order->customer_email }} {{ $order->customer_phone }}</td></tr>
        <tr><td><strong>{{ t('checkout.shipping') }}</strong></td><td>{{ $order->shipping_address }}, {{ $order->shipping_postal_code }} {{ $order->shipping_city }}, {{ t('countries.'.$order->shipping_country) }}</td></tr>
        <tr><td><strong>{{ t('checkout.payment') }}</strong></td><td>{{ $order->payment_method_label }} ({{ $order->payment_status }})</td></tr>
        <tr><td><strong>{{ t('mail.order_total') }}</strong></td><td>${{ number_format($order->total, 2) }} USD</td></tr>
    </table>
    <ul>
        @foreach ($order->items as $item)
            <li>{{ $item->quantity }}× {{ $item->product_name }} ({{ collect($item->options ?? [])->implode(' / ') }}) — {{ t('mail.provider') }}: {{ $item->provider }} — ${{ number_format($item->line_total, 2) }}</li>
        @endforeach
    </ul>
    <p>→ {{ url('/admin') }}</p>
</body></html>
