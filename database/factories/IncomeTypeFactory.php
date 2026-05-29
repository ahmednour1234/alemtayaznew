<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => fake()->unique()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'active'      => true,
        ];
    }
}
