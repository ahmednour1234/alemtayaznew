<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SponsorshipTransfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sponsorship_transfer_contracts';

    protected $fillable = [
        'contract_number', 'musaned_contract_number', 'musaned_contract_image', 'worker_id', 'from_client_id', 'to_client_id',
        'branch_id', 'admin_id', 'original_contract_id', 'transfer_date',
        'total_fees', 'service_fee', 'loss_amount',
        'payment_status', 'needs_medical_exam', 'needs_iqama',
        'current_department', 'current_status',
        'notes', 'active',
    ];

    protected function casts(): array
    {
        return [
            'transfer_date' => 'date',
            'total_fees'    => 'decimal:2',
            'service_fee'   => 'decimal:2',
            'loss_amount'   => 'decimal:2',
            'needs_medical_exam' => 'boolean',
            'needs_iqama'        => 'boolean',
            'active'        => 'boolean',
        ];
    }

    // ── Static Options ───────────────────────────────────────────────────────

    public static function statuses(): array
    {
        return [
            1 => ['label' => 'جديد',           'color' => '#64748b'],
            2 => ['label' => 'قيد المعالجة',   'color' => '#c9a84c'],
            3 => ['label' => 'تم النقل',        'color' => '#16a34a'],
            4 => ['label' => 'ملغي',            'color' => '#dc2626'],
        ];
    }

    public static function departments(): array
    {
        return [
            'customer_service' => 'خدمة العملاء',
            'accounts'         => 'الحسابات',
        ];
    }

    public static function paymentStatuses(): array
    {
        return [
            'pending' => 'معلق',
            'partial' => 'مدفوع جزئياً',
            'full'    => 'مدفوع كاملاً',
        ];
    }

    // ── Number Generator ─────────────────────────────────────────────────────

    public static function generateNumber(): string
    {
        $year   = Carbon::now()->year;
        $prefix = "ST-{$year}-";
        $last   = static::where('contract_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('contract_number');
        $seq    = $last ? ((int) substr($last, -5)) + 1 : 1;
        return $prefix . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return static::statuses()[$this->current_status]['label'] ?? '-';
    }

    public function getStatusColorAttribute(): string
    {
        return static::statuses()[$this->current_status]['color'] ?? '#64748b';
    }

    public function getDepartmentLabelAttribute(): string
    {
        return static::departments()[$this->current_department] ?? $this->current_department;
    }

    public function getNetResultAttribute(): float
    {
        return (float) $this->total_fees - (float) $this->loss_amount;
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function fromClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'from_client_id');
    }

    public function toClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'to_client_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function originalContract(): BelongsTo
    {
        return $this->belongsTo(RecruitmentContract::class, 'original_contract_id');
    }
}
