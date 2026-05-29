<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContractStatusHistory extends Model
{
    use HasFactory;
    protected $fillable = ['contract_id', 'status', 'status_date', 'admin_id', 'whatsapp_message'];

    protected function casts(): array
    {
        return [
            'status_date' => 'date',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(RecruitmentContract::class, 'contract_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function getLabelAttribute(): string
    {
        return RecruitmentContract::statuses()[$this->status]['label'] ?? "مرحلة {$this->status}";
    }
}
