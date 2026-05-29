<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HousingAssignment extends Model
{
    use SoftDeletes;

    protected $table = 'worker_housing_assignments';

    protected $fillable = [
        'worker_id', 'housing_id', 'branch_id', 'admin_id',
        'check_in_date', 'check_out_date', 'notes', 'reason',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date'  => 'date',
            'check_out_date' => 'date',
        ];
    }

    // ── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereNull('check_out_date');
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function housing(): BelongsTo
    {
        return $this->belongsTo(Housing::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return is_null($this->check_out_date);
    }

    public function daysStayed(): int
    {
        $end = $this->check_out_date ?? now();
        return (int) $this->check_in_date->diffInDays($end);
    }
}
