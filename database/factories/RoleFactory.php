<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => fake()->unique()->jobTitle(),
            'slug'        => fake()->unique()->slug(2),
            'description' => fake()->optional()->sentence(),
            'active'      => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['active' => false]);
    }
}
