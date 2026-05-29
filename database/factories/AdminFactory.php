<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AdminFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => fake()->name(),
            'email'      => fake()->unique()->safeEmail(),
            'password'   => Hash::make('password'),
            'active'     => true,
            'branch_id'  => null,
            'department' => fake()->optional()->word(),
        ];
    }

    public function withBranch(): static
    {
        return $this->state(['branch_id' => Branch::factory()]);
    }

    public function inactive(): static
    {
        return $this->state(['active' => false]);
    }
}
