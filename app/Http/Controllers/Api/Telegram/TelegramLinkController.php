<?php

namespace App\Http\Controllers\Api\Telegram;

use App\Http\Controllers\Controller;
use App\Http\Requests\Telegram\LinkTelegramRequest;
use Illuminate\Http\Request;

class TelegramLinkController extends Controller
{
    public function link(LinkTelegramRequest $request)
    {
        $request->user()->update([
            'telegram_chat_id' => $request->telegram_chat_id,
            'telegram_notifications_enabled' => true,
        ]);

        return response()->json(['message' => 'تم ربط حساب تليجرام بنجاح']);
    }

    public function unlink(Request $request)
    {
        $request->user()->update([
            'telegram_notifications_enabled' => false,
        ]);

        return response()->json(['message' => 'تم إيقاف إشعارات تليجرام']);
    }
}
