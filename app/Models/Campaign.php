<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'sheet_url', 'budget',
        'start_date', 'end_date', 'branch_id', 'admin_id', 'active',
    ];

    protected function casts(): array
    {
        return [
            'active'     => 'boolean',
            'budget'     => 'decimal:2',
            'start_date' => 'date',
            'end_date'   => 'date',
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

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function getConvertedCountAttribute(): int
    {
        return $this->leads()->where('status', 'converted')->count();
    }

    public function getTotalLeadsCountAttribute(): int
    {
        return $this->leads()->count();
    }
}
