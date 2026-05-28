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
            ['name' => 'عرض الفروع',          'slug' => 'branches.view'],
            ['name' => 'إنشاء الفروع',        'slug' => 'branches.create'],
            ['name' => 'تعديل الفروع',        'slug' => 'branches.edit'],
            ['name' => 'حذف الفروع',          'slug' => 'branches.delete'],
            ['name' => 'تفعيل/تعطيل الفروع', 'slug' => 'branches.toggle'],
            ['name' => 'استعادة الفروع',      'slug' => 'branches.restore'],
            // Income Types
            ['name' => 'عرض أنواع الدخل',            'slug' => 'income-types.view'],
            ['name' => 'إنشاء أنواع الدخل',          'slug' => 'income-types.create'],
            ['name' => 'تعديل أنواع الدخل',          'slug' => 'income-types.edit'],
            ['name' => 'حذف أنواع الدخل',            'slug' => 'income-types.delete'],
            ['name' => 'تفعيل/تعطيل أنواع الدخل',   'slug' => 'income-types.toggle'],
            ['name' => 'استعادة أنواع الدخل',        'slug' => 'income-types.restore'],
            // Expense Types
            ['name' => 'عرض أنواع المصاريف',          'slug' => 'expense-types.view'],
            ['name' => 'إنشاء أنواع المصاريف',        'slug' => 'expense-types.create'],
            ['name' => 'تعديل أنواع المصاريف',        'slug' => 'expense-types.edit'],
            ['name' => 'حذف أنواع المصاريف',          'slug' => 'expense-types.delete'],
            ['name' => 'تفعيل/تعطيل أنواع المصاريف', 'slug' => 'expense-types.toggle'],
            ['name' => 'استعادة أنواع المصاريف',      'slug' => 'expense-types.restore'],
            // Nationalities
            ['name' => 'عرض الجنسيات',          'slug' => 'nationalities.view'],
            ['name' => 'إنشاء الجنسيات',        'slug' => 'nationalities.create'],
            ['name' => 'تعديل الجنسيات',        'slug' => 'nationalities.edit'],
            ['name' => 'حذف الجنسيات',          'slug' => 'nationalities.delete'],
            ['name' => 'تفعيل/تعطيل الجنسيات', 'slug' => 'nationalities.toggle'],
            ['name' => 'استعادة الجنسيات',      'slug' => 'nationalities.restore'],
            // Airports
            ['name' => 'عرض المطارات',          'slug' => 'airports.view'],
            ['name' => 'إنشاء المطارات',        'slug' => 'airports.create'],
            ['name' => 'تعديل المطارات',        'slug' => 'airports.edit'],
            ['name' => 'حذف المطارات',          'slug' => 'airports.delete'],
            ['name' => 'تفعيل/تعطيل المطارات', 'slug' => 'airports.toggle'],
            ['name' => 'استعادة المطارات',      'slug' => 'airports.restore'],
            // Incomes
            ['name' => 'عرض الإيرادات',     'slug' => 'incomes.view'],
            ['name' => 'إنشاء الإيرادات',   'slug' => 'incomes.create'],
            ['name' => 'تعديل الإيرادات',   'slug' => 'incomes.edit'],
            ['name' => 'حذف الإيرادات',     'slug' => 'incomes.delete'],
            ['name' => 'تصدير الإيرادات',   'slug' => 'incomes.export'],
            ['name' => 'استيراد الإيرادات', 'slug' => 'incomes.import'],
            ['name' => 'استعادة الإيرادات', 'slug' => 'incomes.restore'],
            // Expenses
            ['name' => 'عرض المصاريف',        'slug' => 'expenses.view'],
            ['name' => 'إنشاء المصاريف',      'slug' => 'expenses.create'],
            ['name' => 'تعديل المصاريف',      'slug' => 'expenses.edit'],
            ['name' => 'حذف المصاريف',        'slug' => 'expenses.delete'],
            ['name' => 'موافقة على المصاريف', 'slug' => 'expenses.approve'],
            ['name' => 'رفض المصاريف',        'slug' => 'expenses.reject'],
            ['name' => 'تصدير المصاريف',      'slug' => 'expenses.export'],
            ['name' => 'استيراد المصاريف',    'slug' => 'expenses.import'],
            ['name' => 'استعادة المصاريف',    'slug' => 'expenses.restore'],
            // Transfers
            ['name' => 'عرض التحويلات',        'slug' => 'transfers.view'],
            ['name' => 'إنشاء التحويلات',      'slug' => 'transfers.create'],
            ['name' => 'تعديل التحويلات',      'slug' => 'transfers.edit'],
            ['name' => 'حذف التحويلات',        'slug' => 'transfers.delete'],
            ['name' => 'موافقة على التحويلات', 'slug' => 'transfers.approve'],
            ['name' => 'رفض التحويلات',        'slug' => 'transfers.reject'],
            ['name' => 'استعادة التحويلات',    'slug' => 'transfers.restore'],
            // Reports
            ['name' => 'عرض التقارير',                 'slug' => 'reports.view'],
            ['name' => 'كشف حساب الفرع',               'slug' => 'reports.branch-statement'],
            ['name' => 'قائمة الدخل بين الفروع',       'slug' => 'reports.income-statement'],
            ['name' => 'تصدير كشف حساب الفرع',         'slug' => 'reports.branch-statement.export'],
            ['name' => 'تصدير قائمة الدخل بين الفروع', 'slug' => 'reports.income-statement.export'],
            ['name' => 'إحصائيات العقود',            'slug' => 'reports.contracts-stats'],
            ['name' => 'تقرير العمالة المستلمة',       'slug' => 'reports.contracts-received'],
            ['name' => 'تقرير العقود المتأخرة',        'slug' => 'reports.contracts-delayed'],
            // Settings / Admins
            ['name' => 'إدارة المديرين',              'slug' => 'admins.manage'],
            ['name' => 'تفعيل/تعطيل المديرين',        'slug' => 'admins.toggle'],
            ['name' => 'استعادة المديرين',             'slug' => 'admins.restore'],
            // Roles
            ['name' => 'إدارة الأدوار والصلاحيات',   'slug' => 'roles.manage'],
            // Clients
            ['name' => 'عرض العملاء',   'slug' => 'clients.view'],
            ['name' => 'إنشاء عميل',    'slug' => 'clients.create'],
            ['name' => 'تعديل عميل',    'slug' => 'clients.edit'],
            ['name' => 'حذف عميل',      'slug' => 'clients.delete'],
            // Agents
            ['name' => 'عرض الوكلاء',   'slug' => 'agents.view'],
            ['name' => 'إنشاء وكيل',    'slug' => 'agents.create'],
            ['name' => 'تعديل وكيل',    'slug' => 'agents.edit'],
            ['name' => 'حذف وكيل',      'slug' => 'agents.delete'],
            // Workers
            ['name' => 'عرض العاملات',         'slug' => 'workers.view'],
            ['name' => 'إضافة عاملة',          'slug' => 'workers.create'],
            ['name' => 'تعديل عاملة',          'slug' => 'workers.edit'],
            ['name' => 'حذف عاملة',            'slug' => 'workers.delete'],
            ['name' => 'تعيين عاملة لعميل',    'slug' => 'workers.assign'],
            // Contracts
            ['name' => 'عرض عقود الاستقدام',   'slug' => 'contracts.view'],
            ['name' => 'إنشاء عقد استقدام',    'slug' => 'contracts.create'],
            ['name' => 'تعديل عقد استقدام',    'slug' => 'contracts.edit'],
            ['name' => 'حذف عقد استقدام',      'slug' => 'contracts.delete'],
            // Housing
            ['name' => 'عرض مباني السكن',           'slug' => 'housings.view'],
            ['name' => 'إنشاء مبنى سكن',           'slug' => 'housings.create'],
            ['name' => 'تعديل مبنى سكن',           'slug' => 'housings.edit'],
            ['name' => 'حذف مبنى سكن',             'slug' => 'housings.delete'],
            ['name' => 'تفعيل/تعطيل مباني السكن', 'slug' => 'housings.toggle'],
            ['name' => 'استعادة مباني السكن',     'slug' => 'housings.restore'],
            // Complaints
            ['name' => 'عرض الشكاوي',                'slug' => 'complaints.view'],
            ['name' => 'تسجيل شكوى',                  'slug' => 'complaints.create'],
            ['name' => 'تعديل شكوى',                 'slug' => 'complaints.edit'],
            ['name' => 'حذف شكوى',                   'slug' => 'complaints.delete'],
            ['name' => 'استعادة شكوى',                'slug' => 'complaints.restore'],
            ['name' => 'عرض تقارير الشكاوي',         'slug' => 'complaints.reports'],
            // Marketing — Campaigns
            ['name' => 'عرض الحملات التسويقية',     'slug' => 'campaigns.view'],
            ['name' => 'إنشاء حملة تسويقية',         'slug' => 'campaigns.create'],
            ['name' => 'تعديل حملة تسويقية',         'slug' => 'campaigns.edit'],
            ['name' => 'حذف حملة تسويقية',           'slug' => 'campaigns.delete'],
            ['name' => 'استيراد عملاء من Google Sheets', 'slug' => 'campaigns.import'],
            // Marketing — Leads
            ['name' => 'عرض العملاء المحتملين',       'slug' => 'leads.view'],
            ['name' => 'إنشاء عميل محتمل',             'slug' => 'leads.create'],
            ['name' => 'تعديل عميل محتمل',             'slug' => 'leads.edit'],
            ['name' => 'حذف عميل محتمل',               'slug' => 'leads.delete'],
            ['name' => 'تسجيل مكالمة عميل محتمل',     'slug' => 'leads.call'],
            ['name' => 'تحويل عميل محتمل لعميل فعلي', 'slug' => 'leads.convert'],
            // Marketing — Reports
            ['name' => 'عرض تقارير التسويق', 'slug' => 'marketing.reports.view'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['slug' => $p['slug']], ['name' => $p['name']]);
        }
    }
}
