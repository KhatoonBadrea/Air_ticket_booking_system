<?php

namespace App\Services\Flight;

use App\Models\Flight;
use Illuminate\Support\Facades\Log;
use Exception;

class FlightService
{
    /**
     * Get all flights.
     * @param array $data
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllFlights($data)
    {
        try {
            $perPage = $data['perPage'] ?? 10;
            $destination = $data['destination'] ?? null;
            $origin = $data['origin'] ?? null;
            $available_seats = isset($data['available_seats']) ? intval($data['available_seats']) : null;
            $departure_time = $data['departure_time'] ?? null;
    
            $query = Flight::query()
                ->when($destination, fn($query) => $query->FilterByDestination($destination))
                ->when($origin, fn($query) => $query->FilterByOrigin($origin))
                ->when($available_seats, fn($query) => $query->FilterByAvailableSeats($available_seats))
                ->when($departure_time, fn($query) => $query->FilterByDay($departure_time));
    
            Log::info('Generated SQL Query: ' . $query->toSql());
    
            return $query->paginate($perPage);
        } catch (Exception $e) {
            Log::error('Failed to fetch all flights: ' . $e->getMessage());
            throw new Exception('Failed to fetch all flights.');
        }
    }

    /**
     * Create a new flight.
     *
     * @param array $data
     * @return Flight
     * @throws Exception
     */
    public function createFlight(array $data)
    {
        try {
            return Flight::create($data);
        } catch (Exception $e) {
            Log::error('Failed to create flight: ' . $e->getMessage());
            throw new Exception('Failed to create flight.');
        }
    }

    /**
     * Update an existing flight.
     *
     * @param Flight $flight
     * @param array $data
     * @return Flight
     * @throws Exception
     */
    public function updateFlight(Flight $flight, array $data)
    {
        try {
            $flight->update($data);
            return $flight;
        } catch (Exception $e) {
            Log::error('Failed to update flight: ' . $e->getMessage());
            throw new Exception('Failed to update flight.');
        }
    }

    /**
     * Delete a flight (Soft Delete).
     *
     * @param Flight $flight
     * @return Flight
     * @throws Exception
     */
    public function deleteFlight(Flight $flight)
    {
        try {
            $flight->delete();
            return $flight;
        } catch (Exception $e) {
            Log::error('Failed to delete flight: ' . $e->getMessage());
            throw new Exception('Failed to delete flight.');
        }
    }

    /**
     * Restore a deleted flight (Soft Delete).
     *
     * @param int $id
     * @return Flight
     * @throws Exception
     */
    public function restoreFlight($id)
    {
        try {
            $flight = Flight::onlyTrashed()->findOrFail($id);
            $flight->restore();
            return $flight;
        } catch (Exception $e) {
            Log::error('Failed to restore flight: ' . $e->getMessage());
            throw new Exception('Failed to restore flight.');
        }
    }

    /**
     * Get all deleted flights (Soft Deleted).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws Exception
     */
    public function getDeleteFlight()
    {
        try {
            return Flight::onlyTrashed()->get();
        } catch (Exception $e) {
            Log::error('Failed to fetch deleted flights: ' . $e->getMessage());
            throw new Exception('Failed to fetch deleted flights.');
        }
    }

    /**
     * Permanently delete a flight (Force Delete).
     *
     * @param int $id
     * @return void
     * @throws Exception
     */
    public function forceDeleteFlight($id)
    {
        try {
            $flight = Flight::onlyTrashed()->findOrFail($id);
            $flight->forceDelete();
        } catch (Exception $e) {
            Log::error('Failed to force delete flight: ' . $e->getMessage());
            throw new Exception('Failed to force delete flight.');
        }
    }
}
