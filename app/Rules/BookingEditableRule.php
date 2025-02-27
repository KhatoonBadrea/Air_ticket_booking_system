<?php

namespace App\Rules;

use Carbon\Carbon;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Rule;

class BookingEditableRule implements Rule
{
    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $departureTime = $this->booking->flight->departure_time;
        $timeRemaining = Carbon::parse($departureTime)->diffInHours(Carbon::now());
        Log::info('Time Remaining in Hours:', ['time_remaining' => $timeRemaining]);

        return $timeRemaining > 24;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You cannot modify this booking as the flight departs within 24 hours.';
    }
}
