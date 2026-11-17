<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class RentalFine extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'assessed_at' => 'datetime', 'settled_at' => 'datetime']; }
    public function order(): BelongsTo { return $this->belongsTo(RentalOrder::class); }
    public function inspection(): BelongsTo { return $this->belongsTo(RentalInspection::class, 'inspection_id'); }
    public function assessor(): BelongsTo { return $this->belongsTo(User::class, 'assessed_by'); }
    public function settler(): BelongsTo { return $this->belongsTo(User::class, 'settled_by'); }
}
