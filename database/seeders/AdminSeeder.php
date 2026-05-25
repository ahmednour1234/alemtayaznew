<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'     => 'مدير النظام',
                'password' => Hash::make('password'),
                'active'   => true,
            ]
        );

        $superAdmin = Role::where('slug', 'super-admin')->first();
        if ($superAdmin) {
            $admin->roles()->syncWithoutDetaching([$superAdmin->id]);
        }
    }
}
