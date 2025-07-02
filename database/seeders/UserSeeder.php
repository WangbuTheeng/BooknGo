<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@bookngo.com',
            'phone' => '9841000000',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create Sample Operator Users
        $operators = [
            [
                'name' => 'Greenline Operator',
                'email' => 'greenline@bookngo.com',
                'phone' => '9841111111',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Buddha Air Operator',
                'email' => 'buddha@bookngo.com',
                'phone' => '9841222222',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($operators as $operator) {
            User::create($operator);
        }

        // Create Sample Regular Users
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '9841333333',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '9841444444',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
