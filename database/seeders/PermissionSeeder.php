<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = array (
  0 =>
  array (
    'name' => 'عرض الفروع',
    'slug' => 'branches.view',
    'description' => NULL,
  ),
  1 =>
  array (
    'name' => 'إنشاء الفروع',
    'slug' => 'branches.create',
    'description' => NULL,
  ),
  2 =>
  array (
    'name' => 'تعديل الفروع',
    'slug' => 'branches.edit',
    'description' => NULL,
  ),
  3 =>
  array (
    'name' => 'حذف الفروع',
    'slug' => 'branches.delete',
    'description' => NULL,
  ),
  4 =>
  array (
    'name' => 'تفعيل/تعطيل الفروع',
    'slug' => 'branches.toggle',
    'description' => NULL,
  ),
  5 =>
  array (
    'name' => 'استعادة الفروع',
    'slug' => 'branches.restore',
    'description' => NULL,
  ),
  6 =>
  array (
    'name' => 'عرض أنواع الدخل',
    'slug' => 'income-types.view',
    'description' => NULL,
  ),
  7 =>
  array (
    'name' => 'إنشاء أنواع الدخل',
    'slug' => 'income-types.create',
    'description' => NULL,
  ),
  8 =>
  array (
    'name' => 'تعديل أنواع الدخل',
    'slug' => 'income-types.edit',
    'description' => NULL,
  ),
  9 =>
  array (
    'name' => 'حذف أنواع الدخل',
    'slug' => 'income-types.delete',
    'description' => NULL,
  ),
  10 =>
  array (
    'name' => 'تفعيل/تعطيل أنواع الدخل',
    'slug' => 'income-types.toggle',
    'description' => NULL,
  ),
  11 =>
  array (
    'name' => 'استعادة أنواع الدخل',
    'slug' => 'income-types.restore',
    'description' => NULL,
  ),
  12 =>
  array (
    'name' => 'عرض أنواع المصاريف',
    'slug' => 'expense-types.view',
    'description' => NULL,
  ),
  13 =>
  array (
    'name' => 'إنشاء أنواع المصاريف',
    'slug' => 'expense-types.create',
    'description' => NULL,
  ),
  14 =>
  array (
    'name' => 'تعديل أنواع المصاريف',
    'slug' => 'expense-types.edit',
    'description' => NULL,
  ),
  15 =>
  array (
    'name' => 'حذف أنواع المصاريف',
    'slug' => 'expense-types.delete',
    'description' => NULL,
  ),
  16 =>
  array (
    'name' => 'تفعيل/تعطيل أنواع المصاريف',
    'slug' => 'expense-types.toggle',
    'description' => NULL,
  ),
  17 =>
  array (
    'name' => 'استعادة أنواع المصاريف',
    'slug' => 'expense-types.restore',
    'description' => NULL,
  ),
  18 =>
  array (
    'name' => 'عرض الجنسيات',
    'slug' => 'nationalities.view',
    'description' => NULL,
  ),
  19 =>
  array (
    'name' => 'إنشاء الجنسيات',
    'slug' => 'nationalities.create',
    'description' => NULL,
  ),
  20 =>
  array (
    'name' => 'تعديل الجنسيات',
    'slug' => 'nationalities.edit',
    'description' => NULL,
  ),
  21 =>
  array (
    'name' => 'حذف الجنسيات',
    'slug' => 'nationalities.delete',
    'description' => NULL,
  ),
  22 =>
  array (
    'name' => 'تفعيل/تعطيل الجنسيات',
    'slug' => 'nationalities.toggle',
    'description' => NULL,
  ),
  23 =>
  array (
    'name' => 'استعادة الجنسيات',
    'slug' => 'nationalities.restore',
    'description' => NULL,
  ),
  24 =>
  array (
    'name' => 'عرض المطارات',
    'slug' => 'airports.view',
    'description' => NULL,
  ),
  25 =>
  array (
    'name' => 'إنشاء المطارات',
    'slug' => 'airports.create',
    'description' => NULL,
  ),
  26 =>
  array (
    'name' => 'تعديل المطارات',
    'slug' => 'airports.edit',
    'description' => NULL,
  ),
  27 =>
  array (
    'name' => 'حذف المطارات',
    'slug' => 'airports.delete',
    'description' => NULL,
  ),
  28 =>
  array (
    'name' => 'تفعيل/تعطيل المطارات',
    'slug' => 'airports.toggle',
    'description' => NULL,
  ),
  29 =>
  array (
    'name' => 'استعادة المطارات',
    'slug' => 'airports.restore',
    'description' => NULL,
  ),
  30 =>
  array (
    'name' => 'عرض الإيرادات',
    'slug' => 'incomes.view',
    'description' => NULL,
  ),
  31 =>
  array (
    'name' => 'إنشاء الإيرادات',
    'slug' => 'incomes.create',
    'description' => NULL,
  ),
  32 =>
  array (
    'name' => 'تعديل الإيرادات',
    'slug' => 'incomes.edit',
    'description' => NULL,
  ),
  33 =>
  array (
    'name' => 'حذف الإيرادات',
    'slug' => 'incomes.delete',
    'description' => NULL,
  ),
  34 =>
  array (
    'name' => 'تصدير الإيرادات',
    'slug' => 'incomes.export',
    'description' => NULL,
  ),
  35 =>
  array (
    'name' => 'استيراد الإيرادات',
    'slug' => 'incomes.import',
    'description' => NULL,
  ),
  36 =>
  array (
    'name' => 'استعادة الإيرادات',
    'slug' => 'incomes.restore',
    'description' => NULL,
  ),
  37 =>
  array (
    'name' => 'عرض المصاريف',
    'slug' => 'expenses.view',
    'description' => NULL,
  ),
  38 =>
  array (
    'name' => 'إنشاء المصاريف',
    'slug' => 'expenses.create',
    'description' => NULL,
  ),
  39 =>
  array (
    'name' => 'تعديل المصاريف',
    'slug' => 'expenses.edit',
    'description' => NULL,
  ),
  40 =>
  array (
    'name' => 'حذف المصاريف',
    'slug' => 'expenses.delete',
    'description' => NULL,
  ),
  41 =>
  array (
    'name' => 'موافقة على المصاريف',
    'slug' => 'expenses.approve',
    'description' => NULL,
  ),
  42 =>
  array (
    'name' => 'رفض المصاريف',
    'slug' => 'expenses.reject',
    'description' => NULL,
  ),
  43 =>
  array (
    'name' => 'تصدير المصاريف',
    'slug' => 'expenses.export',
    'description' => NULL,
  ),
  44 =>
  array (
    'name' => 'استيراد المصاريف',
    'slug' => 'expenses.import',
    'description' => NULL,
  ),
  45 =>
  array (
    'name' => 'استعادة المصاريف',
    'slug' => 'expenses.restore',
    'description' => NULL,
  ),
  46 =>
  array (
    'name' => 'عرض التحويلات',
    'slug' => 'transfers.view',
    'description' => NULL,
  ),
  47 =>
  array (
    'name' => 'إنشاء التحويلات',
    'slug' => 'transfers.create',
    'description' => NULL,
  ),
  48 =>
  array (
    'name' => 'تعديل التحويلات',
    'slug' => 'transfers.edit',
    'description' => NULL,
  ),
  49 =>
  array (
    'name' => 'حذف التحويلات',
    'slug' => 'transfers.delete',
    'description' => NULL,
  ),
  50 =>
  array (
    'name' => 'موافقة على التحويلات',
    'slug' => 'transfers.approve',
    'description' => NULL,
  ),
  51 =>
  array (
    'name' => 'رفض التحويلات',
    'slug' => 'transfers.reject',
    'description' => NULL,
  ),
  52 =>
  array (
    'name' => 'استعادة التحويلات',
    'slug' => 'transfers.restore',
    'description' => NULL,
  ),
  53 =>
  array (
    'name' => 'عرض التقارير',
    'slug' => 'reports.view',
    'description' => NULL,
  ),
  54 =>
  array (
    'name' => 'كشف حساب الفرع',
    'slug' => 'reports.branch-statement',
    'description' => NULL,
  ),
  55 =>
  array (
    'name' => 'قائمة الدخل بين الفروع',
    'slug' => 'reports.income-statement',
    'description' => NULL,
  ),
  56 =>
  array (
    'name' => 'تصدير كشف حساب الفرع',
    'slug' => 'reports.branch-statement.export',
    'description' => NULL,
  ),
  57 =>
  array (
    'name' => 'تصدير قائمة الدخل بين الفروع',
    'slug' => 'reports.income-statement.export',
    'description' => NULL,
  ),
  58 =>
  array (
    'name' => 'إحصائيات العقود',
    'slug' => 'reports.contracts-stats',
    'description' => NULL,
  ),
  59 =>
  array (
    'name' => 'تقرير العمالة المستلمة',
    'slug' => 'reports.contracts-received',
    'description' => NULL,
  ),
  60 =>
  array (
    'name' => 'تقرير العقود المتأخرة',
    'slug' => 'reports.contracts-delayed',
    'description' => NULL,
  ),
  61 =>
  array (
    'name' => 'إدارة المديرين',
    'slug' => 'admins.manage',
    'description' => NULL,
  ),
  62 =>
  array (
    'name' => 'تفعيل/تعطيل المديرين',
    'slug' => 'admins.toggle',
    'description' => NULL,
  ),
  63 =>
  array (
    'name' => 'استعادة المديرين',
    'slug' => 'admins.restore',
    'description' => NULL,
  ),
  64 =>
  array (
    'name' => 'إدارة الأدوار والصلاحيات',
    'slug' => 'roles.manage',
    'description' => NULL,
  ),
  65 =>
  array (
    'name' => 'عرض العملاء',
    'slug' => 'clients.view',
    'description' => NULL,
  ),
  66 =>
  array (
    'name' => 'إنشاء عميل',
    'slug' => 'clients.create',
    'description' => NULL,
  ),
  67 =>
  array (
    'name' => 'تعديل عميل',
    'slug' => 'clients.edit',
    'description' => NULL,
  ),
  68 =>
  array (
    'name' => 'حذف عميل',
    'slug' => 'clients.delete',
    'description' => NULL,
  ),
  69 =>
  array (
    'name' => 'عرض الوكلاء',
    'slug' => 'agents.view',
    'description' => NULL,
  ),
  70 =>
  array (
    'name' => 'إنشاء وكيل',
    'slug' => 'agents.create',
    'description' => NULL,
  ),
  71 =>
  array (
    'name' => 'تعديل وكيل',
    'slug' => 'agents.edit',
    'description' => NULL,
  ),
  72 =>
  array (
    'name' => 'حذف وكيل',
    'slug' => 'agents.delete',
    'description' => NULL,
  ),
  73 =>
  array (
    'name' => 'عرض العاملات',
    'slug' => 'workers.view',
    'description' => NULL,
  ),
  74 =>
  array (
    'name' => 'إضافة عاملة',
    'slug' => 'workers.create',
    'description' => NULL,
  ),
  75 =>
  array (
    'name' => 'تعديل عاملة',
    'slug' => 'workers.edit',
    'description' => NULL,
  ),
  76 =>
  array (
    'name' => 'حذف عاملة',
    'slug' => 'workers.delete',
    'description' => NULL,
  ),
  77 =>
  array (
    'name' => 'تعيين عاملة لعميل',
    'slug' => 'workers.assign',
    'description' => NULL,
  ),
  78 =>
  array (
    'name' => 'عرض عقود الاستقدام',
    'slug' => 'contracts.view',
    'description' => NULL,
  ),
  79 =>
  array (
    'name' => 'إنشاء عقد استقدام',
    'slug' => 'contracts.create',
    'description' => NULL,
  ),
  80 =>
  array (
    'name' => 'تعديل عقد استقدام',
    'slug' => 'contracts.edit',
    'description' => NULL,
  ),
  81 =>
  array (
    'name' => 'حذف عقد استقدام',
    'slug' => 'contracts.delete',
    'description' => NULL,
  ),
  82 =>
  array (
    'name' => 'عرض مباني السكن',
    'slug' => 'housings.view',
    'description' => NULL,
  ),
  83 =>
  array (
    'name' => 'إنشاء مبنى سكن',
    'slug' => 'housings.create',
    'description' => NULL,
  ),
  84 =>
  array (
    'name' => 'تعديل مبنى سكن',
    'slug' => 'housings.edit',
    'description' => NULL,
  ),
  85 =>
  array (
    'name' => 'حذف مبنى سكن',
    'slug' => 'housings.delete',
    'description' => NULL,
  ),
  86 =>
  array (
    'name' => 'تفعيل/تعطيل مباني السكن',
    'slug' => 'housings.toggle',
    'description' => NULL,
  ),
  87 =>
  array (
    'name' => 'استعادة مباني السكن',
    'slug' => 'housings.restore',
    'description' => NULL,
  ),
  88 =>
  array (
    'name' => 'عرض الشكاوي',
    'slug' => 'complaints.view',
    'description' => NULL,
  ),
  89 =>
  array (
    'name' => 'تسجيل شكوى',
    'slug' => 'complaints.create',
    'description' => NULL,
  ),
  90 =>
  array (
    'name' => 'تعديل شكوى',
    'slug' => 'complaints.edit',
    'description' => NULL,
  ),
  91 =>
  array (
    'name' => 'حذف شكوى',
    'slug' => 'complaints.delete',
    'description' => NULL,
  ),
  92 =>
  array (
    'name' => 'استعادة شكوى',
    'slug' => 'complaints.restore',
    'description' => NULL,
  ),
  93 =>
  array (
    'name' => 'عرض تقارير الشكاوي',
    'slug' => 'complaints.reports',
    'description' => NULL,
  ),
  94 =>
  array (
    'name' => 'عرض الحملات التسويقية',
    'slug' => 'campaigns.view',
    'description' => NULL,
  ),
  95 =>
  array (
    'name' => 'إنشاء حملة تسويقية',
    'slug' => 'campaigns.create',
    'description' => NULL,
  ),
  96 =>
  array (
    'name' => 'تعديل حملة تسويقية',
    'slug' => 'campaigns.edit',
    'description' => NULL,
  ),
  97 =>
  array (
    'name' => 'حذف حملة تسويقية',
    'slug' => 'campaigns.delete',
    'description' => NULL,
  ),
  98 =>
  array (
    'name' => 'استيراد عملاء من Google Sheets',
    'slug' => 'campaigns.import',
    'description' => NULL,
  ),
  99 =>
  array (
    'name' => 'عرض العملاء المحتملين',
    'slug' => 'leads.view',
    'description' => NULL,
  ),
  100 =>
  array (
    'name' => 'إنشاء عميل محتمل',
    'slug' => 'leads.create',
    'description' => NULL,
  ),
  101 =>
  array (
    'name' => 'تعديل عميل محتمل',
    'slug' => 'leads.edit',
    'description' => NULL,
  ),
  102 =>
  array (
    'name' => 'حذف عميل محتمل',
    'slug' => 'leads.delete',
    'description' => NULL,
  ),
  103 =>
  array (
    'name' => 'تسجيل مكالمة عميل محتمل',
    'slug' => 'leads.call',
    'description' => NULL,
  ),
  104 =>
  array (
    'name' => 'تحويل عميل محتمل لعميل فعلي',
    'slug' => 'leads.convert',
    'description' => NULL,
  ),
  105 =>
  array (
    'name' => 'عرض تقارير التسويق',
    'slug' => 'marketing.reports.view',
    'description' => NULL,
  ),
  106 =>
  array (
    'name' => 'عرض تعيينات السكن',
    'slug' => 'housing-assignments.view',
    'description' => NULL,
  ),
  107 =>
  array (
    'name' => 'إضافة تعيين سكن',
    'slug' => 'housing-assignments.create',
    'description' => NULL,
  ),
  108 =>
  array (
    'name' => 'تعديل تعيين سكن',
    'slug' => 'housing-assignments.edit',
    'description' => NULL,
  ),
  109 =>
  array (
    'name' => 'حذف تعيين سكن',
    'slug' => 'housing-assignments.delete',
    'description' => NULL,
  ),
  110 =>
  array (
    'name' => 'عرض الرحلات',
    'slug' => 'trips.view',
    'description' => NULL,
  ),
  111 =>
  array (
    'name' => 'إنشاء رحلة',
    'slug' => 'trips.create',
    'description' => NULL,
  ),
  112 =>
  array (
    'name' => 'تعديل رحلة',
    'slug' => 'trips.edit',
    'description' => NULL,
  ),
  113 =>
  array (
    'name' => 'حذف رحلة',
    'slug' => 'trips.delete',
    'description' => NULL,
  ),
  114 =>
  array (
    'name' => 'عرض عقود نقل الكفالة',
    'slug' => 'sponsorship-transfers.view',
    'description' => NULL,
  ),
  115 =>
  array (
    'name' => 'إنشاء عقد نقل كفالة',
    'slug' => 'sponsorship-transfers.create',
    'description' => NULL,
  ),
  116 =>
  array (
    'name' => 'تعديل عقد نقل كفالة',
    'slug' => 'sponsorship-transfers.edit',
    'description' => NULL,
  ),
  117 =>
  array (
    'name' => 'حذف عقد نقل كفالة',
    'slug' => 'sponsorship-transfers.delete',
    'description' => NULL,
  ),
  118 =>
  array (
    'name' => 'عرض التقويم',
    'slug' => 'calendar.view',
    'description' => NULL,
  ),
);

        $permissions = array_merge($permissions, [
            ['name' => 'عرض زيارات السكن', 'slug' => 'housing-visits.view', 'description' => null],
            ['name' => 'إنشاء زيارات السكن', 'slug' => 'housing-visits.create', 'description' => null],
            ['name' => 'تعديل زيارات السكن', 'slug' => 'housing-visits.edit', 'description' => null],
            ['name' => 'حذف زيارات السكن', 'slug' => 'housing-visits.delete', 'description' => null],
            ['name' => 'تقارير زيارات السكن', 'slug' => 'housing-visits.reports', 'description' => null],
        ]);

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                [
                    'name' => $permission['name'],
                    'description' => $permission['description'],
                ]
            );
        }
    }
}
