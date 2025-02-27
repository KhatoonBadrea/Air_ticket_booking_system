<?php

namespace App\Http\Requests\Booking;

use App\Rules\BookingEditableRule;
use Illuminate\Foundation\Http\FormRequest;

class CancelBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $booking = $this->route('booking'); 

        return [
            '*' => [new BookingEditableRule($booking)],
        ];
    }
}
