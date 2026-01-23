<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ===== Admin =====
        $admin = User::firstOrCreate(
            ['email' => 'khatoonbadrea66@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        $admin->assignRole('admin');

        // ===== Normal Users =====
        $users = [
            [
                'name' => 'User One',
                'email' => 'user1@airbooking.com',
            ],
            [
                'name' => 'User Two',
                'email' => 'user2@airbooking.com',
            ],
            [
                'name' => 'User Three',
                'email' => 'user3@airbooking.com',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                ]
            );

            $user->assignRole('user');
        }
    }
}
