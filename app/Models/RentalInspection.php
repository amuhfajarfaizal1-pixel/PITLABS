<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class RentalInspection extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['inspected_at' => 'datetime']; }
    public function order(): BelongsTo { return $this->belongsTo(RentalOrder::class); }
    public function orderItem(): BelongsTo { return $this->belongsTo(RentalOrderItem::class, 'order_item_id'); }
    public function unit(): BelongsTo { return $this->belongsTo(InventoryUnit::class, 'unit_id'); }
    public function inspector(): BelongsTo { return $this->belongsTo(User::class, 'inspector_user_id'); }
}
