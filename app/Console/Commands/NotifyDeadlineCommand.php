<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotifyDeadlineCommand extends Command
{
    protected $signature = 'notify:deadlines';
    protected $description = 'Уведомление пользователей о приближении дедлайна задач (за день и за час)';

    public function handle()
    {
        Log::info('Запуск команды notify:deadlines');

        $now = Carbon::now('Asia/Yekaterinburg');
        Log::info('Текущее время:', ['now' => $now->toDateTimeString()]);

        // Определяем временные интервалы
        $notifyDayStart  = $now->copy()->addDay()->startOfMinute();
        $notifyDayEnd    = $now->copy()->addDay()->endOfMinute();

        $notifyHourStart = $now->copy()->addHour()->startOfMinute();
        $notifyHourEnd   = $now->copy()->addHour()->endOfMinute();

        Log::info('Интервалы уведомлений:', [
            'notifyDayStart' => $notifyDayStart->toDateTimeString(),
            'notifyDayEnd'   => $notifyDayEnd->toDateTimeString(),
            'notifyHourStart'=> $notifyHourStart->toDateTimeString(),
            'notifyHourEnd'  => $notifyHourEnd->toDateTimeString(),
        ]);

        // Получаем всех пользователей с привязанным Telegram
        $users = User::whereNotNull('telegram_chat_id')->get();

        foreach ($users as $user) {
            // Получаем задачи пользователя с дедлайнами в ближайшие день и час
            $tasksDay = Task::where('user_id', $user->id)
                ->whereBetween('deadline', [$notifyDayStart, $notifyDayEnd])
                ->get();

            $tasksHour = Task::where('user_id', $user->id)
                ->whereBetween('deadline', [$notifyHourStart, $notifyHourEnd])
                ->get();

            Log::info('Найденные задачи для пользователя:', [
                'user_id' => $user->id,
                'tasksDayCount' => $tasksDay->count(),
                'tasksHourCount' => $tasksHour->count()
            ]);

            // Обрабатываем уведомления
            foreach (['day' => $tasksDay, 'hour' => $tasksHour] as $interval => $tasks) {
                foreach ($tasks as $task) {
                    $deadline = Carbon::parse($task->deadline);

$message = $interval === 'day'
    ? "⏰ Напоминаем, что дедлайн задачи \"{$task->title}\" наступит через 1 день ({$deadline->format('d.m.Y H:i')})."
    : "⏰ Внимание! Дедлайн задачи \"{$task->title}\" наступит через 1 час ({$deadline->format('d.m.Y H:i')}).";


                    Log::info("Отправка уведомления пользователю:", [
                        'user_id' => $user->id,
                        'chat_id' => $user->telegram_chat_id,
                        'task_id' => $task->id,
                        'interval' => $interval
                    ]);

                    // Отправляем сообщение в Telegram
                    $this->sendTelegramMessage($user->telegram_chat_id, $message);
                }
            }
        }

        Log::info('Команда notify:deadlines завершена');
        $this->info("Уведомления отправлены.");
    }

    private function sendTelegramMessage($chatId, $message)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $url   = "https://api.telegram.org/bot{$token}/sendMessage";

        $payload = [
            'chat_id'    => $chatId,
            'text'       => $message,
            'parse_mode' => 'HTML',
        ];

        Log::info('Отправка запроса в Telegram API:', ['url' => $url, 'payload' => $payload]);

        // Отправляем запрос в Telegram
        $response = Http::post($url, $payload);

        // Логируем ответ от Telegram
        if ($response->successful()) {
            Log::info('Ответ от Telegram API:', ['response' => $response->json()]);
        } else {
            Log::error('Ошибка при отправке сообщения в Telegram:', ['error' => $response->body()]);
        }
    }
}