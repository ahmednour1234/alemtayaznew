<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Housing;
use App\Models\HousingAssignment;
use App\Models\RecruitmentContract;
use App\Models\Role;
use App\Models\Worker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SponsorshipTransferCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_page_lists_all_active_workers_without_client_assignment(): void
    {
        $admin = Admin::factory()->create(['department' => 'customer_service']);
        $admin->roles()->attach(Role::factory()->create(['slug' => 'super-admin']));

        $branch = Branch::factory()->create();
        $client = Client::factory()->create(['branch_id' => $branch->id]);

        $housedWorker = Worker::factory()->create([
            'name' => 'Housed Worker',
            'branch_id' => $branch->id,
            'status' => 'in_housing',
            'client_id' => null,
            'assigned_by_admin_id' => null,
            'assigned_at' => null,
        ]);

        HousingAssignment::factory()->create([
            'worker_id' => $housedWorker->id,
            'housing_id' => Housing::factory()->create(['branch_id' => $branch->id])->id,
            'branch_id' => $branch->id,
            'check_out_date' => null,
            'reason' => 'sponsorship_transfer',
        ]);

        $contractWorker = Worker::factory()->create([
            'name' => 'Contract Worker',
            'branch_id' => $branch->id,
            'status' => 'assigned',
            'client_id' => null,
            'assigned_by_admin_id' => null,
            'assigned_at' => null,
        ]);

        RecruitmentContract::create([
            'contract_number' => 'RC-TEST-00001',
            'worker_id' => $contractWorker->id,
            'client_id' => $client->id,
            'branch_id' => $branch->id,
            'admin_id' => $admin->id,
            'request_date' => now()->toDateString(),
            'current_department' => 'customer_service',
            'current_status' => 1,
            'payment_status' => 'pending',
            'active' => true,
        ]);

        $plainWorker = Worker::factory()->create([
            'name' => 'Plain Worker',
            'branch_id' => $branch->id,
            'status' => 'available',
            'client_id' => null,
            'assigned_by_admin_id' => null,
            'assigned_at' => null,
        ]);

        $assignedWorker = Worker::factory()->create([
            'name' => 'Assigned Worker',
            'branch_id' => $branch->id,
            'status' => 'assigned',
            'client_id' => $client->id,
            'assigned_by_admin_id' => $admin->id,
            'assigned_at' => now(),
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.sponsorship-transfers.create'));

        $response->assertOk();
        $response->assertSee($housedWorker->name);
        $response->assertSee($contractWorker->name);
        $response->assertSee($plainWorker->name);
        $response->assertDontSee($assignedWorker->name);
    }
}
