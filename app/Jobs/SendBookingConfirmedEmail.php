<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use App\Mail\BookingSuccessMail;
use App\Mail\BookingConfirmedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendBookingConfirmedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $booking;
     /**
     * Create a new job instance.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function __construct(User $user,Booking $booking)
    {
        $this->booking = $booking;
        $this->user = $user;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->booking->email_sent_at !== null) {
            Log::info('Email already sent for booking ID: ' . $this->booking->id);
            return; 
        }

        try {
          
            Mail::to($this->booking->user->email)->send(new BookingConfirmedMail($this->user,$this->booking));
            $this->booking->update(['email_sent_at' => now()]);

            Log::info('Email sent successfully for booking ID: ' . $this->booking->id);
        } catch (\Exception $e) {
            Log::error('Error in SendPaymentSuccessEmail job: ' . $e->getMessage());
            throw $e; 
        }
    }
}
