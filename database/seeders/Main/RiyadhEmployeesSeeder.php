<?php

namespace Database\Seeders\Main;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * RiyadhEmployeesSeeder
 * ──────────────────────
 * يضيف 3 موظفين جدد لفرع الرياض، كل واحد يعمل في جميع الأقسام:
 * (المبيعات، الشكاوى، التسويق، نقل الكفالة، الاستقدام، النقل والرحلات)
 *
 * php artisan db:seed --class="Database\Seeders\Main\RiyadhEmployeesSeeder"
 */
class RiyadhEmployeesSeeder extends Seeder
{
    public function run(): void
    {
        $ryd = Branch::where('code', 'RYD-001')->value('id');
        $roles = Role::pluck('id', 'slug');

        // أدوار مشتركة لجميع الموظفين الثلاثة
        $sharedRoles = [
            'clients-manager',               // المبيعات
            'complaints-manager',            // الشكاوى
            'marketing-manager',             // التسويق
            'sponsorship-transfer-manager',  // نقل الكفالة
            'contracts-manager',             // الاستقدام
            'transport-manager',             // النقل والرحلات
        ];

        $users = [
            // 1 — مرعي — مدير الفرع
            [
                'email'      => 'marwi.riyadh@alemtayaz.com',
                'password'   => 'Mrw@Ryd#2026',
                'name'       => 'مرعي',
                'branch_id'  => $ryd,
                'department' => 'branch_manager',
                'roles'      => array_merge(['branch-manager'], $sharedRoles),
            ],

            // 2 — سطام — خدمة العملاء
            [
                'email'      => 'satam.riyadh@estkdam.alemtayaz.com',
                'password'   => 'Stm@Ryd#2026',
                'name'       => 'سطام',
                'branch_id'  => $ryd,
                'department' => 'customer_service',
                'roles'      => $sharedRoles,
            ],

            // 3 — منذر — المحاسب
            [
                'email'      => 'muthar.riyadh@estkdam.alemtayaz.com',
                'password'   => 'Mth@Ryd#2026',
                'name'       => 'منذر',
                'branch_id'  => $ryd,
                'department' => 'accountant',
                'roles'      => array_merge(['accountant'], $sharedRoles),
            ],
        ];

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

            // تحديث البيانات إذا كان الحساب موجوداً (بدون المساس بكلمة المرور)
            if (! $admin->wasRecentlyCreated) {
                $admin->fill([
                    'name'       => $data['name'],
                    'branch_id'  => $data['branch_id'],
                    'department' => $data['department'],
                    'active'     => true,
                ])->save();
                $skipped++;
            } else {
                $created++;
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

        $this->command->info("✓ موظفو الرياض — أُنشئ: {$created}، موجود مسبقاً: {$skipped}");
        $this->command->warn('  ⚠️  غيّر كلمات المرور الافتراضية بعد أول تسجيل دخول');
    }
}
