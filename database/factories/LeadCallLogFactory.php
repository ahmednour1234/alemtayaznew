<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadCallLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lead_id'       => Lead::factory(),
            'admin_id'      => Admin::factory(),
            'status'        => fake()->randomElement([
                'no_answer',
                'not_suitable',
                'nationality_unavailable',
                'wants_rent',
                'profiles_rejected',
                'need_followup',
                'converted',
                'wrong_number',
            ]),
            'notes'         => fake()->optional()->sentence(),
            'follow_up_at'  => fake()->optional(0.4)->dateTimeBetween('now', '+30 days'),
        ];
    }
}
