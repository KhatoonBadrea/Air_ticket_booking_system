<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'=>'admin',
            'email'=>'admin@gmail.com',
            'password'=>'123456789&Hh',
            'role'=>'admin',
        ]);
        User::create([
            'name'=>'manager',
            'email'=>'manager@gmail.com',
            'password'=>'123456789&Hh',
            'role'=>'manager',
        ]);
        User::create([
            'name'=>'user1',
            'email'=>'user1@gmail.com',
            'password'=>'123456789&Hh',
            'role'=>'user',
        ]);
        User::create([
            'name'=>'user2',
            'email'=>'user2@gmail.com',
            'password'=>'123456789&Hh',
            'role'=>'user',
        ]);
        User::create([
            'name'=>'user3',
            'email'=>'user3@gmail.com',
            'password'=>'123456789&Hh',
            'role'=>'user',
        ]);
    }
}
