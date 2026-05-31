<?php

namespace Database\Seeders\Main;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * ProductionUsersSeeder
 * ─────────────────────
 * ينشئ جميع مستخدمي الإنتاج مع ربطهم بالفروع والأدوار المناسبة.
 * آمن للتكرار — يستخدم firstOrCreate.
 *
 * php artisan db:seed --class="Database\Seeders\Main\ProductionUsersSeeder"
 */
class ProductionUsersSeeder extends Seeder
{
    public function run(): void
    {
        // ── جلب الفروع ────────────────────────────────────────────────────────
        $branches = Branch::pluck('id', 'code');

        $ryd = $branches['RYD-001'] ?? null;
        $hfr = $branches['HFR-001'] ?? null;
        $arr = $branches['ARR-001'] ?? null;

        // ── جلب الأدوار ───────────────────────────────────────────────────────
        $roles = Role::pluck('id', 'slug');

        // ── تعريف المستخدمين ──────────────────────────────────────────────────
        // [ email, password, name, branch_id, department, role_slugs[] ]
        $users = [
            // 1 — رئيس مجلس الإدارة
            [
                'email'      => 'ceo@alemtayaz.com',
                'password'   => '0RFSX>dR~',
                'name'       => 'رئيس مجلس الإدارة',
                'branch_id'  => null,
                'department' => 'chairman',
                'roles'      => ['super-admin'],
            ],

            // 2 — مدير فرع الرياض
            [
                'email'      => 'branch.manager.riyadh@alemtayaz.com',
                'password'   => 'Y7&vI2?ggrV*',
                'name'       => 'مدير فرع الرياض',
                'branch_id'  => $ryd,
                'department' => 'branch_manager',
                'roles'      => ['branch-manager'],
            ],

            // 3 — منسق فرع عرعر
            [
                'email'      => 'coordinator.arar@alemtayaz.com',
                'password'   => '2a+UMXHuDWb',
                'name'       => 'منسق فرع عرعر',
                'branch_id'  => $arr,
                'department' => 'coordination',
                'roles'      => ['contracts-manager'],
            ],

            // 4 — منسق فرع حفر الباطن
            [
                'email'      => 'coordinator.hafar@alemtayaz.com',
                'password'   => '2IL=[HV7n',
                'name'       => 'منسق فرع حفر الباطن',
                'branch_id'  => $hfr,
                'department' => 'coordination',
                'roles'      => ['contracts-manager'],
            ],

            // 5 — مدير فرع عرعر
            [
                'email'      => 'branch.manager.arar@alemtayaz.com',
                'password'   => '$V6DM1e]k',
                'name'       => 'مدير فرع عرعر',
                'branch_id'  => $arr,
                'department' => 'branch_manager',
                'roles'      => ['branch-manager'],
            ],

            // 6 — مدير فرع حفر الباطن
            [
                'email'      => 'branch.manager.hafar@alemtayaz.com',
                'password'   => 'n7?Ek=S;qR',
                'name'       => 'مدير فرع حفر الباطن',
                'branch_id'  => $hfr,
                'department' => 'branch_manager',
                'roles'      => ['branch-manager'],
            ],

            // 7 — مشرف مركز الاتصال
            [
                'email'      => 'callcenter.supervisor.hafar@alemtayaz.com',
                'password'   => '1FT7iXc9^',
                'name'       => 'مشرف مركز الاتصال',
                'branch_id'  => $hfr,
                'department' => 'customer_service',
                'roles'      => ['complaints-manager', 'clients-manager'],
            ],

            // 8 — استقبال الرياض 1
            [
                'email'      => 'customer.support1.riyadh@estkdam.alemtayaz.com',
                'password'   => '!:GS^];p1+Kp',
                'name'       => 'استقبال الرياض',
                'branch_id'  => $ryd,
                'department' => 'customer_service',
                'roles'      => ['clients-manager'],
            ],

            // 9 — استقبال الرياض 2
            [
                'email'      => 'customer.support2.riyadh@estkdam.alemtayaz.com',
                'password'   => 'i*4:tY4|',
                'name'       => 'استقبال الرياض 2',
                'branch_id'  => $ryd,
                'department' => 'customer_service',
                'roles'      => ['clients-manager'],
            ],

            // 10 — محاسب فرع الرياض
            [
                'email'      => 'accountant.riyadh@estkdam.alemtayaz.com',
                'password'   => 'Z!fFaixh8',
                'name'       => 'محاسب فرع الرياض',
                'branch_id'  => $ryd,
                'department' => 'accountant',
                'roles'      => ['accountant'],
            ],

            // 11 — مسؤول شكاوى فرع الحفر
            [
                'email'      => 'pr.complaints.hafar@estkdam.alemtayaz.com',
                'password'   => 'IB7:RUAns',
                'name'       => 'مسؤول شكاوى فرع الحفر',
                'branch_id'  => $hfr,
                'department' => 'customer_service',
                'roles'      => ['complaints-manager'],
            ],

            // 12 — المدير المالي
            [
                'email'      => 'finance.director.hafar@alemtayaz.com',
                'password'   => ']1VBtdeHIMn',
                'name'       => 'المدير المالي',
                'branch_id'  => null,
                'department' => 'accounts',
                'roles'      => ['finance-manager'],
            ],

            // 13 — موظف استقبال فرع الحفر 1  (كلمة المرور تحتاج تحديث يدوي)
            [
                'email'      => 'customer.support1.hafar@estkdam.alemtayaz.com',
                'password'   => 'Hfr@Support#1',   // ← غيّر هذه الكلمة يدوياً
                'name'       => 'موظف استقبال فرع الحفر',
                'branch_id'  => $hfr,
                'department' => 'customer_service',
                'roles'      => ['clients-manager'],
            ],

            // 14 — موظف استقبال فرع الحفر 2
            [
                'email'      => 'customer.support2.hafar@estkdam.alemtayaz.com',
                'password'   => '>/L~|r9P',
                'name'       => 'موظف استقبال فرع الحفر 2',
                'branch_id'  => $hfr,
                'department' => 'customer_service',
                'roles'      => ['clients-manager'],
            ],

            // 15 — مشرف التنسيق
            [
                'email'      => 'coordination.supervisor.hafar@estkdam.alemtayaz.com',
                'password'   => '3W1$Pu2=',
                'name'       => 'مشرف التنسيق',
                'branch_id'  => $hfr,
                'department' => 'coordination',
                'roles'      => ['contracts-manager'],
            ],

            // 16 — محاسب فرع الحفر
            [
                'email'      => 'accountant.hafar@estkdam.alemtayaz.com',
                'password'   => 'u4Vhrhuc7;R',
                'name'       => 'محاسب فرع الحفر',
                'branch_id'  => $hfr,
                'department' => 'accountant',
                'roles'      => ['accountant'],
            ],

            // 17 — محاسب فرع عرعر
            [
                'email'      => 'accountant.arar@estkdam.alemtayaz.com',
                'password'   => 'Oiy^kv8Z*',
                'name'       => 'محاسب فرع عرعر',
                'branch_id'  => $arr,
                'department' => 'accountant',
                'roles'      => ['accountant'],
            ],

            // 18 — مسؤول شكاوى فرع عرعر
            [
                'email'      => 'coordinator.arar@estkdam.alemtayaz.com',
                'password'   => '|3Td=?:pDv',
                'name'       => 'مسؤول شكاوى فرع عرعر',
                'branch_id'  => $arr,
                'department' => 'customer_service',
                'roles'      => ['complaints-manager'],
            ],
        ];

        // ── إنشاء المستخدمين ──────────────────────────────────────────────────
        $created = 0;
        $skipped = 0;

        foreach ($users as $data) {
            $admin = Admin::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'       => $data['name'],
                    'password'   => Hash::make($data['password']),
                    'branch_id'  => $data['branch_id'],
                    'department' => $data['department'],
                    'active'     => true,
                ]
            );

            // Update branch_id / department / name on existing records (never touches password)
            if (! $admin->wasRecentlyCreated) {
                $admin->fill([
                    'name'       => $data['name'],
                    'branch_id'  => $data['branch_id'],
                    'department' => $data['department'],
                    'active'     => true,
                ])->save();
            }

            if ($admin->wasRecentlyCreated) {
                $created++;
            } else {
                $skipped++;
            }

            // ربط الأدوار
            $roleIds = collect($data['roles'])
                ->map(fn($slug) => $roles[$slug] ?? null)
                ->filter()
                ->values()
                ->toArray();

            if ($roleIds) {
                $admin->roles()->syncWithoutDetaching($roleIds);
            }
        }

        $this->command->info("✓ Users seeded — created: {$created}, already existed: {$skipped}");
        $this->command->warn('  ⚠️  راجع كلمة مرور: customer.support1.hafar@estkdam.alemtayaz.com (كانت خطأ في الإكسل)');
    }
}
