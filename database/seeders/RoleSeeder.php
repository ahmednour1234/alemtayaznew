<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. مدير عام — كل الصلاحيات ───────────────────────────────────────
        $superAdmin = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            ['name' => 'مدير عام', 'description' => 'صلاحيات كاملة بدون قيود', 'active' => true]
        );
        $superAdmin->permissions()->sync(Permission::pluck('id'));

        // ── helpers ────────────────────────────────────────────────────────────
        $perms = fn(array $slugs) => Permission::whereIn('slug', $slugs)->pluck('id');

        // ── 2. الإعدادات العامة ────────────────────────────────────────────────
        Role::firstOrCreate(
            ['slug' => 'settings-manager'],
            ['name' => 'مدير الإعدادات', 'description' => 'إدارة إعدادات النظام والفروع والمستخدمين', 'active' => true]
        )->permissions()->sync($perms([
            'admins.manage', 'admins.toggle', 'admins.restore',
            'roles.manage',
            'branches.view', 'branches.create', 'branches.edit', 'branches.delete', 'branches.toggle', 'branches.restore',
            'nationalities.view', 'nationalities.create', 'nationalities.edit', 'nationalities.delete', 'nationalities.toggle', 'nationalities.restore',
            'airports.view', 'airports.create', 'airports.edit', 'airports.delete', 'airports.toggle', 'airports.restore',
            'income-types.view', 'income-types.create', 'income-types.edit', 'income-types.delete', 'income-types.toggle', 'income-types.restore',
            'expense-types.view', 'expense-types.create', 'expense-types.edit', 'expense-types.delete', 'expense-types.toggle', 'expense-types.restore',
            'housings.view', 'housings.create', 'housings.edit', 'housings.delete', 'housings.toggle', 'housings.restore',
        ]));

        // ── 3. القسم المالي ────────────────────────────────────────────────────
        Role::firstOrCreate(
            ['slug' => 'finance-manager'],
            ['name' => 'مدير مالي', 'description' => 'إدارة الإيرادات والمصاريف والتحويلات والتقارير المالية', 'active' => true]
        )->permissions()->sync($perms([
            'incomes.view', 'incomes.create', 'incomes.edit', 'incomes.delete', 'incomes.export', 'incomes.import', 'incomes.restore',
            'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete', 'expenses.approve', 'expenses.reject', 'expenses.export', 'expenses.import', 'expenses.restore',
            'transfers.view', 'transfers.create', 'transfers.edit', 'transfers.delete', 'transfers.approve', 'transfers.reject', 'transfers.restore',
            'reports.view', 'reports.branch-statement', 'reports.branch-statement.export',
            'reports.income-statement', 'reports.income-statement.export',
            'reports.contracts-stats', 'reports.contracts-received', 'reports.contracts-delayed',
            'branches.view',
        ]));

        // ── 4. العملاء والوكلاء ────────────────────────────────────────────────
        Role::firstOrCreate(
            ['slug' => 'clients-manager'],
            ['name' => 'مدير العملاء والوكلاء', 'description' => 'إدارة العملاء والوكلاء', 'active' => true]
        )->permissions()->sync($perms([
            'clients.view', 'clients.create', 'clients.edit', 'clients.delete',
            'agents.view', 'agents.create', 'agents.edit', 'agents.delete',
            'branches.view',
        ]));

        // ── 5. التسويق ─────────────────────────────────────────────────────────
        Role::firstOrCreate(
            ['slug' => 'marketing-manager'],
            ['name' => 'مدير التسويق', 'description' => 'إدارة الحملات التسويقية والعملاء المحتملين', 'active' => true]
        )->permissions()->sync($perms([
            'campaigns.view', 'campaigns.create', 'campaigns.edit', 'campaigns.delete', 'campaigns.import',
            'leads.view', 'leads.create', 'leads.edit', 'leads.delete', 'leads.call', 'leads.convert',
            'marketing.reports.view',
            'calendar.view',
            'clients.view', 'clients.create',
            'branches.view',
        ]));

        // ── 6. الشكاوى ─────────────────────────────────────────────────────────
        Role::firstOrCreate(
            ['slug' => 'complaints-manager'],
            ['name' => 'مدير الشكاوى', 'description' => 'متابعة وإدارة شكاوى العملاء والعاملات', 'active' => true]
        )->permissions()->sync($perms([
            'complaints.view', 'complaints.create', 'complaints.edit', 'complaints.delete', 'complaints.restore', 'complaints.reports',
            'workers.view',
            'clients.view',
            'branches.view',
        ]));

        // ── 7. الاستلام والنقل (الرحلات + السكن) ──────────────────────────────
        Role::firstOrCreate(
            ['slug' => 'transport-manager'],
            ['name' => 'مدير الاستلام والنقل', 'description' => 'إدارة رحلات الاستلام والنقل وتعيينات السكن', 'active' => true]
        )->permissions()->sync($perms([
            'trips.view', 'trips.create', 'trips.edit', 'trips.delete',
            'housing-assignments.view', 'housing-assignments.create', 'housing-assignments.edit', 'housing-assignments.delete',
            'housings.view',
            'workers.view',
            'airports.view',
            'branches.view',
        ]));

        // ── 8. نقل الكفالة ─────────────────────────────────────────────────────
        Role::firstOrCreate(
            ['slug' => 'sponsorship-transfer-manager'],
            ['name' => 'مدير نقل الكفالة', 'description' => 'إدارة عقود نقل الكفالة ومتابعتها', 'active' => true]
        )->permissions()->sync($perms([
            'sponsorship-transfers.view', 'sponsorship-transfers.create', 'sponsorship-transfers.edit', 'sponsorship-transfers.delete',
            'workers.view',
            'clients.view',
            'branches.view',
        ]));

        // ── 9. العاملات (CVS) ──────────────────────────────────────────────────
        Role::firstOrCreate(
            ['slug' => 'workers-manager'],
            ['name' => 'مدير العاملات', 'description' => 'إدارة ملفات العاملات وتعيينهن للعملاء', 'active' => true]
        )->permissions()->sync($perms([
            'workers.view', 'workers.create', 'workers.edit', 'workers.delete', 'workers.assign',
            'nationalities.view',
            'clients.view',
            'branches.view',
        ]));

        // ── 10. عقود الاستقدام ─────────────────────────────────────────────────
        Role::firstOrCreate(
            ['slug' => 'contracts-manager'],
            ['name' => 'مدير عقود الاستقدام', 'description' => 'إدارة عقود الاستقدام ومتابعة العمالة', 'active' => true]
        )->permissions()->sync($perms([
            'contracts.view', 'contracts.create', 'contracts.edit', 'contracts.delete',
            'workers.view', 'workers.create', 'workers.edit', 'workers.assign',
            'clients.view',
            'agents.view',
            'reports.contracts-stats', 'reports.contracts-received', 'reports.contracts-delayed',
            'branches.view',
        ]));

        // ── (legacy) محاسب ─────────────────────────────────────────────────────
        Role::firstOrCreate(
            ['slug' => 'accountant'],
            ['name' => 'محاسب', 'description' => 'إدارة الإيرادات والمصاريف', 'active' => true]
        )->permissions()->sync($perms([
            'branches.view', 'incomes.view', 'incomes.create', 'incomes.edit',
            'expenses.view', 'expenses.create', 'expenses.edit',
            'transfers.view', 'reports.view',
            'reports.contracts-stats', 'reports.contracts-received', 'reports.contracts-delayed',
        ]));

        // ── (legacy) مدير فرع ──────────────────────────────────────────────────
        Role::firstOrCreate(
            ['slug' => 'branch-manager'],
            ['name' => 'مدير فرع', 'description' => 'إدارة الفرع', 'active' => true]
        )->permissions()->sync($perms([
            'branches.view', 'incomes.view', 'incomes.create',
            'expenses.view', 'expenses.create', 'transfers.view', 'transfers.create',
            'reports.view', 'reports.branch-statement', 'reports.income-statement',
            'reports.contracts-stats', 'reports.contracts-received', 'reports.contracts-delayed',
            'contracts.view', 'contracts.edit',
            'campaigns.view', 'campaigns.create', 'campaigns.edit', 'campaigns.import',
            'leads.view', 'leads.create', 'leads.edit', 'leads.call', 'leads.convert',
            'marketing.reports.view',
        ]));
    }
}

