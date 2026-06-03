<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $branches = array (
  0 =>
  array (
    'name' => 'الرياض',
    'code' => 'RYD-001',
    'city' => 'الرياض',
    'manager_name' => '',
    'active' => true,
  ),
  1 =>
  array (
    'name' => 'حفر الباطن',
    'code' => 'HFR-001',
    'city' => 'حفر الباطن',
    'manager_name' => '',
    'active' => true,
  ),
  2 =>
  array (
    'name' => 'عرعر',
    'code' => 'ARR-001',
    'city' => 'عرعر',
    'manager_name' => '',
    'active' => true,
  ),
);

        foreach ($branches as $branch) {
            Branch::updateOrCreate(
                ['code' => $branch['code']],
                [
                    'name' => $branch['name'],
                    'city' => $branch['city'],
                    'manager_name' => $branch['manager_name'],
                    'active' => $branch['active'],
                ]
            );
        }

        $admins = array (
  0 =>
  array (
    'name' => 'مدير النظام',
    'email' => 'admin@admin.com',
    'password' => '$2y$12$qBjY0s/Uc9/IL6BTzsYhseZHDHrGIz2bAE0nGZV0DAs1HvFO1nmaC',
    'active' => true,
    'branch_code' => NULL,
    'department' => 'chairman',
    'roles' =>
    array (
      0 => 'super-admin',
    ),
  ),
  1 =>
  array (
    'name' => 'رئيس مجلس الإدارة',
    'email' => 'ceo@alemtayaz.com',
    'password' => '$2y$12$EGSIzkJTc8DpdwO/ROtcY.R83v5w0jsZRyEMJCQWdW471lwRHYunO',
    'active' => true,
    'branch_code' => NULL,
    'department' => 'chairman',
    'roles' =>
    array (
      0 => 'super-admin',
    ),
  ),
  2 =>
  array (
    'name' => 'مدير فرع الرياض',
    'email' => 'branch.manager.riyadh@alemtayaz.com',
    'password' => '$2y$12$kXc1VHXIcXIQzKhVH/1XU.7s16qSzFA1NtkE3rTfTTYoXont0DlIO',
    'active' => true,
    'branch_code' => 'RYD-001',
    'department' => 'branch_manager',
    'roles' =>
    array (
      0 => 'branch-manager',
    ),
  ),
  3 =>
  array (
    'name' => 'منسق فرع عرعر',
    'email' => 'coordinator.arar@alemtayaz.com',
    'password' => '$2y$12$g7FK9fQtYNg2eXNITxViNuKAQIrofk5tDmKL8jtS.mS5Jx1n737Z6',
    'active' => true,
    'branch_code' => 'ARR-001',
    'department' => 'coordination',
    'roles' =>
    array (
      0 => 'contracts-manager',
    ),
  ),
  4 =>
  array (
    'name' => 'منسق فرع حفر الباطن- خضر الشاذلي',
    'email' => 'coordinator.hafar@alemtayaz.com',
    'password' => '$2y$12$mTeVvRxO2tJUFKkSc5tax.ByOTgFOGFkQ43KdZggi1ZKwdKiEWkIC',
    'active' => true,
    'branch_code' => NULL,
    'department' => 'coordination',
    'roles' =>
    array (
      0 => 'clients-manager',
      1 => 'complaints-manager',
      2 => 'contracts-manager',
      3 => 'sponsorship-transfer-manager',
      4 => 'transport-manager',
      5 => 'workers-manager',
    ),
  ),
  5 =>
  array (
    'name' => 'مدير فرع عرعر',
    'email' => 'branch.manager.arar@alemtayaz.com',
    'password' => '$2y$12$StoS0cIYY28t16TIzA3y/uY4uK22ADDhrk03Cbm5noEYoKmo.DrsC',
    'active' => true,
    'branch_code' => 'ARR-001',
    'department' => 'branch_manager',
    'roles' =>
    array (
      0 => 'branch-manager',
    ),
  ),
  6 =>
  array (
    'name' => 'مدير فرع حفر الباطن',
    'email' => 'branch.manager.hafar@alemtayaz.com',
    'password' => '$2y$12$32jqMfGkg2Kcg/2VfsBtju8YjJ1S.ldAJMuXGLUezSOzyeVQxjM7W',
    'active' => true,
    'branch_code' => 'HFR-001',
    'department' => 'branch_manager',
    'roles' =>
    array (
      0 => 'branch-manager',
    ),
  ),
  7 =>
  array (
    'name' => 'مشرف مركز الاتصال',
    'email' => 'callcenter.supervisor.hafar@alemtayaz.com',
    'password' => '$2y$12$gdgj4pbLJYx1wnfxV85jaO1Zq94OB.4ENcmCai0SUzMjt./Ov9DUm',
    'active' => true,
    'branch_code' => 'HFR-001',
    'department' => 'customer_service',
    'roles' =>
    array (
      0 => 'clients-manager',
      1 => 'complaints-manager',
    ),
  ),
  8 =>
  array (
    'name' => 'استقبال الرياض',
    'email' => 'customer.support1.riyadh@estkdam.alemtayaz.com',
    'password' => '$2y$12$z5OlnhkxwWyhaYZMWpmC/uMW3AukkkjN7qIVC9mUwDE1SHqVbVm4a',
    'active' => true,
    'branch_code' => 'RYD-001',
    'department' => 'customer_service',
    'roles' =>
    array (
      0 => 'clients-manager',
    ),
  ),
  9 =>
  array (
    'name' => 'استقبال الرياض 2',
    'email' => 'customer.support2.riyadh@estkdam.alemtayaz.com',
    'password' => '$2y$12$8TTkmU0dOUQe5W9tldl/t.Kprlr2vEm9k5iSuxy4S2Q0LtfBbh9n.',
    'active' => true,
    'branch_code' => 'RYD-001',
    'department' => 'customer_service',
    'roles' =>
    array (
      0 => 'clients-manager',
    ),
  ),
  10 =>
  array (
    'name' => 'محاسب فرع الرياض',
    'email' => 'accountant.riyadh@estkdam.alemtayaz.com',
    'password' => '$2y$12$4S44wS0Fu6cGhahKqmrTEeAJIwi/mkKaQBkv7hP1FxgxNXMFhS3.G',
    'active' => true,
    'branch_code' => 'RYD-001',
    'department' => 'accountant',
    'roles' =>
    array (
      0 => 'accountant',
    ),
  ),
  11 =>
  array (
    'name' => 'مسؤول شكاوى فرع الحفر - فيصل العنزي',
    'email' => 'pr.complaints.hafar@estkdam.alemtayaz.com',
    'password' => '$2y$12$OsKXKKy5sxQq9q8Y2MaNbuGW8dxl0yzwJG7lEKOlkSK65qZCrnL0.',
    'active' => true,
    'branch_code' => 'HFR-001',
    'department' => 'customer_service',
    'roles' =>
    array (
      0 => 'complaints-manager',
      1 => 'sponsorship-transfer-manager',
      2 => 'transport-manager',
      3 => 'إدارة عقود الأستقدام قسم الشكاوي',
    ),
  ),
  12 =>
  array (
    'name' => 'المدير المالي - محمود بشير',
    'email' => 'finance.director.hafar@alemtayaz.com',
    'password' => '$2y$12$GBQi13SNVPWKvZmGs79PieuLaNjk74B2ra1IJTNKBM5pvc9xZgugq',
    'active' => true,
    'branch_code' => NULL,
    'department' => 'accountant',
    'roles' =>
    array (
      0 => 'finance-manager',
      1 => 'إدارة عقود الاستقدام قسم المالية',
    ),
  ),
  13 =>
  array (
    'name' => 'موظف استقبال فرع الحفر-هاني  الحربي',
    'email' => 'customer.support1.hafar@estkdam.alemtayaz.com',
    'password' => '$2y$12$VP4a41EWC3U0sIPj1LgdIO0PLIFuFVnVFrnLJ1mcIB5pkLSYe3aOO',
    'active' => true,
    'branch_code' => 'HFR-001',
    'department' => 'customer_service',
    'roles' =>
    array (
      0 => 'contracts-manager',
      1 => 'إدارة التسويق خدمة العملاء',
      2 => 'إدارة عاملات قسم خدمة العملاء',
      3 => 'إدارة عملاء قسم خدمة عملاء',
    ),
  ),
  14 =>
  array (
    'name' => 'موظف استقبال فرع الحفر  - مشاري',
    'email' => 'customer.support2.hafar@estkdam.alemtayaz.com',
    'password' => '$2y$12$iQTR2MtGcmk4D7hW9k7.TOyoAh.JGBofoehgpJaEoeZ98gvu8fQ9e',
    'active' => true,
    'branch_code' => 'HFR-001',
    'department' => 'customer_service',
    'roles' =>
    array (
      0 => 'contracts-manager',
      1 => 'إدارة التسويق خدمة العملاء',
      2 => 'إدارة عاملات قسم خدمة العملاء',
      3 => 'إدارة عملاء قسم خدمة عملاء',
    ),
  ),
  15 =>
  array (
    'name' => 'مشرف التنسيق - أسامة محمد',
    'email' => 'coordination.supervisor.hafar@estkdam.alemtayaz.com',
    'password' => '$2y$12$AVCh5Xtgvr7ZSFCeS5/mLu0kSKwj/43ff1rVEExMvFfe2fW5nJPP2',
    'active' => true,
    'branch_code' => NULL,
    'department' => 'branch_manager',
    'roles' =>
    array (
      0 => 'clients-manager',
      1 => 'complaints-manager',
      2 => 'contracts-manager',
      3 => 'transport-manager',
      4 => 'workers-manager',
    ),
  ),
  16 =>
  array (
    'name' => 'محاسب فرع الحفر',
    'email' => 'accountant.hafar@estkdam.alemtayaz.com',
    'password' => '$2y$12$O9sFB7lTzcwOGEkzpb4tvu2Bj/WrH.hFcVRrbt8Rok5t/7Fmai0wm',
    'active' => true,
    'branch_code' => 'HFR-001',
    'department' => 'accountant',
    'roles' =>
    array (
      0 => 'accountant',
    ),
  ),
  17 =>
  array (
    'name' => 'محاسب فرع عرعر',
    'email' => 'accountant.arar@estkdam.alemtayaz.com',
    'password' => '$2y$12$ClW4QVQ6rs1Or3qU3z6lSeB/OZtrO6Rkmq72YiyY1xoeMwvDSxp6u',
    'active' => true,
    'branch_code' => 'ARR-001',
    'department' => 'accountant',
    'roles' =>
    array (
      0 => 'accountant',
    ),
  ),
  18 =>
  array (
    'name' => 'مسؤول شكاوى فرع عرعر',
    'email' => 'coordinator.arar@estkdam.alemtayaz.com',
    'password' => '$2y$12$NgjWBGjwQlM5R0vNJFJRzO7F.QS/3n8M/fWYtGAvvFgwuY07jnrXC',
    'active' => true,
    'branch_code' => 'ARR-001',
    'department' => 'customer_service',
    'roles' =>
    array (
      0 => 'complaints-manager',
    ),
  ),
);

        $branchIdsByCode = Branch::query()->pluck('id', 'code');
        $roleIdsBySlug = Role::query()->pluck('id', 'slug');

        foreach ($admins as $adminData) {
            $admin = Admin::withTrashed()->updateOrCreate(
                ['email' => $adminData['email']],
                [
                    'name' => $adminData['name'],
                    'password' => $adminData['password'],
                    'active' => $adminData['active'],
                    'branch_id' => $adminData['branch_code'] ? ($branchIdsByCode[$adminData['branch_code']] ?? null) : null,
                    'department' => $adminData['department'],
                ]
            );

            if ($admin->trashed()) {
                $admin->restore();
            }

            $roleIds = collect($adminData['roles'])
                ->map(fn (string $slug) => $roleIdsBySlug[$slug] ?? null)
                ->filter()
                ->values()
                ->all();

            $admin->roles()->sync($roleIds);
        }
    }
}
