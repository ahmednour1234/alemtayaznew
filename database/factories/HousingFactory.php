<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class HousingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'branch_id'   => Branch::factory(),
            'admin_id'    => null,
            'name'        => fake()->streetName() . ' Housing',
            'address'     => fake()->optional()->address(),
            'capacity'    => fake()->optional()->numberBetween(5, 50),
            'description' => fake()->optional()->sentence(),
            'active'      => true,
        ];
    }

    public function withAdmin(): static
    {
        return $this->state(['admin_id' => Admin::factory()]);
    }
}
