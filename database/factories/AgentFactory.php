<?php

namespace Database\Factories;

use App\Models\Nationality;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'           => fake()->name(),
            'phone'          => fake()->optional()->phoneNumber(),
            'email'          => fake()->optional()->safeEmail(),
            'nationality_id' => null,
            'document'       => null,
            'notes'          => fake()->optional()->sentence(),
            'active'         => true,
        ];
    }

    public function withNationality(): static
    {
        return $this->state(['nationality_id' => Nationality::factory()]);
    }
}
