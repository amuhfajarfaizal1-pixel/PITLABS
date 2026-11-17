<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public const UPDATED_AT = null;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['old_values' => 'array', 'new_values' => 'array'];
    }

    /** Catat perubahan penting. Dipanggil dari service/controller, tidak pernah dari input mentah. */
    public static function record(?int $actorId, string $entityType, int $entityId, string $action, ?array $old = null, ?array $new = null, ?string $reason = null): self
    {
        return static::create([
            'actor_user_id' => $actorId,
            'entity_type'   => $entityType,
            'entity_id'     => $entityId,
            'action'        => $action,
            'old_values'    => $old,
            'new_values'    => $new,
            'reason'        => $reason,
            'ip_address'    => request()?->ip(),
        ]);
    }
}
