<?php

namespace App\Listeners\Telegram;

use App\Events\BookingConfirmed;
use App\Services\TelegramNotification\TelegramNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBookingConfirmedNotification implements ShouldQueue
{
    public function __construct(
        protected TelegramNotificationService $telegram
    ) {}

    public function handle(BookingConfirmed $event): void
    {
        $user = $event->booking->user;

        if (!$user->telegram_notifications_enabled || !$user->telegram_chat_id) {
            return;
        }

        $message = "🎉 <b>تم تأكيد حجزك</b>\n\n"
            . "رقم الحجز: #{$event->booking->id}\n"
            . "الحالة: مؤكد ✅";

        $this->telegram->sendMessage($user->telegram_chat_id, $message);
    }
}
