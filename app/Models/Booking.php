<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
     * Relationship: A booking has one payment.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }


    /**
     * Scope to retrieve cancelled bookings.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }


    /**
     * Scope to retrieve pending bookings before 24 hours of departure.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePendingBefore24Hours($query)
    {
        return $query->where('status', 'pending')
            ->whereHas('flight', function ($q) {
                $q->where('departure_time', '<=', Carbon::now()->addHours(24));
            });
    }

}
