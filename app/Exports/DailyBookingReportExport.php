<?php

namespace App\Exports;

use App\Models\Booking;
use App\Models\BookingActivityLog;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class DailyBookingReportExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return BookingActivityLog::whereDate('created_at', today())
            ->get()
            ->map(function ($log) {
                return [
                    'Booking ID'      => $log->booking_id,
                    'User ID'         => $log->user_id,
                    'Action'          => $log->action,
                    'Changes'         => json_encode($log->changes), // حولنا JSON لسطر واحد
                    'Created At'      => $log->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Booking ID',
            'User ID',
            'Action',
            'Changes',
            'Created At',
        ];
    }
}
