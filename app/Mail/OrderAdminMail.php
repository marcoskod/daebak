<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->locale(config('site.default_locale'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: t('mail.admin_order_subject', ['number' => $this->order->order_number, 'total' => money($this->order->total, 'USD')]));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order-admin', with: ['order' => $this->order]);
    }
}
