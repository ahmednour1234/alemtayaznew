<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\Nationality;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'campaign_id'          => null,
            'name'                 => fake()->name(),
            'phone'                => fake()->optional()->phoneNumber(),
            'city'                 => fake()->optional()->city(),
            'nationality_id'       => null,
            'branch_id'            => null,
            'assigned_admin_id'    => null,
            'referred_by_admin_id' => null,
            'source'               => fake()->optional()->randomElement(['facebook', 'instagram', 'referral', 'website', 'walk_in']),
            'status'               => fake()->randomElement(['new', 'in_progress', 'converted', 'archived']),
            'notes'                => fake()->optional()->sentence(),
            'client_id'            => null,
            'last_contacted_at'    => null,
        ];
    }

    public function withCampaign(): static
    {
        return $this->state(['campaign_id' => Campaign::factory()]);
    }

    public function withBranch(): static
    {
        return $this->state(['branch_id' => Branch::factory()]);
    }

    public function assigned(): static
    {
        return $this->state(['assigned_admin_id' => Admin::factory(), 'status' => 'in_progress']);
    }

    public function converted(): static
    {
        return $this->state(['status' => 'converted', 'client_id' => Client::factory()]);
    }
}
