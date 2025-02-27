<?php

namespace Database\Seeders;

use App\Models\Flight;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FlightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Flight::create([
            'origin' => 'New York',
            'destination' => 'Los Angeles',
            'departure_time' => '2025-02-28 08:00:00',
            'arrival_time' => '2025-02-28 11:00:00',
            'price' => 250,
            'available_seats' => 150,
        ]);

        Flight::create([
            'origin' => 'London',
            'destination' => 'Paris',
            'departure_time' => '2025-10-20 10:30:00',
            'arrival_time' => '2025-10-20 12:00:00',
            'price' => 300,
            'available_seats' => 100,
        ]);

        Flight::create([
            'origin' => 'Dubai',
            'destination' => 'Riyadh',
            'departure_time' => '2025-10-24 14:00:00',
            'arrival_time' => '2025-10-25 15:30:00',
            'price' => 200,
            'available_seats' => 200,
        ]);

        Flight::create([
            'origin' => 'Tokyo',
            'destination' => 'Seoul',
            'departure_time' => '2025-10-30 09:00:00',
            'arrival_time' => '2025-10-30 11:30:00',
            'price' => 400,
            'available_seats' => 120,
        ]);
    }
}
