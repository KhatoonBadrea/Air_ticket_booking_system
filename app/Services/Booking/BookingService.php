<?php

namespace App\Services\Booking;

use Exception;
use App\Models\Flight;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class BookingService
{
    /**
     * Get all bookings.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllBookings()
    {
        try {
            $perPage = $data['perPage'] ?? 10;

            $booking = Booking::with(['user', 'flight']);
            return $booking->paginate($perPage);
        } catch (Exception $e) {
            Log::error('Failed to fetch all bookings: ' . $e->getMessage());
            throw new Exception('Failed to fetch all bookings.');
        }
    }

    /**
     * Get a specific booking.
     *
     * @param Booking $booking
     * @return Booking
     */
    public function getBooking(Booking $booking)
    {
        return $booking;
    }


    /**
     * Create a new booking.
     *
     * @param array $data
     * @return Booking
     * @throws Exception
     */

    public function createBooking(array $data)
    {
        $user = JWTAuth::parseToken()->authenticate();

        DB::beginTransaction();

        // try {
        $flight = Flight::findOrFail($data['flight_id']);

        if ($flight->available_seats < $data['number_of_seats']) {
            throw new Exception('Not enough available seats.');
        }

        $booking = Booking::create([
            'user_id' => $user->id,
            'flight_id' => $data['flight_id'],
            'number_of_seats' => $data['number_of_seats'],
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $flight->decrement('available_seats', $data['number_of_seats']);

        DB::commit();

        return Booking::with(['user', 'flight'])->find($booking->id);
        // } catch (Exception $e) {
        //     DB::rollBack();
        //     Log::error('Failed to create booking: ' . $e->getMessage());
        //     throw new Exception('Failed to create booking: ' );
        // }
    }

    /**
     * Update an existing booking.
     *
     * @param Booking $booking
     * @param array $data
     * @return Booking
     * @throws Exception
     */
    public function updateBooking(Booking $booking, array $data)
    {
        try {
            $booking->update(array_filter([
                'flight_id' => $data['flight_id'] ?? $booking->flight_id,
                'number_of_seats' => $data['number_of_seats'] ?? $booking->number_of_seats,
            ]));
            return $booking;
        } catch (Exception $e) {
            Log::error('Failed to update booking: ' . $e->getMessage());
            throw new Exception('Failed to update booking.');
        }
    }

    /**
     * Delete a booking (Soft Delete).
     *
     * @param Booking $booking
     * @return Booking
     * @throws Exception
     */
    public function deleteBooking(Booking $booking)
    {
        try {
            $booking->delete();
            return $booking;
        } catch (Exception $e) {
            Log::error('Failed to delete booking: ' . $e->getMessage());
            throw new Exception('Failed to delete booking.');
        }
    }

    /**
     * Restore a deleted booking (Soft Delete).
     *
     * @param int $id
     * @return Booking
     * @throws Exception
     */
    public function restoreBooking($id)
    {
        try {
            $booking = Booking::withTrashed()->findOrFail($id);
            $booking->restore();
            return $booking;
        } catch (Exception $e) {
            Log::error('Failed to restore booking: ' . $e->getMessage());
            throw new Exception('Failed to restore booking.');
        }
    }

    /**
     * Get all deleted bookings (Soft Deleted).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws Exception
     */
    public function getDeletedBookings()
    {
        try {
            return Booking::onlyTrashed()->with(['user', 'flight'])->get();
        } catch (Exception $e) {
            Log::error('Failed to fetch deleted bookings: ' . $e->getMessage());
            throw new Exception('Failed to fetch deleted bookings.');
        }
    }

    /**
     * Permanently delete a booking (Force Delete).
     *
     * @param int $id
     * @return void
     * @throws Exception
     */
    public function forceDeleteBooking($id)
    {
        try {
            $booking = Booking::withTrashed()->findOrFail($id);
            $booking->forceDelete();
        } catch (Exception $e) {
            Log::error('Failed to force delete booking: ' . $e->getMessage());
            throw new Exception('Failed to force delete booking.');
        }
    }
}
