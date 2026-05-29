<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    protected $fillable = [
        'branch_id', 'expense_type_id', 'admin_id', 'approved_by',
        'amount', 'date', 'payment_method', 'status',
        'reference_number', 'description', 'attachment',
        'rejection_reason', 'approved_at',
    ];
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'date'        => 'date',
            'amount'      => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function expenseType(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
