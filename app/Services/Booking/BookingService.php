<?php

namespace App\Services\Booking;

use Exception;
use Stripe\Refund;
use Stripe\Stripe;
use App\Models\Flight;
use App\Models\Booking;
use App\Rules\BookingEditableRule;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Services\Payment\PaymentService;
use App\Jobs\SendBookingCancellationEmail;

class BookingService
{

    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }



    //================================getAllBookings


    /**
     * Get all bookings.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllBookings($data)
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

    //==============================================getBooking

    /**
     * Get a specific booking.
     *
     * @param Booking $booking
     * @return Booking
     */
    public function getBooking(Booking $booking)
    {
        $booking->load(['flight', 'payment']);

        return $booking;
    }

    //===========================================createBooking

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

        try {
            $flight = Flight::findOrFail($data['flight_id']);

            if (!$flight->hasAvailableSeats($data['number_of_seats'])) {
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
            return [
                'status' => 'success',
                'message' => 'Booking Create successfully',
                'data' => Booking::with(['user', 'flight'])->find($booking->id),
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create booking: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Booking create failed'];
        }
    }

    //=============================================updateBooking

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
        DB::beginTransaction();
        try {

            $oldFlight = $booking->flight;

            $oldNumberOfSeats = $booking->number_of_seats;

            Log::info('Old flight ID: ' . $oldFlight->id);
            Log::info('Old number of seats: ' . $oldNumberOfSeats);

            $booking->update([
                'flight_id' => $data['flight_id'] ?? $booking->flight_id,
                'number_of_seats' => $data['number_of_seats'] ?? $booking->number_of_seats,

            ]);
            $booking = $booking->fresh();

            Log::info('After fresh - New flight ID: ' . $booking->flight_id);
            Log::info('After fresh - New number of seats: ' . $booking->number_of_seats);

            //get the new flight
            $newFlight = $booking->fresh()->flight;

            if ($oldFlight->id != $newFlight->id) {

                $oldFlight->increment('available_seats', $oldNumberOfSeats);

                if (!$newFlight->hasAvailableSeats($booking->number_of_seats)) {
                    DB::rollBack();
                    return [
                        'status' => 'error',
                        'message' => 'Not enough available seats in the new flight.',
                    ];
                }

                $newFlight->decrement('available_seats', $booking->number_of_seats);
            } else {

                //  If the flight has not changed, but the number of seats has changed

                $seatDifference = $booking->number_of_seats - $oldNumberOfSeats;
                Log::info('Seat difference: ' . $seatDifference);

                if (!$oldFlight->hasAvailableSeats(abs($seatDifference))) {
                    DB::rollBack();
                    return [
                        'status' => 'error',
                        'message' => 'Not enough available seats in the current flight.',
                    ];
                }


                $newFlight['available_seats'] = $oldFlight['available_seats']  - $seatDifference;
                $newFlight->save();
            }

            //Update the payment if the flight or the number of seats has changed

            $this->updatePaymentForBooking($booking, $oldFlight, $oldNumberOfSeats);

            $booking = $booking->fresh()->load('payment');


            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Booking updated successfully',
                'data' => $booking,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update booking: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Booking update failed'];
        }
    }

    //=================================updatePaymentForBooking

    /**
     * update payment directly when update the booking
     * 
     * @param Booking $booking
     * @param $oldFlight
     * @param $oldNumberOfSeats
     * 
     */

    protected function updatePaymentForBooking(Booking $booking, $oldFlight, $oldNumberOfSeats)
    {
        $newFlight = $booking->flight;

        $oldPricePerSeat = $oldFlight->price;
        $newPricePerSeat = $newFlight->price;

        $oldAmount = $oldNumberOfSeats * $oldPricePerSeat;
        $newAmount = $booking->number_of_seats * $newPricePerSeat;

        $amountDifference = $newAmount - $oldAmount;

        if ($amountDifference != 0) {
            $this->paymentService->updatePayment([
                'booking_id' => $booking->id,
                'amount' => $newAmount,
            ]);
            log::info($newAmount);
        }
    }


    //=====================================getCancelled

    /**
     * Get all cancelled bookings with related data.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws Exception
     */

    public function getCancelled()
    {
        try {
            return [
                'status' => 'success',
                'message' => 'fetch cancelled bookings successfully',
                'data' => Booking::cancelled()->with(['flight', 'payment', 'user'])->get(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to fetch cancelled bookings: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to fetch cancelled bookings'];
        }
    }

    //================================================deleteCancelledBookings

    /**
     * Delete all cancelled bookings.
     *
     * @return array
     * @throws Exception
     */
    public function deleteCancelledBookings()
    {
        try {
            $cancelledBookings = Booking::cancelled()->get();

            if ($cancelledBookings->isEmpty()) {
                return [
                    'status' => 'success',
                    'message' => 'No cancelled bookings found to delete.',
                ];
            }

            $deletedCount = Booking::cancelled()->delete();

            return [
                'status' => 'success',
                'message' => "cancelled bookings have been deleted successfully.",
                // 'data'=>$deletedCount
            ];
        } catch (\Exception $e) {
            Log::error('Failed to delete cancelled bookings: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to delete cancelled bookings.',
            ];
        }
    }


    //==================================================cancelBooking

    /**
     * Cancel a booking and refund the payment if applicable.
     *
     * @param \App\Models\Booking $booking
     * @return array
     */
    public function cancelBooking(Booking $booking): array
    {
        DB::beginTransaction();
        try {

            $rule = new BookingEditableRule($booking);

            if (!$rule->passes(null, null)) {
                throw new Exception($rule->message());
            }

            $booking->status = 'cancelled';
            $booking->payment_status = 'refunded';
            $booking->save();

            // Refund the Payment
            $this->refundPayment($booking);

            $booking->flight->increment('available_seats', $booking->number_of_seats);

            SendBookingCancellationEmail::dispatch($booking);

            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Booking cancelled successfully and payment refunded.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking cancellation failed: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    //====================================refundPayment

    /**
     * Refund the payment using Stripe Refund API.
     *
     * @param \App\Models\Booking $booking
     * @return void
     */
    protected function refundPayment(Booking $booking)
    {
        $payment = $booking->payment;
        if ($payment && $payment->transaction_id) {
            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

            Refund::create([
                'charge' => $payment->transaction_id,
            ]);

            $payment->update(['status' => 'refunded']);
        }
    }




    //==============================================deletePendingBookingsBefore24Hours

    /**
     * Delete pending bookings before 24 hours of departure and update flight seats.
     *
     * @return array
     * @throws Exception
     */
    public function deletePendingBookingsBefore24Hours()
    {
        DB::beginTransaction();
        try {
            $pendingBookings = Booking::pendingBefore24Hours()->get();

            if ($pendingBookings->isEmpty()) {
                return [
                    'status' => 'success',
                    'message' => 'No pending bookings found to delete.',
                ];
            }

            foreach ($pendingBookings as $booking) {
                $booking->flight->increment('available_seats', $booking->number_of_seats);

                $booking->delete();
            }

            DB::commit();
            return [
                'status' => 'success',
                'message' => "{$pendingBookings->count()} pending bookings have been deleted successfully, and flight seats have been updated.",
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete pending bookings: ' . $e->getMessage());
            throw new Exception('Failed to delete pending bookings.');
        }
    }

 
}