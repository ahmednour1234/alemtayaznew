<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $permissions = [
        ['name' => 'عرض تأجير العاملات', 'slug' => 'housing-rentals.view'],
        ['name' => 'إنشاء تأجير عاملة', 'slug' => 'housing-rentals.create'],
        ['name' => 'تقرير تأجير العاملات', 'slug' => 'housing-rentals.reports'],
        ['name' => 'عرض التسويات', 'slug' => 'housing-settlements.view'],
        ['name' => 'إنشاء تسوية', 'slug' => 'housing-settlements.create'],
        ['name' => 'تقرير التسويات', 'slug' => 'housing-settlements.reports'],
    ];

    public function up(): void
    {
        Schema::create('housing_rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('housing_assignment_id')->constrained('worker_housing_assignments')->cascadeOnDelete();
            $table->foreignId('worker_id')->constrained('workers')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('contract_number')->nullable();
            $table->decimal('rent_value', 12, 2)->default(0);
            $table->date('rent_start_date')->nullable();
            $table->date('rent_end_date')->nullable();
            $table->string('contract_image')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'rent_end_date']);
            $table->index('client_id');
        });

        Schema::create('housing_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('housing_assignment_id')->constrained('worker_housing_assignments')->cascadeOnDelete();
            $table->foreignId('worker_id')->constrained('workers')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('reference_number')->nullable();
            $table->decimal('settlement_amount', 12, 2)->default(0);
            $table->string('settlement_type')->nullable();
            $table->date('settlement_date')->nullable();
            $table->string('document_image')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'settlement_date']);
            $table->index('client_id');
        });

        $this->seedPermissions();
    }

    private function seedPermissions(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

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

        // Grant to super-admin + any role that already manages housing assignments
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

    public function down(): void
    {
        Schema::dropIfExists('housing_settlements');
        Schema::dropIfExists('housing_rentals');

        if (Schema::hasTable('permissions')) {
            $slugs = collect($this->permissions)->pluck('slug')->all();
            $permissionIds = DB::table('permissions')->whereIn('slug', $slugs)->pluck('id');

            if (Schema::hasTable('role_permission') && $permissionIds->isNotEmpty()) {
                DB::table('role_permission')->whereIn('permission_id', $permissionIds)->delete();
            }

            DB::table('permissions')->whereIn('slug', $slugs)->delete();
        }
    }
};
