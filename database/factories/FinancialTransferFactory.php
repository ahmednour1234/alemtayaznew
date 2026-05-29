<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialTransferFactory extends Factory
{
    public function definition(): array
    {
        return [
            'from_branch_id'   => Branch::factory(),
            'to_branch_id'     => Branch::factory(),
            'admin_id'         => Admin::factory(),
            'approved_by'      => null,
            'amount'           => fake()->randomFloat(2, 100, 100000),
            'date'             => fake()->date(),
            'status'           => 'pending',
            'description'      => fake()->optional()->sentence(),
            'rejection_reason' => null,
            'approved_at'      => null,
        ];
    }

    public function approved(): static
    {
        return $this->state([
            'status'      => 'approved',
            'approved_by' => Admin::factory(),
            'approved_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status'           => 'rejected',
            'rejection_reason' => fake()->sentence(),
        ]);
    }
}
