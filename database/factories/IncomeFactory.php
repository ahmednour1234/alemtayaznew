<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\IncomeType;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'branch_id'        => Branch::factory(),
            'income_type_id'   => IncomeType::factory(),
            'admin_id'         => Admin::factory(),
            'amount'           => fake()->randomFloat(2, 50, 50000),
            'date'             => fake()->date(),
            'payment_method'   => fake()->randomElement(['cash', 'bank_transfer', 'card', 'other']),
            'reference_number' => fake()->optional()->bothify('REF-#####'),
            'description'      => fake()->optional()->sentence(),
            'attachment'       => null,
        ];
    }
}
