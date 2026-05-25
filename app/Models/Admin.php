<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'active'])]
#[Hidden(['password', 'remember_token'])]
class Admin extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected function casts(): array
    {
        return [
            'active'    => 'boolean',
            'password'  => 'hashed',
        ];
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
