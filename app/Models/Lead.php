<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'campaign_id', 'name', 'phone', 'city', 'nationality_id',
        'branch_id', 'assigned_admin_id', 'referred_by_admin_id',
        'source', 'status', 'notes', 'client_id', 'last_contacted_at',
    ];

    protected function casts(): array
    {
        return [
            'last_contacted_at' => 'datetime',
        ];
    }

    public static function statuses(): array
    {
        return [
            'new'         => ['label' => 'جديد',            'color' => 'bg-blue-100 text-blue-700'],
            'in_progress' => ['label' => 'قيد المتابعة',     'color' => 'bg-amber-100 text-amber-700'],
            'converted'   => ['label' => 'تحول لعميل',       'color' => 'bg-green-100 text-green-700'],
            'archived'    => ['label' => 'مؤرشف',            'color' => 'bg-slate-100 text-slate-500'],
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_admin_id');
    }

    public function referredByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'referred_by_admin_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function callLogs(): HasMany
    {
        return $this->hasMany(LeadCallLog::class)->latest();
    }

    public function latestCallLog(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LeadCallLog::class)->latest();
    }
}
