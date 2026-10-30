<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head><meta charset="utf-8"><title>{{ t('mail.contact_subject', ['brand' => site('brand.name')]) }}</title></head>
<body style="font-family:'Urbanist','Noto Sans KR',Arial,sans-serif;color:#1a1c1b;line-height:1.6">
    <p>{{ t('mail.hello', ['name' => $contactMessage->name]) }}</p>
    <p>{{ t('mail.contact_thanks') }}</p>
    <p><strong>{{ t('mail.contact_copy') }}</strong></p>
    <p style="white-space:pre-line;background:#faf5f2;padding:12px;border-radius:8px">{{ $contactMessage->message }}</p>
    <p>{{ t('mail.signoff') }}<br>{{ t('mail.team', ['brand' => site('brand.name')]) }}</p>
</body></html>
