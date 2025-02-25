<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmed</title>
</head>
<body>
    <h1>Booking Confirmed</h1>
    <p>Dear {{ $user->name }},</p>
   

    @php
        Log::info('Rendering booking details for user: ' . $user->email);
    @endphp

    <h2>Booking Details:</h2>
    <ul>
        <li><strong>Flight:</strong> {{ $booking->flight->origin }} to {{ $booking->flight->destination }}</li>
        <li><strong>Departure Time:</strong> {{ $booking->flight->departure_time }}</li>
        <li><strong>Arrival Time:</strong> {{ $booking->flight->arrival_time }}</li>
        <li><strong>Number of Seats:</strong> {{ $booking->number_of_seats }}</li>
        <li><strong>Status:</strong> {{ $booking->status }}</li>
        <li><strong>Payment Status:</strong> {{ $booking->payment_status }} </li>
        <li><strong>Your payment: </strong> {{$booking->payment->amount}} $</li>
    </ul>


    <p>Thank you for using our service!</p>
</body>
</html>