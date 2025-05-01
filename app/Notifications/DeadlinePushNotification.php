<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use App\Models\Task;
use Carbon\Carbon;

class DeadlinePushNotification extends Notification
{
    protected $task;
    protected $interval;

    /**
     * Создание нового экземпляра уведомления.
     *
     * @param Task $task
     * @param string $interval 'day' или 'hour'
     * @return void
     */
    public function __construct(Task $task, $interval)
    {
        $this->task = $task;
        $this->interval = $interval;
    }

    /**
     * Получить каналы доставки уведомления.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    /**
     * Получить представление уведомления WebPush.
     *
     * @param  mixed  $notifiable
     * @return \NotificationChannels\WebPush\WebPushMessage
     */
    public function toWebPush($notifiable, $notification)
    {
        $deadline = Carbon::parse($this->task->deadline)->format('d.m.Y H:i');
        
        $title = $this->interval === 'day' 
            ? 'Напоминание о задаче'
            : 'Срочно! Приближается дедлайн';
        
        $body = $this->interval === 'day'
            ? "Напоминаем, что дедлайн задачи \"{$this->task->title}\" наступит через 1 день ({$deadline})."
            : "Внимание! Дедлайн задачи \"{$this->task->title}\" наступит через 1 час ({$deadline}).";

        return (new WebPushMessage)
            ->title($title)
            ->icon('/task-manager/public/images/notification-icon.png')
            ->body($body)
            ->badge('/task-manager/public/images/badge.png')
            ->data(['task_id' => $this->task->id])
            ->action('Просмотр задачи', '/task-manager/public/tasks/' . $this->task->id);
    }
} 