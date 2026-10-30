<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->locale($order->locale ?: config('site.default_locale'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: t('mail.order_subject', ['brand' => site('brand.name'), 'number' => $this->order->order_number]));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order-confirmed-client', with: ['order' => $this->order]);
    }
}
