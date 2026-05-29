<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Agent;
use App\Models\Airport;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Nationality;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecruitmentContractFactory extends Factory
{
    public function definition(): array
    {
        $requestDate = fake()->dateTimeBetween('-1 year', 'now');

        return [
            'contract_number'       => 'RC-' . strtoupper(fake()->unique()->bothify('####-??????')),
            'client_id'             => Client::factory(),
            'branch_id'             => Branch::factory(),
            'admin_id'              => Admin::factory(),
            'request_date'          => $requestDate->format('Y-m-d'),
            'visa_image'            => null,
            'visa_type'             => fake()->optional()->randomElement(['domestic', 'rehabilitation']),
            'visa_number'           => fake()->optional()->numerify('#########'),
            'arrival_airport_id'    => null,
            'origin_nationality_id' => null,
            'delivery_airport_id'   => null,
            'musaned_number'        => fake()->optional()->numerify('###########'),
            'musaned_date'          => null,
            'musaned_file'          => null,
            'worker_id'             => null,
            'e_doc_number'          => null,
            'agent_id'              => null,
            'current_department'    => 'customer_service',
            'current_status'        => fake()->numberBetween(1, 15),
            'payment_status'        => fake()->randomElement(['pending', 'partial', 'full']),
            'total_cost'            => fake()->optional()->randomFloat(2, 5000, 50000),
            'arrival_date'          => null,
            'trial_end_date'        => null,
            'contract_end_date'     => null,
            'notes'                 => fake()->optional()->sentence(),
            'client_sms'            => false,
            'client_rating'         => false,
            'rating_image'          => null,
            'active'                => true,
        ];
    }

    public function withWorker(): static
    {
        return $this->state(['worker_id' => Worker::factory()]);
    }

    public function withAgent(): static
    {
        return $this->state(['agent_id' => Agent::factory()]);
    }

    public function withAirports(): static
    {
        return $this->state([
            'arrival_airport_id'  => Airport::factory(),
            'delivery_airport_id' => Airport::factory(),
        ]);
    }

    public function fullyPaid(): static
    {
        return $this->state(['payment_status' => 'full']);
    }
}
