<?php

namespace Database\Factories;

use App\Models\Flight;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flight>
 */
class FlightFactory extends Factory
{

    protected $model = Flight::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'origin' => 'Flight ' . $this->faker->unique()->numberBetween(100, 999),
            'destination' => 'Dubai',
            'departure_time' => now()->addDays(2),
            'arrival_time' => now()->addDay(2),
            'price' => $this->faker->numberBetween(100, 500),
            'available_seats' => $this->faker->numberBetween(1, 100),

        ];
    }
}
