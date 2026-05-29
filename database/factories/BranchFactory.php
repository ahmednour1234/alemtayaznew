<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'         => fake()->company(),
            'code'         => fake()->unique()->bothify('BR-###'),
            'phone'        => fake()->optional()->phoneNumber(),
            'address'      => fake()->optional()->address(),
            'city'         => fake()->optional()->city(),
            'manager_name' => fake()->optional()->name(),
            'active'       => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['active' => false]);
    }
}
