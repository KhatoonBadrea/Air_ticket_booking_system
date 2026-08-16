<?php

namespace App\Listeners\Telegram;

use App\Events\BookingCreated;
use App\Services\TelegramNotification\TelegramNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBookingCreatedNotification implements ShouldQueue
{
    public function __construct(
        protected TelegramNotificationService $telegram
    ) {}

    public function handle(BookingCreated $event): void
    {
        $user = $event->booking->user;

        if (!$user->telegram_notifications_enabled || !$user->telegram_chat_id) {
            return;
        }

        $message = $this->buildMessage($event->booking);

        $this->telegram->sendMessage($user->telegram_chat_id, $message);
    }

    protected function buildMessage($booking): string
    {
        return "✅ <b>تم إنشاء حجزك بنجاح</b>\n\n"
            // . "رقم الحجز: #{$booking->id}\n"
            . "التاريخ: {$booking->created_at->format('Y-m-d H:i')}\n\n"
            . "عدد المقاعد: {$booking->number_of_seats}\n"
            . "سيتم إعلامك فور تأكيد الحجز.";
    }
}
