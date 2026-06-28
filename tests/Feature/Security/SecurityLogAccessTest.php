<?php

namespace Tests\Feature\Security;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityLogAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_without_permission_cannot_view_security_logs(): void
    {
        $admin = Admin::factory()->create();
        $admin->roles()->attach(Role::factory()->create(['slug' => 'plain-role'])); // no permissions

        $this->actingAs($admin, 'admin')
            ->get(route('admin.security.access-logs'))
            ->assertForbidden();
    }

    public function test_super_admin_can_view_security_logs(): void
    {
        $admin = Admin::factory()->create();
        $admin->roles()->attach(Role::factory()->create(['slug' => 'super-admin']));

        $this->actingAs($admin, 'admin')
            ->get(route('admin.security.access-logs'))
            ->assertOk();
    }

    public function test_sensitive_access_is_recorded_by_middleware(): void
    {
        $admin = Admin::factory()->create();
        $admin->roles()->attach(Role::factory()->create(['slug' => 'super-admin']));

        $this->actingAs($admin, 'admin')
            ->get(route('admin.security.failed-logins'))
            ->assertOk();

        $this->assertDatabaseHas('access_logs', [
            'route_name'  => 'admin.security.failed-logins',
            'action_type' => 'sensitive_access',
            'user_id'     => $admin->id,
        ]);
    }
}
