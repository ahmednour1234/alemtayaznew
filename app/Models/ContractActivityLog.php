<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContractActivityLog extends Model
{
    use HasFactory;
    protected $fillable = ['contract_id', 'admin_id', 'action', 'section', 'label'];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(RecruitmentContract::class);
    }

    public function sectionLabel(): string
    {
        return match ($this->section) {
            'customer_service' => 'خدمة العملاء',
            'accounts'         => 'الحسابات',
            'accountant'       => 'الحسابات',
            'coordination'     => 'التنسيق',
            'branch_manager'   => 'مدير الفرع',
            'chairman'         => 'رئيس مجلس الإدارة',
            default            => 'الإدارة',
        };
    }

    public function actionIcon(): string
    {
        return match ($this->action) {
            'created'        => 'M12 4v16m8-8H4',
            'updated'        => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
            'status_changed' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            default          => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        };
    }

    public function actionColor(): string
    {
        return match ($this->action) {
            'created'        => 'text-blue-600 bg-blue-50',
            'updated'        => 'text-amber-600 bg-amber-50',
            'status_changed' => 'text-green-600 bg-green-50',
            default          => 'text-slate-600 bg-slate-50',
        };
    }
}
