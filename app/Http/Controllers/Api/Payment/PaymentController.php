<?php

namespace App\Http\Controllers\Api\Payment;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Jobs\SendBookingConfirmedEmail;
use App\Services\Payment\PaymentService;
use App\Http\Requests\Payment\PaymentRequest;
use App\Http\Resources\Payment\PaymentResource;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }


    /* process payment
     *
     * @param PaymentRequest $request
     * @return JsonResponse
     */
    public function processPayment(PaymentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->paymentService->processPayment($data);

        if ($result['status'] === 'success') {
            return $this->success(new PaymentResource($result['data']), $result['message']);
        } else {
            return $this->error($result['message'], 400); 
        }
    }


    /* استرداد المبلغ (Refund).
     *
     * @param int $paymentId
     * @return JsonResponse
     */
    // public function refundPayment(int $paymentId): JsonResponse
    // {
    //     $result = $this->paymentService->refundPayment($paymentId);

    //     return response()->json($result, $result['status'] === 'success' ? 200 : 400);
    // }


    /* عرض تفاصيل الدفع.
     *
     * @param int $paymentId
     * @return JsonResponse
     */
    //     public function showPaymentDetails(int $paymentId): JsonResponse
    //     {
    //         $payment = Payment::with('booking')->find($paymentId);

    //         if (!$payment) {
    //             return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
    //         }

    //         return response()->json(['status' => 'success', 'payment' => new PaymentResource($payment)]);
    //     }

    //     /
    //      * عرض جميع المدفوعات لحجز معين.
    //      *
    //      * @param int $bookingId
    //      * @return JsonResponse
    //      */
    //     public function showPaymentsForBooking(int $bookingId): JsonResponse
    //     {
    //         $booking = Booking::with('payments')->find($bookingId);

    //         if (!$booking) {
    //             return response()->json(['status' => 'error', 'message' => 'Booking not found'], 404);
    //         }

    //         return response()->json(['status' => 'success', 'payments' => PaymentResource::collection($booking->payments)]);
    //     }
}
