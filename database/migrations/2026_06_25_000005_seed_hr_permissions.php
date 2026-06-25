<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $permissions = [
        // ── الموظفين (employees) ───────────────────────────────────────────────
        ['name' => 'عرض الموظفين',          'slug' => 'employees.view'],
        ['name' => 'إضافة موظف',            'slug' => 'employees.create'],
        ['name' => 'تعديل موظف',            'slug' => 'employees.edit'],
        ['name' => 'حذف موظف',              'slug' => 'employees.delete'],

        // ── وثائق الموظفين/الشركة (employee documents) ─────────────────────────
        ['name' => 'عرض وثائق الموظفين',     'slug' => 'employee-documents.view'],
        ['name' => 'رفع وثيقة موظف',         'slug' => 'employee-documents.create'],
        ['name' => 'تعديل وثيقة موظف',       'slug' => 'employee-documents.edit'],
        ['name' => 'حذف وثيقة موظف',         'slug' => 'employee-documents.delete'],
        ['name' => 'تحميل وثيقة موظف',       'slug' => 'employee-documents.download'],

        // ── إجازات الموظفين (employee leaves) ──────────────────────────────────
        ['name' => 'عرض الإجازات',           'slug' => 'employee-leaves.view'],
        ['name' => 'إضافة إجازة',            'slug' => 'employee-leaves.create'],
        ['name' => 'تعديل إجازة',            'slug' => 'employee-leaves.edit'],
        ['name' => 'حذف إجازة',              'slug' => 'employee-leaves.delete'],
        ['name' => 'اعتماد/رفض إجازة',       'slug' => 'employee-leaves.approve'],

        // ── التأمين الطبي (medical insurance) ──────────────────────────────────
        ['name' => 'عرض التأمين الطبي',      'slug' => 'employee-insurances.view'],
        ['name' => 'إضافة تأمين طبي',        'slug' => 'employee-insurances.create'],
        ['name' => 'تعديل تأمين طبي',        'slug' => 'employee-insurances.edit'],
        ['name' => 'حذف تأمين طبي',          'slug' => 'employee-insurances.delete'],
    ];

    public function up(): void
    {
        foreach ($this->permissions as $perm) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $perm['slug']],
                ['name' => $perm['name'], 'slug' => $perm['slug'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        $slugs = array_column($this->permissions, 'slug');
        DB::table('permissions')->whereIn('slug', $slugs)->delete();
    }
};
