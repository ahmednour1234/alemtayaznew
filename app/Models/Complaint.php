<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'complaint_number', 'contract_id', 'contract_type',
        'client_id', 'worker_id', 'branch_id',
        'problem_type', 'description', 'phone',
        'assigned_admin_id', 'priority', 'status',
        'on_musaned', 'musaned_number',
        'resolution', 'processed_at', 'resolved_at',
        'created_by_admin_id', 'last_stale_notified_at',
    ];

    protected function casts(): array
    {
        return [
            'on_musaned'             => 'boolean',
            'processed_at'           => 'datetime',
            'resolved_at'            => 'datetime',
            'last_stale_notified_at' => 'datetime',
        ];
    }

    // ── Relations ────────────────────────────────────────────────────────────
    public function contract(): BelongsTo  { return $this->belongsTo(RecruitmentContract::class, 'contract_id'); }
    public function client(): BelongsTo    { return $this->belongsTo(Client::class); }
    public function worker(): BelongsTo    { return $this->belongsTo(Worker::class); }
    public function branch(): BelongsTo    { return $this->belongsTo(Branch::class); }
    public function assignedAdmin(): BelongsTo { return $this->belongsTo(Admin::class, 'assigned_admin_id'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(Admin::class, 'created_by_admin_id'); }
    public function attachments(): HasMany { return $this->hasMany(ComplaintAttachment::class); }

    // ── Static option lists ──────────────────────────────────────────────────
    public static function problemTypes(): array
    {
        return [
            'salary'        => 'مشكلة رواتب',
            'food'          => 'مشكلة طعام',
            'escape'        => 'هروب',
            'work_refusal'  => 'رفض عمل',
            'abuse'         => 'سوء معاملة',
            'health'        => 'مشكلة صحية',
            'other'         => 'أخرى',
        ];
    }

    public static function priorities(): array
    {
        return [
            'low'    => 'منخفض',
            'medium' => 'متوسط',
            'high'   => 'عالي',
            'urgent' => 'عالي جدًا',
        ];
    }

    public static function statuses(): array
    {
        return [
            'new'         => 'جديدة',
            'in_progress' => 'قيد المعالجة',
            'resolved'    => 'تم الحل',
            'closed'      => 'مغلقة',
            'escalated'   => 'مصعّدة',
        ];
    }

    public static function generateNumber(): string
    {
        $prefix = 'COMP-' . now()->format('Ymd') . '-';
        $last   = static::where('complaint_number', 'like', $prefix . '%')
            ->orderByDesc('id')->value('complaint_number');
        $seq    = $last ? ((int) substr($last, -4)) + 1 : 1;
        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    // ── Display helpers ──────────────────────────────────────────────────────
    public function getProblemTypeLabelAttribute(): string
    {
        return self::problemTypes()[$this->problem_type] ?? $this->problem_type;
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::priorities()[$this->priority] ?? $this->priority;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'new'         => 'bg-blue-100 text-blue-700',
            'in_progress' => 'bg-amber-100 text-amber-700',
            'resolved'    => 'bg-emerald-100 text-emerald-700',
            'closed'      => 'bg-slate-100 text-slate-700',
            'escalated'   => 'bg-red-100 text-red-700',
            default       => 'bg-slate-100 text-slate-700',
        };
    }

    public function getPriorityBadgeClassAttribute(): string
    {
        return match ($this->priority) {
            'low'    => 'bg-slate-100 text-slate-600',
            'medium' => 'bg-blue-100 text-blue-700',
            'high'   => 'bg-orange-100 text-orange-700',
            'urgent' => 'bg-red-100 text-red-700',
            default  => 'bg-slate-100 text-slate-600',
        };
    }
}
