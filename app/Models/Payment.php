<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
    ];

    /**
     * Relationship: A payment belongs to booking .
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
