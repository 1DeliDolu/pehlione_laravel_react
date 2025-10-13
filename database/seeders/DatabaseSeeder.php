<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $users = [
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'role' => Role::EMPLOYEE,
            ],
            [
                'name' => 'Kunden',
                'email' => 'kunden@pehlione.com',
                'password' => 'D0cker',
                'role' => Role::KUNDEN,
            ],
            [
                'name' => 'Marketing',
                'email' => 'marketing@pehlione.com',
                'password' => 'D0cker',
                'role' => Role::MARKETING,
            ],
            [
                'name' => 'Lager',
                'email' => 'lager@pehlione.com',
                'password' => 'D0cker',
                'role' => Role::LAGER,
            ],
            [
                'name' => 'Vertrieb',
                'email' => 'vertrieb@pehlione.com',
                'password' => 'D0cker',
                'role' => Role::VERTRIEB,
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@pehlione.com',
                'password' => 'D0cker',
                'role' => Role::ADMIN,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make($user['password']),
                    'role' => $user['role'],
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->call([
            CategorySeeder::class,
        ]);
    }
}
