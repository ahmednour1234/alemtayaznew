<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            ['name' => 'مدير عام', 'description' => 'صلاحيات كاملة', 'active' => true]
        );
        $superAdmin->permissions()->sync(Permission::pluck('id'));

        Role::firstOrCreate(
            ['slug' => 'accountant'],
            ['name' => 'محاسب', 'description' => 'إدارة الإيرادات والمصاريف', 'active' => true]
        )->permissions()->sync(
            Permission::whereIn('slug', [
                'branches.view', 'incomes.view', 'incomes.create', 'incomes.edit',
                'expenses.view', 'expenses.create', 'expenses.edit',
                'transfers.view', 'reports.view',
            ])->pluck('id')
        );

        Role::firstOrCreate(
            ['slug' => 'branch-manager'],
            ['name' => 'مدير فرع', 'description' => 'إدارة الفرع', 'active' => true]
        )->permissions()->sync(
            Permission::whereIn('slug', [
                'branches.view', 'incomes.view', 'incomes.create',
                'expenses.view', 'expenses.create', 'transfers.view', 'transfers.create',
            ])->pluck('id')
        );
    }
}
