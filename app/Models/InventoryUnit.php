<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryUnit extends Model
{
    protected $fillable = ['product_id', 'unit_code', 'serial_number', 'condition_status', 'operational_status', 'notes'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
