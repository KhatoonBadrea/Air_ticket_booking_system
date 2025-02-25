<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
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
        return [
            // 'user_id' => 'required|exists:users,id',
            'flight_id' => 'required|exists:flights,id',
            'number_of_seats'=>'required|integer|min:0',
            // 'status' => 'nullable|in:pending,confirmed,cancelled',
            // 'payment_status' => 'nullable|in:paid,unpaid,refunded',
        ];
    }
}
