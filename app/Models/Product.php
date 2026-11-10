<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'product_type', 'description', 'specifications', 'usage_guide',
        'sale_enabled', 'rental_enabled', 'sale_price', 'rental_daily_rate', 'shopee_url', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'specifications'    => 'array',
            'sale_enabled'      => 'boolean',
            'rental_enabled'    => 'boolean',
            'is_active'         => 'boolean',
            'sale_price'        => 'decimal:2',
            'rental_daily_rate' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(InventoryUnit::class);
    }

    public function isDigital(): bool
    {
        return $this->product_type === 'DIGITAL_SERVICE';
    }
}
