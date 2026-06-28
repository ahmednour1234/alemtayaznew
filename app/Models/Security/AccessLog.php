<?php

namespace App\Models\Security;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessLog extends Model
{
    public const UPDATED_AT = null; // append-only: created_at only

    protected $fillable = [
        'user_id', 'guard', 'route_name', 'url', 'method',
        'ip_address', 'user_agent', 'status_code', 'action_type', 'metadata',
    ];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }
}
