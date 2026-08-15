<?php

namespace App\Jobs;

use App\Models\BookingActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Exports\DailyBookingReportExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendDailyBookingReportJob implements ShouldQueue
{

   
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   public function handle(): void
{
    try {
        Log::info('Daily CSV report job started');

        $fileName = 'daily_booking_report_' . now()->format('Y-m-d') . '.csv';

        Excel::store(
            new DailyBookingReportExport,
            $fileName,
            'local',
            \Maatwebsite\Excel\Excel::CSV
        );

        $path = storage_path('app/' . $fileName);

        Mail::raw('Daily booking report attached.', function ($message) use ($path) {
            $message->to(config('mail.admin_email'))
                ->subject('Daily Booking Report (CSV)')
                ->attach($path);
        });

     

        Log::info('Daily CSV report job finished successfully');

    } catch (\Throwable $e) {
        Log::error('Daily CSV report job failed', [
            'error' => $e->getMessage(),
        ]);

        throw $e;
    }
}

}
