<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'flight_id',
        'status',
        'payment_status',
        'number_of_seats'
    ];

    /**
     * Relationship: A booking belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Relationship: A booking belongs to a Flight.
     */
    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    /**
     * Relationship: A booking has many payment.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
