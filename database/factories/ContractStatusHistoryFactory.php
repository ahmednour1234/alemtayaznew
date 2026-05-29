<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\RecruitmentContract;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractStatusHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'contract_id'      => RecruitmentContract::factory(),
            'status'           => fake()->numberBetween(1, 15),
            'status_date'      => fake()->optional()->date(),
            'admin_id'         => Admin::factory(),
            'whatsapp_message' => null,
        ];
    }
}
