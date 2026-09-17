<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Machec\Contracts\Enums\RoleName;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Role::findOrCreate(RoleName::Customer);
        Role::findOrCreate(RoleName::CustomerAdmin);
        Role::findOrCreate(RoleName::DataAdmin);

        User::factory()->create([
            'name' => 'Customer Admin',
            'email' => 'customer_admin@example.com',
        ])->assignRole(RoleName::CustomerAdmin);
    }
}
