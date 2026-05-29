<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'description', 'active'];
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
