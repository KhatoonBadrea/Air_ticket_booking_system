<?php

namespace App\Services\Reports;

use App\Models\BookingActivityLog;

class DailyBookingReportService
{
    public function getDailyLogs()
    {
        return BookingActivityLog::whereDate('created_at', today())
            ->with('booking', 'user')
            ->orderBy('created_at')
            ->get();
    }
}
