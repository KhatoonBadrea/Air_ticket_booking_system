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
}
