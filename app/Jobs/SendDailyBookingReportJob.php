<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
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
            $fileName = 'daily_booking_report_' . now()->format('Y-m-d') . '.xlsx';
            $path = storage_path('app/' . $fileName);

            Excel::store(new DailyBookingReportExport, $fileName);

            Mail::raw('Daily booking report attached.', function ($message) use ($path, $fileName) {
                $message->to(config('mail.admin_email'))
                    ->subject('Daily Booking Report')
                    ->attach($path);
            });

            Log::info('Daily report generated');
        } catch (\Throwable $e) {
            Log::error('Daily report failed: ' . $e->getMessage());
            throw $e; // مهم لإظهار الخطأ في queue
        }
    }
}
