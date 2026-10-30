<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    protected $fillable = ['product_id', 'author', 'country', 'rating', 'title', 'body', 'locale', 'verified', 'is_sample'];

    protected function casts(): array
    {
        return ['verified' => 'boolean', 'is_sample' => 'boolean', 'rating' => 'integer'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
