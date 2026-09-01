<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Worker extends Model
{
    use HasFactory, SoftDeletes, \App\Models\Concerns\HasEncryptedPii;

    protected $fillable = [
        'name', 'passport_number', 'visa_number', 'nationality_id', 'profession',
        'gender', 'experience', 'religion', 'age', 'phone',
        'cv_path', 'original_cv_name', 'passport_image',
        'status', 'client_id', 'branch_id', 'admin_id',
        'assigned_by_admin_id', 'assigned_at',
        'notes', 'active',
    ];

    protected function casts(): array
    {
        return [
            'active'          => 'boolean',
            'assigned_at'     => 'datetime',
            'passport_number' => 'encrypted',
            'phone'           => 'encrypted',
        ];
    }

    /** حالات العقد التي تعني انتهاء الارتباط فتعود العاملة متاحة. */
    private const CONTRACT_ENDED = [14, 15];

    protected static function booted(): void
    {
        // حارس اتساق: لا تُصبح العاملة «متاحة» ولها عقد قائم لم ينتهِ.
        // ظهرت عاملات في حالة «تم الاستلام» ومع ذلك معروضات للحجز، فكان
        // بالإمكان حجزهنّ لعميل ثانٍ. نصحّح الحالة هنا لا في كل مسار على حدة.
        static::updating(function (Worker $worker): void {
            if (! $worker->isDirty('status')) {
                return;
            }

            // (أ) «متاحة» تعني معروضة للحجز، فلا تجتمع مع ارتباط قائم بعميل.
            if ($worker->status === 'available') {
                $contractStatus = $worker->latestContract?->current_status;

                // عقد قائم لم ينتهِ → العاملة مرتبطة فعلياً: «تم التعيين»
                if ($contractStatus !== null && ! in_array((int) $contractStatus, self::CONTRACT_ENDED, true)) {
                    $worker->status = 'assigned';
                    return;
                }

                // عميل بلا عقد → حجز قائم: «محجوزة».
                // نتجاهل الحالة إن كان العميل نفسه يُصفَّر في هذا التحديث،
                // فذلك هو مسار فكّ التعيين المشروع.
                if ($worker->client_id !== null && ! $worker->isDirty('client_id')) {
                    $worker->status = 'reserved';
                }

                return;
            }

            // (ب) «محجوزة» و«تم التعيين» تعنيان ارتباطاً بعميل، فلا تُضبطان
            // يدوياً من شاشة التعديل التي لا حقل عميل فيها. بلا عميل ولا عقد
            // تبقى العاملة عالقة: لا يفكّها أمر الـ72 ساعة ولا تظهر للحجز.
            if (in_array($worker->status, ['reserved', 'assigned'], true)
                && ! $worker->client_id
                && ! $worker->hasActiveContract()) {
                $worker->status = $worker->getOriginal('status');
            }
        });
    }

    /** Encrypted-at-rest PII fields and their searchable hash columns. */
    public function piiHashMap(): array
    {
        return [
            'passport_number' => 'passport_number_hash',
            'phone'           => 'phone_hash',
        ];
    }

    // ── Static option lists ──────────────────────────────────────────────────

    /**
     * قوائم التسميات — تُقرأ من ملفات اللغة لا من نصوص ثابتة،
     * فتتبع لغة الواجهة تلقائياً في القوائم والجداول والتقارير.
     *
     * المفاتيح (available, nanny, ...) هي ما يُخزَّن في قاعدة البيانات
     * ولا يتغيّر بتغيّر اللغة.
     */
    public static function statusOptions(): array
    {
        return __('workers.statuses');
    }

    public static function professions(): array
    {
        return __('workers.professions');
    }

    public static function experienceOptions(): array
    {
        return __('workers.experiences');
    }

    public static function genderOptions(): array
    {
        return __('workers.genders');
    }

    public static function religionOptions(): array
    {
        return __('workers.religions');
    }

    /**
     * هل العاملة مرتبطة بعميل حالياً؟
     *
     * نعتمد على وجود العميل نفسه لا على الحالة وحدها، لأن مسارات إنهاء
     * العقد وحذفه (RecruitmentContractService) تُرجع الحالة «available»
     * دون تصفير client_id — فتظهر العاملة متاحة وهي ما زالت مربوطة بعميل.
     */
    public function isBooked(): bool
    {
        return $this->client_id !== null
            || in_array($this->status, ['reserved', 'assigned'], true);
    }

    /**
     * العميل الفعلي للعاملة: المرتبط بها مباشرةً، وإلا عميل عقدها.
     *
     * بعض المسارات تُنشئ العقد دون ضبط client_id على العاملة، فتظهر بلا
     * عميل في القوائم بينما صفحتها تعرض عميل العقد. هذه الدالة هي المصدر
     * الموحّد للاثنين حتى لا تتناقض الشاشات.
     */
    public function effectiveClient(): ?Client
    {
        return $this->client ?? $this->latestContract?->client;
    }

    /**
     * هل للعاملة عقد استقدام قائم؟ (العقود المحذوفة لا تُحتسب)
     * وجود العقد يعني أن الارتباط يُدار من العقد لا من شاشة العمالة.
     */
    public function hasActiveContract(): bool
    {
        // نستخدم العلاقة المحمّلة مسبقاً (eager-loaded) إن وُجدت لتفادي
        // استعلام لكل صف في قائمة العمالة.
        return $this->relationLoaded('latestContract')
            ? $this->latestContract !== null
            : $this->latestContract()->exists();
    }

    /**
     * من يحق له إنشاء عقد استقدام لهذه العاملة.
     *
     * مقصور على الموظف الذي حجزها (والسوبر أدمن)، لأن الحجز التزام شخصي
     * تجاه عميل بعينه ولا يصحّ أن يبني عليه موظف آخر عقداً.
     */
    public function canCreateContractBy(?Admin $actor): bool
    {
        if (! $actor || $this->status !== 'reserved' || $this->hasActiveContract()) {
            return false;
        }

        return $actor->isSuperAdmin()
            || $actor->id === $this->assigned_by_admin_id;
    }

    /**
     * من يحق له إلغاء التعيين: الموظف الذي أجرى الحجز نفسه فقط
     * (بالإضافة للسوبر أدمن). مدير الفرع لا يملك هذه الصلاحية.
     * نفس القاعدة المطبّقة في WorkerService::unassign().
     */
    public function canBeUnassignedBy(?Admin $actor): bool
    {
        // العاملة المرتبطة بعقد استقدام يُدار فكّ ارتباطها من العقد نفسه،
        // حتى لا تتعارض حالة العاملة مع العقد القائم.
        if (! $actor || ! $this->isBooked() || $this->hasActiveContract()) {
            return false;
        }

        return $actor->isSuperAdmin()
            || $actor->id === $this->assigned_by_admin_id;
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return self::statusOptions()[$this->status] ?? (string) $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'available'            => '#16a34a',
            'reserved'             => '#ca8a04',
            'assigned'             => '#2563eb',
            'in_housing'           => '#7c3aed',
            'for_rent'             => '#0891b2',
            'sponsorship_transfer' => '#d97706',
            'deportation'          => '#dc2626',
            'returned'             => '#64748b',
            default                => '#64748b',
        };
    }

    public function getStatusBgAttribute(): string
    {
        return match ($this->status) {
            'available'            => '#dcfce7',
            'reserved'             => '#fef9c3',
            'assigned'             => '#dbeafe',
            'in_housing'           => '#ede9fe',
            'for_rent'             => '#cffafe',
            'sponsorship_transfer' => '#fef3c7',
            'deportation'          => '#fee2e2',
            'returned'             => '#f1f5f9',
            default                => '#f1f5f9',
        };
    }

    public function getProfessionLabelAttribute(): string
    {
        return self::professions()[$this->profession] ?? $this->profession ?? '—';
    }

    public function getGenderLabelAttribute(): string
    {
        return self::genderOptions()[$this->gender] ?? '—';
    }

    public function getExperienceLabelAttribute(): string
    {
        return self::experienceOptions()[$this->experience] ?? '—';
    }

    // ── Relations ────────────────────────────────────────────────────────────

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_by_admin_id');
    }

    public function latestContract(): HasOne
    {
        return $this->hasOne(\App\Models\RecruitmentContract::class)->latest();
    }

    public function recruitmentContracts(): HasMany
    {
        return $this->hasMany(\App\Models\RecruitmentContract::class);
    }

    public function activeHousingAssignment(): HasOne
    {
        return $this->hasOne(\App\Models\HousingAssignment::class)->whereNull('check_out_date')->latest();
    }

    /** سجل التدقيق — من عدّل/حذف/حجز هذه العاملة. */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(\App\Models\WorkerActivityLog::class, 'worker_id')->with('admin')->latest();
    }

    /** True if the worker has an active contract for a specific client */
    public function hasContractForClient(int $clientId): bool
    {
        return $this->latestContract()->where('client_id', $clientId)->exists();
    }
}
