<?php

namespace Tests\Feature\Security;

use App\Models\Admin;
use App\Models\Role;
use App\Services\RoleService;
use App\Services\Security\SecurityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_bad_credentials_are_logged_as_failed_login(): void
    {
        $this->post(route('admin.login.post'), [
            'email'    => 'nobody@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertDatabaseHas('failed_login_logs', [
            'email'          => 'nobody@example.com',
            'failure_reason' => 'bad_credentials',
        ]);
    }

    public function test_inactive_account_login_is_logged(): void
    {
        Admin::factory()->inactive()->create([
            'email'    => 'inactive@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->post(route('admin.login.post'), [
            'email'    => 'inactive@example.com',
            'password' => 'secret123',
        ]);

        $this->assertDatabaseHas('failed_login_logs', [
            'email'          => 'inactive@example.com',
            'failure_reason' => 'inactive',
        ]);
    }

    public function test_role_creation_writes_a_permission_change_log(): void
    {
        $admin = Admin::factory()->create();
        $admin->roles()->attach(Role::factory()->create(['slug' => 'super-admin']));
        $this->actingAs($admin, 'admin');

        app(RoleService::class)->store([
            'name'        => 'Auditor',
            'slug'        => 'auditor-role',
            'active'      => true,
            'permissions' => [],
        ]);

        $this->assertDatabaseHas('permission_change_logs', [
            'action'     => 'created',
            'changed_by' => $admin->id,
        ]);
    }

    public function test_export_log_redacts_sensitive_filters(): void
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        /** @var SecurityLogger $logger */
        $logger = app(SecurityLogger::class);
        $log = $logger->logExport(request(), 'excel', null, null, 'clients.xlsx', [
            'national_id' => '1122334455',
            'status'      => 'active',
        ]);

        $this->assertSame('***', $log->fresh()->filters['national_id']);
        $this->assertSame('active', $log->fresh()->filters['status']);
    }
}
