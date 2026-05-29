<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\RecruitmentContract;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractActivityLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'contract_id' => RecruitmentContract::factory(),
            'admin_id'    => Admin::factory(),
            'action'      => fake()->randomElement(['created', 'updated', 'status_changed', 'document_uploaded', 'note_added']),
            'section'     => fake()->optional()->randomElement(['general', 'visa', 'worker', 'payment', 'dates']),
            'label'       => fake()->optional()->sentence(4),
        ];
    }
}
