<?php

namespace App\Models;

use App\Support\Availability;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Pesanan sewa. Status order, pembayaran, pengambilan, dan penyewaan SELALU terpisah.
 * $guarded hanya untuk id: model ini hanya dibuat oleh BookingService dengan array eksplisit,
 * JANGAN pernah memakai ->fill($request->all()).
 */
class RentalOrder extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'subtotal'                   => 'decimal:2',
            'additional_fees'            => 'decimal:2',
            'total_amount'               => 'decimal:2',
            'customer_terms_accepted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RentalOrderItem::class, 'order_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'order_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id')->latestOfMany();
    }

    /** Bentuk array untuk view dashboard dan invoice (waktu dalam WITA). */
    public function toViewArray(): array
    {
        $item = $this->items->first();
        $tz = Availability::TZ;
        $start = CarbonImmutable::createFromInterface($item->start_at)->setTimezone($tz);
        $end = CarbonImmutable::createFromInterface($item->end_at)->setTimezone($tz);
        $short = fn (CarbonImmutable $d) => $d->locale('id')->translatedFormat('j M');
        $long = fn (CarbonImmutable $d) => $d->locale('id')->translatedFormat('j F Y');

        return [
            'id'          => $this->id,
            'invoice'     => $this->invoice?->invoice_number ?? $this->order_number,
            'order_number' => $this->order_number,
            'customer'    => $this->user?->name ?? '-',
            'product'     => $item->product_name_snapshot,
            'unit'        => $item->unit_code_snapshot ?? '-',
            'start'       => $start,
            'end'         => $end,
            'start_full'  => $long($start),
            'end_full'    => $long($end),
            'period'      => $short($start).' – '.$short($end),
            'days'        => (int) $start->diffInDays($end),
            'method'      => ['QRIS' => 'QRIS', 'VIRTUAL_ACCOUNT' => 'Virtual account', 'COD' => 'COD'][$this->payment_method] ?? $this->payment_method,
            'total'       => $this->total_amount !== null ? (int) $this->total_amount : null,
            'order'       => $this->order_status,
            'payment'     => $this->payment_status,
            'pickup'      => $this->pickup_status,
            'rental'      => $this->rental_status,
            'due_in'      => (int) Availability::today()->diffInDays($end, false),
        ];
    }
}
