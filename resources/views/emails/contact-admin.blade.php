<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>{{ $contactMessage->subject }}</title></head>
<body style="font-family:'Urbanist','Noto Sans KR',Arial,sans-serif;color:#1a1c1b;line-height:1.6">
    <table cellpadding="4">
        <tr><td><strong>Name</strong></td><td>{{ $contactMessage->name }}</td></tr>
        <tr><td><strong>Email</strong></td><td>{{ $contactMessage->email }}</td></tr>
        <tr><td><strong>Phone</strong></td><td>{{ $contactMessage->phone ?? '—' }}</td></tr>
        <tr><td><strong>Subject</strong></td><td>{{ $contactMessage->subject }}</td></tr>
    </table>
    <p style="white-space:pre-line;background:#faf5f2;padding:12px;border-radius:8px">{{ $contactMessage->message }}</p>
    <p>Reply directly to this email: it goes to {{ $contactMessage->email }}.</p>
</body></html>
