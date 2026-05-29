<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-6 months', '+1 month');
        $end   = fake()->dateTimeBetween($start, '+6 months');

        return [
            'name'        => fake()->catchPhrase(),
            'description' => fake()->optional()->paragraph(),
            'sheet_url'   => fake()->optional()->url(),
            'budget'      => fake()->optional()->randomFloat(2, 1000, 100000),
            'start_date'  => $start->format('Y-m-d'),
            'end_date'    => $end->format('Y-m-d'),
            'branch_id'   => null,
            'admin_id'    => Admin::factory(),
            'active'      => true,
        ];
    }

    public function withBranch(): static
    {
        return $this->state(['branch_id' => Branch::factory()]);
    }
}
