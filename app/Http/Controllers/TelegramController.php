<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{
    /**
     * Основной обработчик Webhook от Telegram.
     */
    public function webhook(Request $request)
    {
        Log::info('Telegram webhook received', ['data' => $request->all()]);
        $update = json_decode($request->getContent(), true);

        // Обработка callback_query (нажатий inline-кнопок)
        if (isset($update['callback_query'])) {
            $callbackQuery = $update['callback_query'];
            $chatId        = $callbackQuery['message']['chat']['id'];
            $data          = $callbackQuery['data'];

            // Сброс состояния при нажатии кнопок
            $this->resetState($chatId);

            if ($data === 'bind_telegram') {
                // Запрашиваем ввод email
                $this->sendMessageToUser($chatId, "Напишите свой email:");
                // Сохраняем состояние диалога – ожидаем email
                $this->setState($chatId, 'awaiting_email');
            } elseif ($data === 'my_profile') {
                $this->handleMyProfile($chatId, $callbackQuery['from']);
            } elseif ($data === 'add_task') {
                // Заглушка для добавления задачи
                $this->sendMessageToUser($chatId, "Функционал добавления задачи пока не реализован.");
            } elseif ($data === 'my_tasks') {
                // Заглушка для просмотра задач
                $this->sendMessageToUser($chatId, "Функционал просмотра задач пока не реализован.");
            }

            // Ответ на callback_query для скрытия "часиков"
            $this->answerCallbackQuery($callbackQuery['id']);
            return response()->json();
        }

        // Обработка текстовых сообщений
        if (isset($update['message'])) {
            $message          = $update['message'];
            $chatId           = $message['chat']['id'];
            $text             = trim($message['text'] ?? '');
            $telegramUsername = $message['from']['username'] ?? null;

            // Проверяем, если пользователь уже привязан (ищем по telegram_username)
            $userAlreadyBound = false;
            if ($telegramUsername) {
                $existingUser = User::where('telegram_username', $telegramUsername)->first();
                if ($existingUser) {
                    $userAlreadyBound = true;
                }
            }

            // Если пришла команда /start
            if ($text === '/start') {
                $this->resetState($chatId);
                if ($userAlreadyBound) {
                    $this->sendMainMenu($chatId);
                } else {
                    // Если аккаунт не привязан, выводим меню с опциями "Мой профиль" и "Привязать Telegram"
                    $keyboard = [
                        'inline_keyboard' => [
                            [
                                [
                                    'text'          => 'Мой профиль',
                                    'callback_data' => 'my_profile'
                                ],
                                [
                                    'text'          => 'Привязать Telegram',
                                    'callback_data' => 'bind_telegram'
                                ]
                            ]
                        ]
                    ];
                    $this->sendMessageToUser($chatId, "Выберите опцию:", $keyboard);
                }
                return response()->json();
            }

            // Проверяем, есть ли сохранённое состояние
            $state = $this->getState($chatId);

            // Если ожидаем email
            if ($state === 'awaiting_email') {
                if (filter_var($text, FILTER_VALIDATE_EMAIL)) {
                    $email = $text;
                    Log::info("Received email for binding", ['chat_id' => $chatId, 'email' => $email]);

                    $user = User::where('email', $email)->first();
                    if ($user) {
                        // Сохраняем Telegram-данные пользователя
                        $user->telegram_chat_id  = $chatId;
                        $user->telegram_username = $telegramUsername;
                        $user->save();

                        $this->sendMessageToUser($chatId, "✅ Телеграм успешно привязан к вашему аккаунту.");
                        $this->sendMainMenu($chatId);
                    } else {
                        $this->sendMessageToUser($chatId, "❌ Пользователь с таким email не найден. Зарегистрируйтесь на сайте или попробуйте другой email.");
                    }
                    // Сбрасываем состояние
                    $this->resetState($chatId);
                } else {
                    $this->sendMessageToUser($chatId, "❌ Некорректный email. Попробуйте снова:");
                }
                return response()->json();
            }

            // Если пользователь уже привязан и отправляет любое сообщение – показываем главное меню
            if ($userAlreadyBound) {
                $this->sendMessageToUser($chatId, "Выберите опцию из меню:");
                $this->sendMainMenu($chatId);
                return response()->json();
            }

            // Если не распознано, просим начать с команды /start
            $this->sendMessageToUser($chatId, "Неизвестная команда. Используйте /start для начала.");
        }

        return response()->json();
    }

    /**
     * Отправка информации о профиле пользователю.
     */
    private function handleMyProfile($chatId, array $telegramUser)
    {
        $telegramUsername = $telegramUser['username'] ?? null;
        if (!$telegramUsername) {
            $this->sendMessageToUser($chatId, "Не удалось определить ваш username. Попробуйте снова.");
            return;
        }

        $user = User::where('telegram_username', $telegramUsername)->first();
        if ($user) {
            $profileText = "Ваш профиль:\n" .
                           "Имя: " . ($user->name ?? 'Не указано') . "\n" .
                           "Email: " . ($user->email ?? 'Не указано') . "\n" .
                           "Привязан: " . ($user->telegram_username ? 'Да' : 'Нет');
            $this->sendMessageToUser($chatId, $profileText);
        } else {
            $this->sendMessageToUser($chatId, "Пользователь не найден. Сначала привяжите Telegram к вашему аккаунту.");
        }
    }

    /**
     * Отправка сообщения пользователю через Telegram.
     * Если передан $keyboard, он будет включён в сообщение.
     */
    private function sendMessageToUser($chatId, $message, $keyboard = null)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $url   = "https://api.telegram.org/bot{$token}/sendMessage";

        $payload = [
            'chat_id'    => $chatId,
            'text'       => $message,
            'parse_mode' => 'HTML',
        ];

        if ($keyboard) {
            $payload['reply_markup'] = json_encode($keyboard);
        }

        Log::info('Sending message to Telegram', ['chat_id' => $chatId, 'message' => $message]);
        Http::post($url, $payload);
    }

    /**
     * Отправка главного меню для привязанного пользователя.
     */
    private function sendMainMenu($chatId)
    {
        $keyboard = [
            'inline_keyboard' => [
                [
                    [
                        'text'          => 'Добавить задачу',
                        'callback_data' => 'add_task'
                    ],
                    [
                        'text'          => 'Мои задачи',
                        'callback_data' => 'my_tasks'
                    ]
                ],
                [
                    [
                        'text'          => 'Мой профиль',
                        'callback_data' => 'my_profile'
                    ]
                ]
            ]
        ];
        $this->sendMessageToUser($chatId, "Главное меню:", $keyboard);
    }

    /**
     * Отправка ответа на callback_query для скрытия "часиков".
     */
    private function answerCallbackQuery($callbackQueryId)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $url   = "https://api.telegram.org/bot{$token}/answerCallbackQuery";

        Http::post($url, [
            'callback_query_id' => $callbackQueryId,
        ]);
    }

    /**
     * Сохранение состояния диалога в кэше.
     */
    private function setState($chatId, $state)
    {
        // Сохраняем состояние на 10 минут (можно изменить время жизни)
        Cache::put("telegram_state:{$chatId}", $state, now()->addMinutes(10));
    }

    /**
     * Получение сохранённого состояния диалога.
     */
    private function getState($chatId)
    {
        return Cache::get("telegram_state:{$chatId}");
    }

    /**
     * Сброс состояния диалога.
     */
    private function resetState($chatId)
    {
        Cache::forget("telegram_state:{$chatId}");
    }
}
