<?php

namespace App\Http\Controllers\Api\Booking;

use Exception;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Booking\BookingService;
use App\Http\Resources\Booking\BookingResource;
use App\Http\Requests\Booking\CancelBookingRequest;
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
        $this->authorize('viewAny', Booking::class);

        $data = [
            'perPage' => $request->input('per_page', 10),
        ];
        $bookings = $this->bookingService->getAllBookings($data);
        return $this->paginated($bookings, BookingResource::class, 'Bookings fetched successfully', 200);
    }


    //===================================================store

    /**
     * Store a newly created booking in storage.
     *
     * @param CreateBookingRequest $request
     * @return BookingResource
     */
    public function store(CreateBookingRequest $request)
    {
        $this->authorize('create', Booking::class);

        $result = $this->bookingService->createBooking($request->validated());
        if ($result['status'] === 'success') {
            return $this->success(new BookingResource($result['data']), $result['message'], $status = 201);
        } else {
            return $this->error($result['message'], 400);
        }
    }



    //==========================================show

    /**
     * Display the specified booking.
     *
     * @param Booking $booking
     * @return BookingResource
     */
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $result = $this->bookingService->getBooking($booking);
        return $this->success(new BookingResource($result), 'Booking data ', 200);
    }



    //==========================================update

    /**
     * Update the specified booking in storage.
     *
     * @param UpdateBookingRequest $request
     * @param Booking $booking
     * @return BookingResource
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $result = $this->bookingService->updateBooking($booking, $request->validated());
        if ($result['status'] === 'success') {
            return $this->success(new BookingResource($result['data']), $result['message']);
        } else {
            return $this->error($result['message'], 400);
        }
    }


    //===========================================cancel

    /**
     * Cancel a booking.
     *
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Booking $booking)
    {
        $this->authorize('cancel', $booking);

        $result = $this->bookingService->cancelBooking($booking);
        if ($result['status'] === 'success') {
            return response()->json([
                'status' => 'success',
                'message' => $result['message'],
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
            ], 400);
        }
    }


    //=======================================getCancelledBookings

    /**
     * Get all cancelled bookings.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCancelledBookings()
    {
        $result = $this->bookingService->getCancelled();

        if ($result['status'] === 'success') {
            return $this->success((BookingResource::collection($result['data'])), $result['message']);
        } else {
            return $this->error($result['message'], 400);
        }
    }

    //================================================deleteCancelledBookings
    /**
     * Delete all cancelled bookings.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCancelledBookings()
    {


        $result = $this->bookingService->deleteCancelledBookings();

        if ($result['status'] === 'success') {
            return $this->success(null, $result['message']);
        } else {
            return $this->error($result['message'], 400);
        }
    }

    //======================================================deletePendingBookingsBefore24Hours

    /**
     * Delete pending bookings before 24 hours of departure.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePendingBookingsBefore24Hours()
    {
        try {

            $result = $this->bookingService->deletePendingBookingsBefore24Hours();

            return response()->json([
                'status' => $result['status'],
                'message' => $result['message'],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
