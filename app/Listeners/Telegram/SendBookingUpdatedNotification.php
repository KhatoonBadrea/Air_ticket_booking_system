<?php

namespace App\Listeners\Telegram;

use App\Events\BookingUpdated;
use App\Services\TelegramNotification\TelegramNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBookingUpdatedNotification implements ShouldQueue
{
    public function __construct(
        protected TelegramNotificationService $telegram
    ) {}

    public function handle(BookingUpdated $event): void
    {
        $user = $event->booking->user;

        if (!$user->telegram_notifications_enabled || !$user->telegram_chat_id) {
            return;
        }

        $message = "✏️ <b>تم تعديل حجزك</b>\n\n"
            . "رقم الحجز: #{$event->booking->id}\n"
            . "يرجى مراجعة التفاصيل الجديدة من حسابك.";

        $this->telegram->sendMessage($user->telegram_chat_id, $message);
    }
}
