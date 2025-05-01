<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Notifications\DeadlinePushNotification;

class NotifyWebPushDeadlineCommand extends Command
{
    protected $signature = 'notify:webpush-deadlines';
    protected $description = 'Отправка Web Push уведомлений пользователям о приближении дедлайна задач (за день и за час)';

    public function handle()
    {
        Log::info('Запуск команды notify:webpush-deadlines');

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

        // Получаем ID статусов "выполнено"
        $completedStatusIds = TaskStatus::where('name', 'like', '%выполнен%')
            ->orWhere('name', 'like', '%завершен%')
            ->pluck('id');
            
        Log::info('Исключаемые статусы задач:', ['completed_status_ids' => $completedStatusIds]);

        // Получаем всех пользователей с подписками на уведомления
        $users = User::all();
        $webPushSentCount = 0;

        foreach ($users as $user) {
            // Проверяем, есть ли у пользователя подписки на web-push
            if (!$user->pushSubscriptions()->exists()) {
                continue;
            }

            // Получаем задачи пользователя с дедлайнами в ближайшие день и час, исключая выполненные
            $tasksDay = Task::where('user_id', $user->id)
                ->whereBetween('deadline', [$notifyDayStart, $notifyDayEnd])
                ->where(function($query) use ($completedStatusIds) {
                    $query->whereNull('status_id')
                          ->orWhereNotIn('status_id', $completedStatusIds);
                })
                ->get();

            $tasksHour = Task::where('user_id', $user->id)
                ->whereBetween('deadline', [$notifyHourStart, $notifyHourEnd])
                ->where(function($query) use ($completedStatusIds) {
                    $query->whereNull('status_id')
                          ->orWhereNotIn('status_id', $completedStatusIds);
                })
                ->get();

            Log::info('Найденные задачи для пользователя:', [
                'user_id' => $user->id,
                'tasksDayCount' => $tasksDay->count(),
                'tasksHourCount' => $tasksHour->count()
            ]);

            // Обрабатываем уведомления
            foreach (['day' => $tasksDay, 'hour' => $tasksHour] as $interval => $tasks) {
                foreach ($tasks as $task) {
                    Log::info("Отправка Web Push уведомления пользователю:", [
                        'user_id' => $user->id,
                        'task_id' => $task->id,
                        'interval' => $interval
                    ]);

                    // Отправляем Web Push уведомление
                    $user->notify(new DeadlinePushNotification($task, $interval));
                    $webPushSentCount++;
                }
            }
        }

        Log::info('Команда notify:webpush-deadlines завершена', ['sent_notifications' => $webPushSentCount]);
        $this->info("Web Push уведомления отправлены: {$webPushSentCount}");
    }
} 