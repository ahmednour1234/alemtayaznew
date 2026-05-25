<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Branches
            ['name' => 'عرض الفروع',      'slug' => 'branches.view'],
            ['name' => 'إنشاء الفروع',    'slug' => 'branches.create'],
            ['name' => 'تعديل الفروع',    'slug' => 'branches.edit'],
            ['name' => 'حذف الفروع',      'slug' => 'branches.delete'],
            // Income Types
            ['name' => 'عرض أنواع الدخل',    'slug' => 'income-types.view'],
            ['name' => 'إنشاء أنواع الدخل',  'slug' => 'income-types.create'],
            ['name' => 'تعديل أنواع الدخل',  'slug' => 'income-types.edit'],
            ['name' => 'حذف أنواع الدخل',    'slug' => 'income-types.delete'],
            // Expense Types
            ['name' => 'عرض أنواع المصاريف',    'slug' => 'expense-types.view'],
            ['name' => 'إنشاء أنواع المصاريف',  'slug' => 'expense-types.create'],
            ['name' => 'تعديل أنواع المصاريف',  'slug' => 'expense-types.edit'],
            ['name' => 'حذف أنواع المصاريف',    'slug' => 'expense-types.delete'],
            // Incomes
            ['name' => 'عرض الإيرادات',     'slug' => 'incomes.view'],
            ['name' => 'إنشاء الإيرادات',   'slug' => 'incomes.create'],
            ['name' => 'تعديل الإيرادات',   'slug' => 'incomes.edit'],
            ['name' => 'حذف الإيرادات',     'slug' => 'incomes.delete'],
            ['name' => 'تصدير الإيرادات',   'slug' => 'incomes.export'],
            ['name' => 'استيراد الإيرادات', 'slug' => 'incomes.import'],
            // Expenses
            ['name' => 'عرض المصاريف',        'slug' => 'expenses.view'],
            ['name' => 'إنشاء المصاريف',      'slug' => 'expenses.create'],
            ['name' => 'تعديل المصاريف',      'slug' => 'expenses.edit'],
            ['name' => 'حذف المصاريف',        'slug' => 'expenses.delete'],
            ['name' => 'موافقة على المصاريف', 'slug' => 'expenses.approve'],
            ['name' => 'تصدير المصاريف',      'slug' => 'expenses.export'],
            ['name' => 'استيراد المصاريف',    'slug' => 'expenses.import'],
            // Transfers
            ['name' => 'عرض التحويلات',        'slug' => 'transfers.view'],
            ['name' => 'إنشاء التحويلات',      'slug' => 'transfers.create'],
            ['name' => 'تعديل التحويلات',      'slug' => 'transfers.edit'],
            ['name' => 'حذف التحويلات',        'slug' => 'transfers.delete'],
            ['name' => 'موافقة على التحويلات', 'slug' => 'transfers.approve'],
            // Reports
            ['name' => 'عرض التقارير', 'slug' => 'reports.view'],
            // Settings / Admins
            ['name' => 'إدارة المديرين',    'slug' => 'admins.manage'],
            ['name' => 'إدارة الأدوار',     'slug' => 'roles.manage'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['slug' => $p['slug']], ['name' => $p['name']]);
        }
    }
}
