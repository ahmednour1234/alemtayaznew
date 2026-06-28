<?php

namespace App\Models\Security;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermissionChangeLog extends Model
{
    public const UPDATED_AT = null; // append-only: created_at only

    protected $fillable = [
        'changed_by', 'target_user_id', 'role_id', 'permission_id',
        'action', 'before', 'after', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'before' => 'array',
            'after'  => 'array',
        ];
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'changed_by');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'target_user_id');
    }
}
