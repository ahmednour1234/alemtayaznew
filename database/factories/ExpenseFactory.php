<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\ExpenseType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'branch_id'        => Branch::factory(),
            'expense_type_id'  => ExpenseType::factory(),
            'admin_id'         => Admin::factory(),
            'approved_by'      => null,
            'amount'           => fake()->randomFloat(2, 50, 50000),
            'date'             => fake()->date(),
            'payment_method'   => fake()->randomElement(['cash', 'bank_transfer', 'card', 'other']),
            'status'           => 'pending',
            'reference_number' => fake()->optional()->bothify('REF-#####'),
            'description'      => fake()->optional()->sentence(),
            'attachment'       => null,
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
