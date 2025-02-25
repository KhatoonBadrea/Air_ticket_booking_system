<?php

namespace Database\Seeders;

use App\Models\Booking;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Booking::create([

            'user_id' => 2,
            'flight_id' => 1,
            'number_of_seats' => 2,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        Booking::create([

            'user_id' => 3,
            'flight_id' => 2,
            'number_of_seats' => 2,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
        Booking::create([

            'user_id' => 4,
            'flight_id' => 4,
            'number_of_seats' => 2,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }
}
