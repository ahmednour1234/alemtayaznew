<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Client;
use App\Models\RecruitmentContract;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

class SponsorshipTransferFactory extends Factory
{
    public function definition(): array
    {
        return [
            'contract_number'         => 'ST-' . strtoupper(fake()->unique()->bothify('####-??????')),
            'musaned_contract_number' => fake()->optional()->numerify('###########'),
            'worker_id'               => Worker::factory(),
            'from_client_id'          => Client::factory(),
            'to_client_id'            => null,
            'branch_id'               => Branch::factory(),
            'admin_id'                => null,
            'original_contract_id'    => null,
            'transfer_date'           => fake()->optional()->date(),
            'total_fees'              => fake()->randomFloat(2, 0, 20000),
            'service_fee'             => fake()->randomFloat(2, 0, 5000),
            'loss_amount'             => fake()->randomFloat(2, 0, 3000),
            'payment_status'          => fake()->randomElement(['pending', 'partial', 'full']),
            'needs_medical_exam'      => fake()->boolean(35),
            'needs_iqama'             => fake()->boolean(35),
            'current_department'      => fake()->randomElement(['customer_service', 'accounts']),
            'current_status'          => fake()->numberBetween(1, 4),
            'notes'                   => fake()->optional()->sentence(),
            'active'                  => true,
        ];
    }

    public function withAdmin(): static
    {
        return $this->state(['admin_id' => Admin::factory()]);
    }

    public function withToClient(): static
    {
        return $this->state(['to_client_id' => Client::factory()]);
    }

    public function withOriginalContract(): static
    {
        return $this->state(['original_contract_id' => RecruitmentContract::factory()]);
    }
}
