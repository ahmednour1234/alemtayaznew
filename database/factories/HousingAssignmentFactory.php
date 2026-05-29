<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Housing;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

class HousingAssignmentFactory extends Factory
{
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('-3 months', 'now');

        return [
            'worker_id'      => Worker::factory(),
            'housing_id'     => Housing::factory(),
            'branch_id'      => Branch::factory(),
            'admin_id'       => null,
            'check_in_date'  => $checkIn->format('Y-m-d'),
            'check_out_date' => fake()->optional(0.4)->dateTimeBetween($checkIn, '+3 months')?->format('Y-m-d'),
            'notes'          => fake()->optional()->sentence(),
            'reason'         => fake()->optional()->sentence(4),
        ];
    }

    public function withAdmin(): static
    {
        return $this->state(['admin_id' => Admin::factory()]);
    }

    public function checkedOut(): static
    {
        return $this->state(fn(array $attrs) => [
            'check_out_date' => fake()->dateTimeBetween($attrs['check_in_date'], '+2 months')->format('Y-m-d'),
        ]);
    }
}
