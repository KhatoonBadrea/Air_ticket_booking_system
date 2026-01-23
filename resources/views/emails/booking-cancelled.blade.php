<!DOCTYPE html>
<html>
<head>
    <title>Booking Cancellation</title>
</head>
<body>
    <h1>Your Booking Has Been Cancelled</h1>
    <p>Dear {{ $booking->user->name }},</p>
    <p>We regret to inform you that your booking for flight from {{ $booking->flight->origin }} to {{ $booking->flight->destination }} has been cancelled.</p>
    <p>The amount of <p>
    Amount:
    {{ optional($booking->payment)->amount ?? 'Not available' }}
</p>
 has been refunded to your account.</p>
    <p>Thank you for using our service.</p>
</body>
</html>