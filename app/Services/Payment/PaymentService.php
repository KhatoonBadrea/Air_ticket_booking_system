<?php

namespace App\Services\Payment;


use Exception;
use Stripe\Charge;
use Stripe\Stripe;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendBookingConfirmedEmail;

class PaymentService

{

    /**

     * Process a payment for a reservation.
     *
     * @param array $data An associative array containing the payment details:
     *                    - 'booking_id': The ID of the booking.
     *                    - 'amount': The amount to be charged.
     *                    - 'stripeToken': The token generated by Stripe.
     * @return StripeCharge|array The charge object if successful, or an error array if failed.
     */
    public function processPayment(array $data): array
    {
        DB::beginTransaction();
        try {
            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));


            $booking = Booking::find($data['booking_id']);
            if (!$booking) {
                throw new Exception('Booking not found.');
            }

            //procces the total price
            $totalPrice = $booking->number_of_seats * $booking->flight->price;

            // cheack if the amount is enough
            if ($data['amount'] < $totalPrice) {
                throw new Exception('The amount provided is insufficient for the booking.');
            }

            $charge = Charge::create([
                'amount' => $data['amount'] * 100, // convert to cent
                'currency' => 'usd',
                'source' => $data['stripeToken'],
                'description' => 'Payment for booking ID: ' . $booking->id,
            ]);

            $payment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => $data['amount'],
                'transaction_id' => $charge->id,
                'status' => 'paid',
            ]);

            $booking->payment_status = 'paid';
            $booking->status = 'confirmed';
            $booking->save();

            // send an email to confirm the booking
            if ($booking->email_sent_at === null) {
                $user = $booking->user;
                SendBookingConfirmedEmail::dispatch($user, $booking);
                $booking->update(['email_sent_at' => now()]);
            }

            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Payment processed successfully',
                'data' => Payment::with('booking')->find($payment->id),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PaymentService Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Update an existing payment for a booking.
     *
     * @param array $data An associative array containing the updated payment details:
     *                    - 'booking_id': The ID of the booking.
     *                    - 'amount': The updated amount to be charged.
     * @return array The result of the update operation.
     */
    public function updatePayment(array $data): array
    {
        DB::beginTransaction();
        try {

            $booking = Booking::find($data['booking_id']);
            if (!$booking) {
                throw new Exception('Booking not found.');
            }

            $payment = $booking->payment;

            Log::info('Updating payment with data:', [
                'booking_id' => $booking->id,
                'amount' => $data['amount'],
                'transaction_id' => $payment->transaction_id,
            ]);

            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            $oldCharge = Charge::retrieve($payment->transaction_id);
            Log::info('Retrieved old charge from Stripe:', ['charge' => $oldCharge]);


            if ($oldCharge->amount != $data['amount'] * 100) {

                $newCharge = Charge::create([
                    'amount' => $data['amount'] * 100,
                    'currency' => 'usd',
                    'source' => 'tok_visa',
                    'description' => 'Updated payment for booking ID: ' . $booking->id,
                ]);

                Log::info('Created new charge in Stripe:', ['charge' => $newCharge]);


                $payment->update([
                    'transaction_id' => $newCharge->id,
                    'amount' => $data['amount'],
                ]);

                // Update booking status
                $booking->payment_status = 'paid';
                $booking->status = 'confirmed';
                $booking->save();

                log::info($booking);
                Log::info('Updated payment in database:', ['payment' => $payment]);
            }

            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Payment updated successfully',
                'data' => Payment::with('booking')->find($payment->id),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PaymentService Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Payment update failed'];
        }
    }
}
