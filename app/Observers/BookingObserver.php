<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\BookingActivityLog;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        BookingActivityLog::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'action' => 'created',
            'changes' => [
                'price' => $booking->total_price,
                'status' => $booking->status,
            ],
        ]);
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        if ($booking->isDirty()) {
            BookingActivityLog::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'action' => 'updated',
                'changes' => [
                    'old' => $booking->getOriginal(),
                    'new' => $booking->getAttributes(),
                ],
            ]);
        }
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        BookingActivityLog::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'action' => 'deleted',
        ]);
    }

    /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "force deleted" event.
     */
    public function forceDeleted(Booking $booking): void
    {
        //
    }
}
