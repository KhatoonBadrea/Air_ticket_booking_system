<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookingConfirmedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $booking;
    public $user;
    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param Payment $payment
     */
    public function __construct(User $user, Booking $booking)
    {
        $this->booking = $booking;
        $this->user = $user;
        Log::info('Building BookingConfirmedMail for user: ' . $user->email);
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::info('Rendering email template for user: ' . $this->booking->id);


        return $this->subject('Booking Confirmed')
            ->view('emails.booking_confirmed') 
            ->with([
                'booking' => $this->booking,
                'booking' => $this->user,
            ]);
    }
}
