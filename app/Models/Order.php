<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'shipping_city', 'shipping_postal_code', 'shipping_country',
        'subtotal', 'shipping_cost', 'total', 'currency', 'locale',
        'status', 'payment_method', 'payment_status', 'payment_reference', 'fedapay_transaction_id', 'notes',
    ];

    protected function casts(): array
    {
        return ['subtotal' => 'decimal:2', 'shipping_cost' => 'decimal:2', 'total' => 'decimal:2'];
    }

    /** Statuts : libellés via lang (order.status.{clé}) */
    public const STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

    public const PAYMENT_METHODS = ['card', 'paypal'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return t('order.status.'.$this->status);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return t('checkout.pay.'.$this->payment_method);
    }

    public static function numberFor(int $id): string
    {
        return 'DB-'.now()->format('Y').'-'.str_pad((string) $id, 5, '0', STR_PAD_LEFT);
    }
}
