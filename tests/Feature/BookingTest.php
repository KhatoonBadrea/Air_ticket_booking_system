<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Flight;
use App\Models\Booking;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;




class BookingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper function to generate JWT headers
     */
    protected function jwtHeaders(User $user)
    {
        $token = auth()->login($user); // generate JWT Token
        return ['Authorization' => "Bearer $token"];
    }

    /** @test */
    public function user_can_create_booking_when_seats_are_available()
    {
        $user = User::factory()->create();
        $flight = Flight::factory()->create(['available_seats' => 5]);

        $response = $this->withHeaders($this->jwtHeaders($user))
            ->postJson('/api/bookings', [
                'flight_id' => $flight->id,
                'number_of_seats' => 2,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'flight_id' => $flight->id,
            'number_of_seats' => 2,
        ]);
    }

    /** @test */
    public function authenticated_user_can_create_booking()
    {
        $user = User::factory()->create();
        $flight = Flight::factory()->create(['available_seats' => 3]);

        $response = $this->withHeaders($this->jwtHeaders($user))
            ->postJson('/api/bookings', [
                'flight_id' => $flight->id,
                'number_of_seats' => 1,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'flight_id' => $flight->id,
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_booking()
    {
        $flight = Flight::factory()->create(['available_seats' => 3]);

        $response = $this->postJson('/api/bookings', [
            'flight_id' => $flight->id,
            'number_of_seats' => 1,
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function cannot_create_booking_if_seats_not_available()
    {
        $user = User::factory()->create();
        $flight = Flight::factory()->create(['available_seats' => 1]);

        //try to booking more then available seats
        $response = $this->withHeaders($this->jwtHeaders($user))
            ->postJson('/api/bookings', [
                'flight_id' => $flight->id,
                'number_of_seats' => 2,
            ]);

        $response->assertStatus(400); // Validation error
    }

    /** @test */
    public function authenticated_user_can_update_own_booking()
    {
        $user = User::factory()->create();
        $flight = Flight::factory()->create(['available_seats' => 5]);
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'flight_id' => $flight->id,
            'number_of_seats' => 1,
        ]);

        $response = $this->withHeaders($this->jwtHeaders($user))
            ->putJson("/api/bookings/{$booking->id}", [
                'number_of_seats' => 2,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'number_of_seats' => 2,
        ]);
    }

    /** @test */
    public function cannot_update_other_users_booking()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $flight = Flight::factory()->create(['available_seats' => 5]);
        $booking = Booking::factory()->create([
            'user_id' => $user2->id,
            'flight_id' => $flight->id,
            'number_of_seats' => 1,
        ]);

        $response = $this->withHeaders($this->jwtHeaders($user1))
            ->putJson("/api/bookings/{$booking->id}", [
                'number_of_seats' => 2,
            ]);

        $response->assertStatus(500); // Forbidden
    }
}
