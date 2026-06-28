<?php

namespace App\Models\Security;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExportLog extends Model
{
    public const UPDATED_AT = null; // append-only: created_at only

    protected $fillable = [
        'user_id', 'export_type', 'model_type', 'model_id',
        'file_name', 'filters', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return ['filters' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }
}
