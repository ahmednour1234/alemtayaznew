<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = array (
  0 =>
  array (
    'name' => 'مدير عام',
    'slug' => 'super-admin',
    'description' => 'صلاحيات كاملة بدون قيود',
    'active' => true,
    'permissions' =>
    array (
      0 => 'admins.manage',
      1 => 'admins.restore',
      2 => 'admins.toggle',
      3 => 'agents.create',
      4 => 'agents.delete',
      5 => 'agents.edit',
      6 => 'agents.view',
      7 => 'airports.create',
      8 => 'airports.delete',
      9 => 'airports.edit',
      10 => 'airports.restore',
      11 => 'airports.toggle',
      12 => 'airports.view',
      13 => 'branches.create',
      14 => 'branches.delete',
      15 => 'branches.edit',
      16 => 'branches.restore',
      17 => 'branches.toggle',
      18 => 'branches.view',
      19 => 'calendar.view',
      20 => 'campaigns.create',
      21 => 'campaigns.delete',
      22 => 'campaigns.edit',
      23 => 'campaigns.import',
      24 => 'campaigns.view',
      25 => 'clients.create',
      26 => 'clients.delete',
      27 => 'clients.edit',
      28 => 'clients.view',
      29 => 'complaints.create',
      30 => 'complaints.delete',
      31 => 'complaints.edit',
      32 => 'complaints.reports',
      33 => 'complaints.restore',
      34 => 'complaints.view',
      35 => 'contracts.create',
      36 => 'contracts.delete',
      37 => 'contracts.edit',
      38 => 'contracts.view',
      39 => 'expense-types.create',
      40 => 'expense-types.delete',
      41 => 'expense-types.edit',
      42 => 'expense-types.restore',
      43 => 'expense-types.toggle',
      44 => 'expense-types.view',
      45 => 'expenses.approve',
      46 => 'expenses.create',
      47 => 'expenses.delete',
      48 => 'expenses.edit',
      49 => 'expenses.export',
      50 => 'expenses.import',
      51 => 'expenses.reject',
      52 => 'expenses.restore',
      53 => 'expenses.view',
      54 => 'housing-assignments.create',
      55 => 'housing-assignments.delete',
      56 => 'housing-assignments.edit',
      57 => 'housing-assignments.view',
      58 => 'housings.create',
      59 => 'housings.delete',
      60 => 'housings.edit',
      61 => 'housings.restore',
      62 => 'housings.toggle',
      63 => 'housings.view',
      64 => 'income-types.create',
      65 => 'income-types.delete',
      66 => 'income-types.edit',
      67 => 'income-types.restore',
      68 => 'income-types.toggle',
      69 => 'income-types.view',
      70 => 'incomes.create',
      71 => 'incomes.delete',
      72 => 'incomes.edit',
      73 => 'incomes.export',
      74 => 'incomes.import',
      75 => 'incomes.restore',
      76 => 'incomes.view',
      77 => 'leads.call',
      78 => 'leads.convert',
      79 => 'leads.create',
      80 => 'leads.delete',
      81 => 'leads.edit',
      82 => 'leads.view',
      83 => 'marketing.reports.view',
      84 => 'nationalities.create',
      85 => 'nationalities.delete',
      86 => 'nationalities.edit',
      87 => 'nationalities.restore',
      88 => 'nationalities.toggle',
      89 => 'nationalities.view',
      90 => 'reports.branch-statement',
      91 => 'reports.branch-statement.export',
      92 => 'reports.contracts-delayed',
      93 => 'reports.contracts-received',
      94 => 'reports.contracts-stats',
      95 => 'reports.income-statement',
      96 => 'reports.income-statement.export',
      97 => 'reports.view',
      98 => 'roles.manage',
      99 => 'sponsorship-transfers.create',
      100 => 'sponsorship-transfers.delete',
      101 => 'sponsorship-transfers.edit',
      102 => 'sponsorship-transfers.view',
      103 => 'transfers.approve',
      104 => 'transfers.create',
      105 => 'transfers.delete',
      106 => 'transfers.edit',
      107 => 'transfers.reject',
      108 => 'transfers.restore',
      109 => 'transfers.view',
      110 => 'trips.create',
      111 => 'trips.delete',
      112 => 'trips.edit',
      113 => 'trips.view',
      114 => 'workers.assign',
      115 => 'workers.create',
      116 => 'workers.delete',
      117 => 'workers.edit',
      118 => 'workers.view',
    ),
  ),
  1 =>
  array (
    'name' => 'مدير الإعدادات',
    'slug' => 'settings-manager',
    'description' => 'إدارة إعدادات النظام والفروع والمستخدمين',
    'active' => true,
    'permissions' =>
    array (
      0 => 'admins.manage',
      1 => 'admins.restore',
      2 => 'admins.toggle',
      3 => 'airports.create',
      4 => 'airports.delete',
      5 => 'airports.edit',
      6 => 'airports.restore',
      7 => 'airports.toggle',
      8 => 'airports.view',
      9 => 'branches.create',
      10 => 'branches.delete',
      11 => 'branches.edit',
      12 => 'branches.restore',
      13 => 'branches.toggle',
      14 => 'branches.view',
      15 => 'expense-types.create',
      16 => 'expense-types.delete',
      17 => 'expense-types.edit',
      18 => 'expense-types.restore',
      19 => 'expense-types.toggle',
      20 => 'expense-types.view',
      21 => 'housings.create',
      22 => 'housings.delete',
      23 => 'housings.edit',
      24 => 'housings.restore',
      25 => 'housings.toggle',
      26 => 'housings.view',
      27 => 'income-types.create',
      28 => 'income-types.delete',
      29 => 'income-types.edit',
      30 => 'income-types.restore',
      31 => 'income-types.toggle',
      32 => 'income-types.view',
      33 => 'nationalities.create',
      34 => 'nationalities.delete',
      35 => 'nationalities.edit',
      36 => 'nationalities.restore',
      37 => 'nationalities.toggle',
      38 => 'nationalities.view',
      39 => 'roles.manage',
    ),
  ),
  2 =>
  array (
    'name' => 'مدير مالي',
    'slug' => 'finance-manager',
    'description' => 'إدارة الإيرادات والمصاريف والتحويلات والتقارير المالية',
    'active' => true,
    'permissions' =>
    array (
      0 => 'branches.create',
      1 => 'branches.view',
      2 => 'expense-types.create',
      3 => 'expense-types.delete',
      4 => 'expense-types.edit',
      5 => 'expense-types.restore',
      6 => 'expense-types.toggle',
      7 => 'expense-types.view',
      8 => 'expenses.approve',
      9 => 'expenses.create',
      10 => 'expenses.delete',
      11 => 'expenses.edit',
      12 => 'expenses.export',
      13 => 'expenses.import',
      14 => 'expenses.reject',
      15 => 'expenses.restore',
      16 => 'expenses.view',
      17 => 'income-types.create',
      18 => 'income-types.delete',
      19 => 'income-types.edit',
      20 => 'income-types.restore',
      21 => 'income-types.toggle',
      22 => 'income-types.view',
      23 => 'incomes.create',
      24 => 'incomes.delete',
      25 => 'incomes.edit',
      26 => 'incomes.export',
      27 => 'incomes.import',
      28 => 'incomes.restore',
      29 => 'incomes.view',
      30 => 'reports.branch-statement',
      31 => 'reports.branch-statement.export',
      32 => 'reports.contracts-delayed',
      33 => 'reports.contracts-received',
      34 => 'reports.contracts-stats',
      35 => 'reports.income-statement',
      36 => 'reports.income-statement.export',
      37 => 'reports.view',
      38 => 'transfers.approve',
      39 => 'transfers.create',
      40 => 'transfers.delete',
      41 => 'transfers.edit',
      42 => 'transfers.reject',
      43 => 'transfers.restore',
      44 => 'transfers.view',
    ),
  ),
  3 =>
  array (
    'name' => 'مدير العملاء والوكلاء',
    'slug' => 'clients-manager',
    'description' => 'إدارة العملاء والوكلاء',
    'active' => true,
    'permissions' =>
    array (
      0 => 'agents.create',
      1 => 'agents.delete',
      2 => 'agents.edit',
      3 => 'agents.view',
      4 => 'branches.view',
      5 => 'clients.create',
      6 => 'clients.delete',
      7 => 'clients.edit',
      8 => 'clients.view',
    ),
  ),
  4 =>
  array (
    'name' => 'مدير التسويق',
    'slug' => 'marketing-manager',
    'description' => 'إدارة الحملات التسويقية والعملاء المحتملين',
    'active' => true,
    'permissions' =>
    array (
      0 => 'branches.view',
      1 => 'calendar.view',
      2 => 'campaigns.create',
      3 => 'campaigns.delete',
      4 => 'campaigns.edit',
      5 => 'campaigns.import',
      6 => 'campaigns.view',
      7 => 'clients.create',
      8 => 'clients.view',
      9 => 'leads.call',
      10 => 'leads.convert',
      11 => 'leads.create',
      12 => 'leads.delete',
      13 => 'leads.edit',
      14 => 'leads.view',
      15 => 'marketing.reports.view',
    ),
  ),
  5 =>
  array (
    'name' => 'مدير الشكاوى',
    'slug' => 'complaints-manager',
    'description' => 'متابعة وإدارة شكاوى العملاء والعاملات',
    'active' => true,
    'permissions' =>
    array (
      0 => 'clients.view',
      1 => 'complaints.create',
      2 => 'complaints.delete',
      3 => 'complaints.edit',
      4 => 'complaints.reports',
      5 => 'complaints.restore',
      6 => 'complaints.view',
      7 => 'workers.view',
    ),
  ),
  6 =>
  array (
    'name' => 'مدير الاستلام والنقل',
    'slug' => 'transport-manager',
    'description' => 'إدارة رحلات الاستلام والنقل وتعيينات السكن',
    'active' => true,
    'permissions' =>
    array (
      0 => 'calendar.view',
      1 => 'housing-assignments.create',
      2 => 'housing-assignments.delete',
      3 => 'housing-assignments.edit',
      4 => 'housing-assignments.view',
      5 => 'housings.view',
      6 => 'trips.create',
      7 => 'trips.delete',
      8 => 'trips.edit',
      9 => 'trips.view',
      10 => 'workers.view',
    ),
  ),
  7 =>
  array (
    'name' => 'مدير نقل الكفالة',
    'slug' => 'sponsorship-transfer-manager',
    'description' => 'إدارة عقود نقل الكفالة ومتابعتها',
    'active' => true,
    'permissions' =>
    array (
      0 => 'branches.view',
      1 => 'clients.view',
      2 => 'sponsorship-transfers.create',
      3 => 'sponsorship-transfers.delete',
      4 => 'sponsorship-transfers.edit',
      5 => 'sponsorship-transfers.view',
      6 => 'workers.view',
    ),
  ),
  8 =>
  array (
    'name' => 'مدير العاملات',
    'slug' => 'workers-manager',
    'description' => 'إدارة ملفات العاملات وتعيينهن للعملاء',
    'active' => true,
    'permissions' =>
    array (
      0 => 'branches.view',
      1 => 'clients.view',
      2 => 'nationalities.view',
      3 => 'workers.assign',
      4 => 'workers.create',
      5 => 'workers.delete',
      6 => 'workers.edit',
      7 => 'workers.view',
    ),
  ),
  9 =>
  array (
    'name' => 'مدير عقود الاستقدام',
    'slug' => 'contracts-manager',
    'description' => 'إدارة عقود الاستقدام ومتابعة العمالة',
    'active' => true,
    'permissions' =>
    array (
      0 => 'clients.view',
      1 => 'contracts.create',
      2 => 'contracts.delete',
      3 => 'contracts.edit',
      4 => 'contracts.view',
      5 => 'reports.contracts-delayed',
      6 => 'reports.contracts-received',
      7 => 'reports.contracts-stats',
    ),
  ),
  10 =>
  array (
    'name' => 'محاسب',
    'slug' => 'accountant',
    'description' => 'إدارة الإيرادات والمصاريف',
    'active' => true,
    'permissions' =>
    array (
      0 => 'branches.view',
      1 => 'expenses.create',
      2 => 'expenses.edit',
      3 => 'expenses.view',
      4 => 'incomes.create',
      5 => 'incomes.edit',
      6 => 'incomes.view',
      7 => 'reports.contracts-delayed',
      8 => 'reports.contracts-received',
      9 => 'reports.contracts-stats',
      10 => 'reports.view',
      11 => 'transfers.view',
    ),
  ),
  11 =>
  array (
    'name' => 'مدير فرع',
    'slug' => 'branch-manager',
    'description' => 'إدارة الفرع',
    'active' => true,
    'permissions' =>
    array (
      0 => 'branches.view',
      1 => 'campaigns.create',
      2 => 'campaigns.edit',
      3 => 'campaigns.import',
      4 => 'campaigns.view',
      5 => 'contracts.edit',
      6 => 'contracts.view',
      7 => 'expenses.create',
      8 => 'expenses.view',
      9 => 'incomes.create',
      10 => 'incomes.view',
      11 => 'leads.call',
      12 => 'leads.convert',
      13 => 'leads.create',
      14 => 'leads.edit',
      15 => 'leads.view',
      16 => 'marketing.reports.view',
      17 => 'reports.branch-statement',
      18 => 'reports.contracts-delayed',
      19 => 'reports.contracts-received',
      20 => 'reports.contracts-stats',
      21 => 'reports.income-statement',
      22 => 'reports.view',
      23 => 'sponsorship-transfers.view',
      24 => 'transfers.create',
      25 => 'transfers.view',
    ),
  ),
  12 =>
  array (
    'name' => 'إدارة عاملات قسم خدمة العملاء',
    'slug' => 'إدارة عاملات قسم خدمة العملاء',
    'description' => NULL,
    'active' => true,
    'permissions' =>
    array (
      0 => 'workers.assign',
      1 => 'workers.view',
    ),
  ),
  13 =>
  array (
    'name' => 'إدارة عملاء قسم خدمة عملاء',
    'slug' => 'إدارة عملاء قسم خدمة عملاء',
    'description' => NULL,
    'active' => true,
    'permissions' =>
    array (
      0 => 'clients.create',
      1 => 'clients.delete',
      2 => 'clients.edit',
      3 => 'clients.view',
    ),
  ),
  14 =>
  array (
    'name' => 'إدارة التسويق خدمة العملاء',
    'slug' => 'إدارة التسويق خدمة العملاء',
    'description' => NULL,
    'active' => true,
    'permissions' =>
    array (
      0 => 'leads.call',
      1 => 'leads.convert',
      2 => 'leads.create',
      3 => 'leads.delete',
      4 => 'leads.edit',
      5 => 'leads.view',
    ),
  ),
  15 =>
  array (
    'name' => 'إدارة عقود الأستقدام قسم الشكاوي',
    'slug' => 'إدارة عقود الأستقدام قسم الشكاوي',
    'description' => NULL,
    'active' => true,
    'permissions' =>
    array (
      0 => 'contracts.view',
      1 => 'reports.contracts-delayed',
      2 => 'reports.contracts-received',
      3 => 'reports.contracts-stats',
    ),
  ),
  16 =>
  array (
    'name' => 'إدارة عقود الاستقدام قسم المالية',
    'slug' => 'إدارة عقود الاستقدام قسم المالية',
    'description' => NULL,
    'active' => true,
    'permissions' =>
    array (
      0 => 'contracts.edit',
      1 => 'contracts.view',
    ),
  ),
);

        $housingVisitPermissions = [
            'housing-visits.view',
            'housing-visits.create',
            'housing-visits.edit',
            'housing-visits.delete',
            'housing-visits.reports',
        ];

        foreach ($roles as &$roleData) {
            $permissions = $roleData['permissions'];
            $isSuperAdmin = $roleData['slug'] === 'super-admin';
            $isHousingRole = in_array('housing-assignments.view', $permissions, true);

            if ($isSuperAdmin || $isHousingRole) {
                $roleData['permissions'] = array_values(array_unique(array_merge($permissions, $housingVisitPermissions)));
            }
        }
        unset($roleData);

        $permissionIdsBySlug = Permission::query()->pluck('id', 'slug');

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                [
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                    'active' => $roleData['active'],
                ]
            );

            $permissionIds = collect($roleData['permissions'])
                ->map(fn (string $slug) => $permissionIdsBySlug[$slug] ?? null)
                ->filter()
                ->values()
                ->all();

            $role->permissions()->sync($permissionIds);
        }
    }
}
