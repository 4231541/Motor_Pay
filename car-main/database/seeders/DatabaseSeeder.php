<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Brand;
use App\Models\Car;
use App\Enums\RoleName;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        // Create Super Admin if not exists
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'phone' => '0500000000',
            ]
        );
        if (!$superAdmin->hasRole(RoleName::SuperAdmin)) {
            $superAdmin->assignRole(RoleName::SuperAdmin);
        }

        // Create Test User
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );
        if (!$user->hasRole(RoleName::Customer)) {
            $user->assignRole(RoleName::Customer);
        }

        // Check if we need to seed cars
        if (Brand::count() === 0) {
            echo "Seeding Cars and Brands...\n";
            Brand::factory(5)->create()->each(function ($brand) {
                // For each brand, create some cars (this will auto-create models via CarFactory)
                Car::factory(4)->create([
                    'brand_id' => $brand->id,
                    'owner_id' => User::where('email', 'admin@example.com')->first()->id
                ]);
            });
        }
    }
}
