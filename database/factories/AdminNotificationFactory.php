<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminNotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'admin_id' => Admin::factory(),
            'type'     => fake()->randomElement(['info', 'warning', 'success', 'error', 'complaint', 'contract']),
            'title'    => fake()->sentence(4),
            'body'     => fake()->sentence(),
            'url'      => fake()->url(),
            'read_at'  => null,
        ];
    }

    public function read(): static
    {
        return $this->state(['read_at' => now()]);
    }
}
