<?php

namespace App\Http\Resources\Booking;

use Illuminate\Http\Request;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Flight\FlightResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'user'    => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),

            'flight' => $this->whenLoaded('flight', function () {
                return new FlightResource($this->flight);
            }),

            'number of seats' => $this->number_of_seats,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment' => $this->whenLoaded('payment', function () {
                return [
                    'amount' => $this->payment->amount,
                    'transaction_id' => $this->payment->transaction_id,
                    'status' => $this->payment->status,
                ];
            }),
        ];
    }
}
