<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Housing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id', 'admin_id', 'name', 'address', 'capacity', 'description', 'active',
    ];

    protected function casts(): array
    {
        return [
            'active'   => 'boolean',
            'capacity' => 'integer',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
