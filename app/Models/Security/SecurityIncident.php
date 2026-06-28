<?php

namespace App\Models\Security;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityIncident extends Model
{
    protected $fillable = [
        'type', 'severity', 'description', 'user_id', 'ip_address', 'user_agent',
        'related_model_type', 'related_model_id', 'metadata',
        'status', 'resolved_by', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata'    => 'array',
            'resolved_at' => 'datetime',
        ];
    }

    public const STATUSES = ['open', 'investigating', 'resolved', 'false_positive'];
    public const SEVERITIES = ['low', 'medium', 'high', 'critical'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'resolved_by');
    }
}
