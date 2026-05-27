<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'active', 'branch_id', 'department'];

    public static function departments(): array
    {
        return [
            'customer_service' => 'قسم خدمة عملاء',
            'coordination'     => 'قسم تنسيق',
            'accounts'         => 'قسم حسابات',
            'accountant'       => 'محاسب عام',
            'branch_manager'   => 'مدير فرع',
            'chairman'         => 'رئيس مجلس إدارة',
        ];
    }

    public function getDepartmentLabelAttribute(): string
    {
        return self::departments()[$this->department] ?? $this->department ?? '—';
    }
    protected $hidden   = ['password', 'remember_token'];

    use HasFactory, Notifiable, SoftDeletes;

    protected function casts(): array
    {
        return [
            'active'    => 'boolean',
            'password'  => 'hashed',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /** True if this admin is scoped to a single branch (not super-admin). */
    public function isBranchAdmin(): bool
    {
        return $this->branch_id !== null && ! $this->isSuperAdmin();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'admin_role');
    }

    public function hasPermission(string $slug): bool
    {
        foreach ($this->roles as $role) {
            if ($role->active && $role->permissions->contains('slug', $slug)) {
                return true;
            }
        }
        return false;
    }

    public function isSuperAdmin(): bool
    {
        return $this->roles->where('slug', 'super-admin')->isNotEmpty();
    }
}
