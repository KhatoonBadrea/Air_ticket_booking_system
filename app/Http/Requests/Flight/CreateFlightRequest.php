<?php

namespace App\Http\Requests\Flight;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateFlightRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }










    protected function prepareForValidation()
    {
        if ($this->departure_time) {
            try {
                $this->merge([
                    'departure_time' => Carbon::createFromFormat('Y-m-d H:i:s', $this->departure_time)->format('Y-m-d H:i:s'),
                ]);
            } catch (\Exception $e) {
                try {
                    $this->merge([
                        'departure_time' => Carbon::createFromFormat('d/m/Y H:i', $this->departure_time)->format('Y-m-d H:i:s'),
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception('Invalid date format for departure_time. Expected formats: Y-m-d H:i:s or d/m/Y H:i');
                }
            }
        }

        if ($this->arrival_time) {
            try {
                $this->merge([
                    'arrival_time' => Carbon::createFromFormat('Y-m-d H:i:s', $this->arrival_time)->format('Y-m-d H:i:s'),
                ]);
            } catch (\Exception $e) {
                try {
                    $this->merge([
                        'arrival_time' => Carbon::createFromFormat('d/m/Y H:i', $this->arrival_time)->format('Y-m-d H:i:s'),
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception('Invalid date format for arrival_time. Expected formats: Y-m-d H:i:s or d/m/Y H:i');
                }
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'price'   => 'required|numeric|min:1',
            'origin'  => 'required|string|max:255',
            'destination'     => 'required|string|max:255',
            'arrival_time'    => 'required|date|after:departure_time',
            'departure_time'  => 'required|date',
            'available_seats' => 'required|integer|min:0',
        ];
    }


    public function attributes(): array
    {
        return [
            'price'      => 'Price',
            'origin'     => 'Origin',
            'destination'    => 'Destination',
            'arrival_time'   => 'Arrival Time',
            'departure_time' => 'Departure Time',
            'available_seats' => 'Available Seats'

        ];
    }

    public function messages(): array
    {
        return [
            'max'            => 'The :attribute field should not exceed 255 characters',
            'date'           => 'The :attribute field must be a valid date.',
            'string'         => 'The :attribute field must be a valid string.',
            'required'       => 'The :attribute field is required.',
            'price.min'      => 'The price field must be at least 1.',
            'price.numeric'           => ' The price field must be a number.',
            'arrival_time.after'      => 'The arrival time field must be after the departure time',
            'available_seats.min'     => 'The field of available seats must be a positive number.',
            'available_seats.integer' => 'The field of available seats must be an integer.',
        ];
    }

    /**
     * Handles failed validation attempts.
     * Logs validation failures with relevant details for debugging and monitoring.
     * Uses parent's failedValidation to maintain consistent error response format.
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator): void
    {
        // Log validation failure with relevant details
        Log::error('Validation failed for RegisterRequest', [
            'errors' => $validator->errors()->toArray(),
        ]);

        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
