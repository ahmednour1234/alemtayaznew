<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $permissions = [
        ['name' => 'عرض زيارات السكن', 'slug' => 'housing-visits.view'],
        ['name' => 'إنشاء زيارات السكن', 'slug' => 'housing-visits.create'],
        ['name' => 'تعديل زيارات السكن', 'slug' => 'housing-visits.edit'],
        ['name' => 'حذف زيارات السكن', 'slug' => 'housing-visits.delete'],
        ['name' => 'تقارير زيارات السكن', 'slug' => 'housing-visits.reports'],
    ];

    public function up(): void
    {
        Schema::create('housing_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('housing_id')->constrained('housings')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->date('visit_date');
            $table->text('documentation')->nullable();
            $table->string('documentation_file')->nullable();
            $table->text('branch_employee_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'housing_id', 'visit_date']);
        });

        Schema::create('housing_visit_admin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('housing_visit_id')->constrained('housing_visits')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['housing_visit_id', 'admin_id']);
        });

        if (Schema::hasTable('permissions')) {
            foreach ($this->permissions as $permission) {
                DB::table('permissions')->updateOrInsert(
                    ['slug' => $permission['slug']],
                    [
                        'name' => $permission['name'],
                        'description' => null,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            $permissionIds = DB::table('permissions')
                ->whereIn('slug', collect($this->permissions)->pluck('slug')->all())
                ->pluck('id');
            $housingAssignmentsViewId = DB::table('permissions')
                ->where('slug', 'housing-assignments.view')
                ->value('id');

            $roleIds = DB::table('roles')
                ->where('slug', 'super-admin')
                ->when($housingAssignmentsViewId, function ($query) use ($housingAssignmentsViewId) {
                    $query->orWhereIn('id', DB::table('role_permission')
                        ->where('permission_id', $housingAssignmentsViewId)
                        ->select('role_id'));
                })
                ->pluck('id');

            foreach ($roleIds as $roleId) {
                foreach ($permissionIds as $permissionId) {
                    DB::table('role_permission')->updateOrInsert([
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('housing_visit_admin');
        Schema::dropIfExists('housing_visits');

        if (Schema::hasTable('permissions')) {
            $permissionIds = DB::table('permissions')
                ->whereIn('slug', collect($this->permissions)->pluck('slug')->all())
                ->pluck('id');

            if (Schema::hasTable('role_permission') && $permissionIds->isNotEmpty()) {
                DB::table('role_permission')->whereIn('permission_id', $permissionIds)->delete();
            }

            DB::table('permissions')
                ->whereIn('slug', collect($this->permissions)->pluck('slug')->all())
                ->delete();
        }
    }
};
