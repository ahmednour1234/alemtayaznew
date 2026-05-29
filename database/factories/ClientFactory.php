<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Nationality;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'                    => fake()->name(),
            'national_id'             => fake()->unique()->numerify('##########'),
            'phone'                   => fake()->optional()->phoneNumber(),
            'marital_status'          => fake()->optional()->randomElement(['single', 'married', 'divorced', 'widowed']),
            'classification'          => fake()->randomElement(['potential', 'confirmed', 'premium', 'blocked']),
            'national_id_image'       => null,
            'required_nationality_id' => null,
            'worker_type'             => fake()->optional()->word(),
            'monthly_salary'          => fake()->optional()->randomFloat(2, 500, 5000),
            'branch_id'               => null,
            'admin_id'                => null,
            'notes'                   => fake()->optional()->sentence(),
            'active'                  => true,
        ];
    }

    public function withRelations(): static
    {
        return $this->state([
            'required_nationality_id' => Nationality::factory(),
            'branch_id'               => Branch::factory(),
            'admin_id'                => Admin::factory(),
        ]);
    }

    public function blocked(): static
    {
        return $this->state(['classification' => 'blocked']);
    }
}
