<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Booking;

class BookingPolicy
{
    /**
     * Determine whether the user can view any bookings.
     */
    public function index(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can view the booking.
     */
    public function show(User $user, Booking $booking): bool
    {
        return in_array($user->role, ['admin', 'manager']) || $user->id === $booking->user_id;
    }

    /**
     * Determine whether the user can create bookings.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the booking.
     */
    public function update(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id;
    }

    /**
     * Determine if the user can cancel the booking.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Booking $booking
     * @return bool
     */
    public function cancel(User $user, Booking $booking)
    {
        return $user->id === $booking->user_id;
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return in_array($user->role, ['admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return in_array($user->role, ['admin']);
    }
}
