<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Nationality;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'             => fake()->name('female'),
            'passport_number'  => fake()->optional()->bothify('??#######'),
            'nationality_id'   => null,
            'profession'       => fake()->optional()->randomElement(['domestic_worker', 'nanny', 'cook', 'driver', 'nurse', 'housekeeper', 'security', 'other']),
            'gender'           => fake()->randomElement(['female', 'male']),
            'experience'       => fake()->optional()->randomElement(['none', '1-3', '3-5', '5+']),
            'religion'         => fake()->optional()->randomElement(['muslim', 'christian', 'other']),
            'age'              => fake()->optional()->numberBetween(18, 55),
            'phone'            => fake()->optional()->phoneNumber(),
            'cv_path'          => null,
            'passport_image'   => null,
            'status'           => 'available',
            'client_id'        => null,
            'branch_id'        => null,
            'admin_id'         => null,
            'notes'            => fake()->optional()->sentence(),
            'active'           => true,
        ];
    }

    public function withNationality(): static
    {
        return $this->state(['nationality_id' => Nationality::factory()]);
    }

    public function withBranch(): static
    {
        return $this->state(['branch_id' => Branch::factory()]);
    }

    public function available(): static
    {
        return $this->state(['status' => 'available']);
    }

    public function assigned(): static
    {
        return $this->state(['status' => 'assigned']);
    }
}
