<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    public function definition(): array
    {
        $resource = fake()->unique()->randomElement([
            'branches', 'nationalities', 'airports', 'admins', 'roles',
            'clients', 'agents', 'workers', 'contracts', 'complaints',
            'incomes', 'expenses', 'financial-transfers', 'campaigns',
            'leads', 'trips', 'housing-assignments', 'sponsorship-transfers',
        ]);
        $action = fake()->randomElement(['view', 'create', 'edit', 'delete']);

        return [
            'name'        => ucfirst($resource) . ' ' . ucfirst($action),
            'slug'        => $resource . '.' . $action,
            'description' => fake()->optional()->sentence(),
        ];
    }
}
