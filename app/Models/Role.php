<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug', 'description', 'active'])]
class Role extends Model
{
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'admin_role');
    }
}
