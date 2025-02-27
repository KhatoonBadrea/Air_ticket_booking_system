<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flight extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'origin',
        'destination',
        'departure_time',
        'arrival_time',
        'price',
        'available_seats',
    ];

    /**
     * Relationship: A Flight has many booking.
     *
     * @return HasMany
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Scope to filter by destination.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeFilterByDestination($query, $destination)
    {
        return $query->where('destination', $destination);
    }

    /**
     * Scope to filter by origin.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeFilterByOrigin($query, $origin)
    {
        return $query->where('origin', $origin);
    }


    /**
     * Scope to filter by available seats.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByAvailableSeats($query, $available_seats)
    {
        return $query->where('available_seats', '>=', $available_seats);
    }

    /**
     * Scope to filter by day.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeFilterByDay($query, $departure_time)
    {
        return $query->whereDate('departure_time', $departure_time);
    }


    /** * Check if there are enough available seats. */

    public function hasAvailableSeats(int $numberOfSeats): bool
    {
        return $this->available_seats >= $numberOfSeats;
    }
}
