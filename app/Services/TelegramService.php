<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected $token;
    protected $chatId;

    public function __construct()
    {
        // Токен бота и ID канала из .env
        $this->token = env('TELEGRAM_BOT_TOKEN'); 
        $this->chatId = env('TELEGRAM_CHANNEL_ID');

    }

    public function sendMessage($message)
{
    if (empty($this->token) || empty($this->chatId)) {
        \Log::error("TelegramService: отсутствует токен или ID канала.");
        return false;
    }

    $url = "https://api.telegram.org/bot{$this->token}/sendMessage";

    $response = Http::post($url, [
        'chat_id' => $this->chatId,
        'text' => $message,
        'parse_mode' => 'Markdown',
    ]);

    if ($response->failed()) {
        \Log::error("Ошибка Telegram: " . $response->body());
        return false;
    }

    return true;
}
}
