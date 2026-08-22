<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

/**
 * صلاحيات شاشة «طلبات الموقع».
 *
 * تُزرع عبر هجرة لا عبر السيدر وحده، لتصل إلى قواعد البيانات المنشورة
 * دون إعادة تشغيل السيدرات — وهو النمط المتبع في المشروع.
 */
return new class extends Migration
{
    private const PERMISSIONS = [
        ['slug' => 'website-leads.view',   'name' => 'عرض طلبات الموقع'],
        ['slug' => 'website-leads.assign', 'name' => 'إسناد طلبات الموقع'],
    ];

    public function up(): void
    {
        $ids = [];

        foreach (self::PERMISSIONS as $p) {
            $perm = Permission::updateOrCreate(
                ['slug' => $p['slug']],
                ['name' => $p['name'], 'description' => null]
            );
            $ids[] = $perm->id;
        }

        // نمنحها لكل دور يملك أصلاً صلاحية عرض العملاء المحتملين.
        // الربط بالصلاحية لا بأسماء الأدوار، لأن أسماء الأدوار تختلف بين
        // البيئات وبعضها بالعربية — فالنتيجة تبقى صحيحة أياً كان التسمية.
        Role::whereHas('permissions', fn ($q) => $q->where('slug', 'leads.view'))
            ->get()
            ->each(fn (Role $role) => $role->permissions()->syncWithoutDetaching($ids));
    }

    public function down(): void
    {
        Permission::whereIn('slug', array_column(self::PERMISSIONS, 'slug'))->delete();
    }
};
