<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Flight;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   protected $model = Booking::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'flight_id' => Flight::factory(),
            'number_of_seats' => $this->faker->numberBetween(1,3),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ];
    }
}
