<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Airport;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripFactory extends Factory
{
    public function definition(): array
    {
        return [
            'trip_number'   => 'TRP-' . strtoupper(fake()->unique()->bothify('####-???')),
            'trip_type'     => fake()->randomElement(['arrival', 'departure', 'group_transport', 'deportation']),
            'trip_date'     => fake()->dateTimeBetween('-1 month', '+2 months')->format('Y-m-d'),
            'trip_time'     => fake()->optional()->time('H:i'),
            'airport_id'    => null,
            'flight_number' => fake()->optional()->bothify('??####'),
            'branch_id'     => Branch::factory(),
            'admin_id'      => null,
            'notes'         => fake()->optional()->sentence(),
            'status'        => 'scheduled',
        ];
    }

    public function withAirport(): static
    {
        return $this->state(['airport_id' => Airport::factory()]);
    }

    public function withAdmin(): static
    {
        return $this->state(['admin_id' => Admin::factory()]);
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed']);
    }

    public function cancelled(): static
    {
        return $this->state(['status' => 'cancelled']);
    }
}
