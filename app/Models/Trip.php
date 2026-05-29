<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'trip_number', 'trip_type', 'trip_date', 'trip_time',
        'airport_id', 'flight_number', 'branch_id', 'admin_id',
        'notes', 'status',
    ];

    protected function casts(): array
    {
        return [
            'trip_date' => 'date',
        ];
    }

    // ── Static Options ───────────────────────────────────────────────────────

    public static function typeLabels(): array
    {
        return [
            'arrival'         => 'استلام وصول',
            'departure'       => 'مغادرة',
            'group_transport' => 'نقل جماعي',
            'deportation'     => 'تسفير',
        ];
    }

    public static function typeColors(): array
    {
        return [
            'arrival'         => '#16a34a',
            'departure'       => '#2563eb',
            'group_transport' => '#c9a84c',
            'deportation'     => '#dc2626',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            'scheduled'  => 'مجدولة',
            'completed'  => 'منتهية',
            'cancelled'  => 'ملغاة',
        ];
    }

    // ── Number Generator ─────────────────────────────────────────────────────

    public static function generateNumber(): string
    {
        $year    = Carbon::now()->year;
        $prefix  = "TR-{$year}-";
        $last    = static::where('trip_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('trip_number');
        $seq     = $last ? ((int) substr($last, -5)) + 1 : 1;
        return $prefix . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return static::typeLabels()[$this->trip_type] ?? $this->trip_type;
    }

    public function getTypeColorAttribute(): string
    {
        return static::typeColors()[$this->trip_type] ?? '#64748b';
    }

    public function getStatusLabelAttribute(): string
    {
        return static::statusLabels()[$this->status] ?? $this->status;
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function airport(): BelongsTo
    {
        return $this->belongsTo(Airport::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(Worker::class, 'trip_workers')
            ->withPivot('contract_id', 'notes', 'status')
            ->withTimestamps();
    }
}
