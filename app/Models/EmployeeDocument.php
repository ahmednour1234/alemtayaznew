<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeDocument extends Model
{
    use HasFactory, SoftDeletes;

    /** Files are stored on the private "local" disk and never publicly served. */
    public const DISK = 'local';

    protected $fillable = [
        'employee_id', 'title', 'doc_type', 'file_path', 'original_name',
        'mime_type', 'size', 'issue_date', 'expiry_date', 'notes',
        'branch_id', 'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'issue_date'  => 'date',
            'expiry_date' => 'date',
            'size'        => 'integer',
        ];
    }

    public function getSizeLabelAttribute(): string
    {
        $bytes = (int) $this->size;
        if ($bytes <= 0) {
            return '—';
        }
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = (int) floor(log($bytes, 1024));
        $i = min($i, count($units) - 1);
        return round($bytes / (1024 ** $i), 1) . ' ' . $units[$i];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
