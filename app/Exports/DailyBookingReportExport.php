<?php

namespace App\Exports;

use App\Models\BookingActivityLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DailyBookingReportExport implements FromQuery, WithHeadings
{
    public function query()
    {
        return BookingActivityLog::query()
            ->whereDate('created_at', today())
            ->select([
                'id',
                'booking_id',
                'user_id',
                'action',
                'created_at',
            ]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Booking ID',
            'User ID',
            'Action',
            'Created At',
        ];
    }
}
