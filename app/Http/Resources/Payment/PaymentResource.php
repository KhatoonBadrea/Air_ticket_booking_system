<?php

namespace App\Http\Resources\Payment;

use Illuminate\Http\Request;
use App\Http\Resources\Booking\BookingResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'amount' => $this->amount,
            'transaction_id' => $this->transaction_id,
            'status' => $this->status,
            'booking' =>  new BookingResource($this->whenLoaded(('booking'))),
        ];
    }
}
