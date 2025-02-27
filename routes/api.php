<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Flight\FlightController;
use App\Http\Controllers\Api\Booking\BookingController;
use App\Http\Controllers\Api\Payment\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
});


Route::get('flights', [FlightController::class, 'index']);

Route::middleware('admin')->group(function () {
    Route::apiResource('flights', FlightController::class)->except(['index']);

    Route::post('/flights/{id}/restore', [FlightController::class, 'restore']);
    Route::get('deleted_flight', [FlightController::class, 'getDeleted']);
    Route::delete('/flights/{id}/force-delete', [FlightController::class, 'forceDelete']);
});

Route::middleware(['auth:api'])->group(function () {

    Route::apiResource('bookings', BookingController::class);

    Route::post('/bookings/{booking}/restore', [BookingController::class, 'restore']);
    Route::delete('/bookings/{booking}/force-delete', [BookingController::class, 'forceDelete']);
    Route::delete('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
});


Route::middleware(['auth:api'])->group(function () {
Route::post('/process-payment', [PaymentController::class, 'processPayment']);
Route::put('/payments/{payment}', [PaymentController::class, 'updatePayment']);

});