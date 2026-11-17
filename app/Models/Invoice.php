<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['issued_at' => 'datetime', 'document_snapshot' => 'array'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(RentalOrder::class, 'order_id');
    }
}
