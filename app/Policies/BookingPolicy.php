<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Booking;

class BookingPolicy
{
    /**
     * View all bookings (Admin / Manager)
     */
    public function viewAny(User $user): bool
    {
        return $user->can('manage booking');
    }

    /**
     * View single booking
     */
    public function view(User $user, Booking $booking): bool
    {
        return
            $user->can('manage booking') ||
            $user->id === $booking->user_id;
    }

    /**
     * Create booking
     */
    public function create(User $user): bool
    {
        return $user->can('create booking');
    }

    /**
     * Update booking
     */
    public function update(User $user, Booking $booking): bool
    {
        return
            $user->can('update booking') &&
            $user->id === $booking->user_id;
    }

    /**
     * Cancel booking
     */
    public function cancel(User $user, Booking $booking): bool
    {
        return
            $user->can('cancel booking') &&
            $user->id === $booking->user_id;
    }

    /**
     * Delete booking (Admin only)
     */
    public function delete(User $user, Booking $booking): bool
    {
        return $user->can('manage booking');
    }
}
