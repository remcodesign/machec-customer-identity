<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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

        $customerAdmin = User::factory()->create([
            'name' => 'Customer Admin',
            'email' => 'cust@example.com',
            'password' => Hash::make(config('services.demo.admin_password')),
        ]);
        $customerAdmin->assignRole(RoleName::CustomerAdmin);

        // Demo-only rows so AdminDashboard's "Recent activity" panel isn't
        // empty on a fresh seed — clearly labelled `[seed demo]` so nobody
        // mistakes them for real audit trail entries.
        AuditLog::factory()
            ->sequence(
                ['action' => '[seed demo] login'],
                ['action' => '[seed demo] profile.updated'],
                ['action' => '[seed demo] address.created'],
            )
            ->count(3)
            ->create([
                'user_id' => $customerAdmin->id,
                'subject_type' => User::class,
                'subject_id' => $customerAdmin->id,
            ]);
    }
}
