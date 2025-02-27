<?php

namespace App\Http\Controllers\Api\Flight;

use App\Models\Flight;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Flight\FlightResource;
use App\Services\Flight\FlightService;
use App\Http\Requests\Flight\CreateFlightRequest;
use App\Http\Requests\Flight\UpdateFlightRequest;

class FlightController extends Controller
{
    protected $flightService;

    /**
     * Constructor to inject the FlightService.
     *
     * @param FlightService $flightService
     */
    public function __construct(FlightService $flightService)
    {
        $this->flightService = $flightService;
    }

    //================================================Display a listing of all flights

    /**
     * Display a listing of all flights.
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing query parameters.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $data = [
            'perPage' => $request->input('per_page', 10),
            'destination' => $request->input('destination'),
            'origin' => $request->input('origin'),
            'available_seats' => $request->input('available_seats'),
            'departure_time' => $request->input('departure_time'),
        ];

        $flights = $this->flightService->getAllFlights($data);

        return $this->paginated($flights, FlightResource::class, 'Flights fetched successfully', 200);
    }



    //=================================================store


    /**
     * Store a newly created flight in storage.
     *
     * @param CreateFlightRequest $request
     * @return FlightResource
     */
    public function store(CreateFlightRequest $request)
    {
        $flight = $this->flightService->createFlight($request->validated());
        return $this->success(new FlightResource($flight), 'Flight created successfully', 201);
    }


    //==============================================show

    /**
     * Display the specified flight.
     *
     * @param Flight $flight
     * @return FlightResource
     */
    public function show(Flight $flight)
    {
        return $this->success(new FlightResource($flight), 'Flight data ', 200);
    }


    //=========================================update


    /**
     * Update the specified flight in storage.
     *
     * @param UpdateFlightRequest $request
     * @param Flight $flight
     * @return FlightResource
     */
    public function update(UpdateFlightRequest $request, Flight $flight)
    {
        $updatedFlight = $this->flightService->updateFlight($flight, $request->validated());
        return $this->success(new FlightResource($updatedFlight), 'Flight updated successfully', 200);
    }

    //==========================================destroy

    /**
     * Remove the specified flight from storage (Soft Delete).
     *
     * @param Flight $flight
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Flight $flight)
    {
        $this->flightService->deleteFlight($flight);
        return $this->success(null, 'Flight deleted successfully', 200);
    }



    //==========================================getDelete

    /**
     * Get all deleted flights (Soft Deleted).
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getDeleted()
    {
        $flights = $this->flightService->getDeleteFlight();
        return $this->success(FlightResource::collection($flights), 'Deleted flight retrieved successfully.');
    }

    //================================================restore

    /**
     * Restore a deleted flight (Soft Delete).
     *
     * @param int $id
     * @return FlightResource
     */
    public function restore($id)
    {
        $flight = $this->flightService->restoreFlight($id);
        return $this->success(new FlightResource($flight), 'Flight restored successfully.');
    }

    //=================================================forceDelete

    /**
     * Permanently delete a flight (Force Delete).
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete($id)
    {
        $this->flightService->forceDeleteFlight($id);
        return $this->success(null, 'Flight permanently deleted');
    }
}
