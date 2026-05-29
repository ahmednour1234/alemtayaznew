<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NationalityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'   => fake()->unique()->country(),
            'code'   => fake()->optional()->countryCode(),
            'active' => true,
        ];
    }
}
