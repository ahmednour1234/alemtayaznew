<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $permissions = [
        // ── التقارير (reports) ─────────────────────────────────────────────────
        ['name' => 'عرض التقارير',                       'slug' => 'reports.view'],
        ['name' => 'كشف حساب الفرع',                     'slug' => 'reports.branch-statement'],
        ['name' => 'تصدير كشف حساب الفرع',               'slug' => 'reports.branch-statement.export'],
        ['name' => 'قائمة الدخل بين الفروع',             'slug' => 'reports.income-statement'],
        ['name' => 'تصدير قائمة الدخل بين الفروع',       'slug' => 'reports.income-statement.export'],
        ['name' => 'إحصائيات العقود',                    'slug' => 'reports.contracts-stats'],
        ['name' => 'تقرير العمالة المستلمة',              'slug' => 'reports.contracts-received'],
        ['name' => 'تقرير العقود المتأخرة',              'slug' => 'reports.contracts-delayed'],
        // ── لوحة الفروع (leads-board) ─────────────────────────────────────────
        ['name' => 'عرض لوحة الفروع',                    'slug' => 'leads-board.view'],
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
