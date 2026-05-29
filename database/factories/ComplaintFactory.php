<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ComplaintFactory extends Factory
{
    public function definition(): array
    {
        return [
            'complaint_number'       => 'CMP-' . strtoupper(fake()->unique()->bothify('####-????')),
            'public_token'           => Str::random(64),
            'contract_id'            => null,
            'contract_type'          => null,
            'client_id'              => Client::factory(),
            'worker_id'              => Worker::factory(),
            'branch_id'              => Branch::factory(),
            'problem_type'           => fake()->randomElement(['salary', 'food', 'escape', 'work_refusal', 'abuse', 'health', 'other']),
            'description'            => fake()->paragraph(),
            'phone'                  => fake()->optional()->phoneNumber(),
            'assigned_admin_id'      => null,
            'priority'               => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'status'                 => fake()->randomElement(['new', 'in_progress', 'resolved', 'closed', 'escalated']),
            'on_musaned'             => false,
            'musaned_number'         => null,
            'resolution'             => null,
            'processed_at'           => null,
            'resolved_at'            => null,
            'created_by_admin_id'    => Admin::factory(),
            'last_stale_notified_at' => null,
        ];
    }

    public function resolved(): static
    {
        return $this->state([
            'status'      => 'resolved',
            'resolution'  => fake()->paragraph(),
            'resolved_at' => now(),
        ]);
    }

    public function urgent(): static
    {
        return $this->state(['priority' => 'urgent', 'status' => 'new']);
    }
}
