<?php

namespace App\Http\Controllers\Api\Booking;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Booking\BookingService;
use App\Http\Resources\Booking\BookingResource;
use App\Http\Requests\Booking\CreateBookingRequest;
use App\Http\Requests\Booking\UpdateBookingRequest;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Display a listing of all bookings.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $data = [
            'perPage' => $request->input('per_page', 10),
        ];
        $bookings = $this->bookingService->getAllBookings();
        return $this->paginated($bookings, BookingResource::class, 'Bookings fetched successfully', 200);
    }

    /**
     * Store a newly created booking in storage.
     *
     * @param CreateBookingRequest $request
     * @return BookingResource
     */
    public function store(CreateBookingRequest $request)
    {
        $booking = $this->bookingService->createBooking($request->validated());
        return $this->success(new BookingResource($booking), 'Booking created successfully', 201);
    }

    /**
     * Display the specified booking.
     *
     * @param Booking $booking
     * @return BookingResource
     */
    public function show(Booking $booking)
    {
        return $this->success(new BookingResource($booking), 'Booking data ', 200);
    }

    /**
     * Update the specified booking in storage.
     *
     * @param UpdateBookingRequest $request
     * @param Booking $booking
     * @return BookingResource
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $updatedBooking = $this->bookingService->updateBooking($booking, $request->validated());
        return $this->success(new BookingResource($updatedBooking), 'Booking updated successfully', 200);
    }

    /**
     * Remove the specified booking from storage (Soft Delete).
     *
     * @param Booking $booking
     * @return JsonResponse
     */
    public function destroy(Booking $booking)
    {
        $this->bookingService->deleteBooking($booking);
        return $this->success(null, 'Booking deleted successfully', 200);
    }


    /**
     * Get all deleted bookings (Soft Deleted).
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getDeleted()
    {
        $bookings = $this->bookingService->getDeletedBookings();
        return $this->success(BookingResource::collection($bookings), 'Deleted Booking retrieved successfully.');
    }


    /**
     * Restore a deleted booking (Soft Delete).
     *
     * @param int $id
     * @return BookingResource
     */
    public function restore($id)
    {
        $booking = $this->bookingService->restoreBooking($id);
        return $this->success(new BookingResource($booking), 'Booking restored successfully.');
    }

    /**
     * Permanently delete a booking (Force Delete).
     *
     * @param int $id
     * @return JsonResponse
     */
    public function forceDelete($id)
    {
        $this->bookingService->forceDeleteBooking($id);
        return $this->success(null, 'Booking permanently deleted');
    }
}
