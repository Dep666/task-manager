<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

class TelegramController extends Controller
{
    /**
     * Основной обработчик Webhook от Telegram.
     */
    public function webhook(Request $request)
    {
        try {
            // Подробное логирование входящего запроса
            Log::info('Telegram webhook received', [
                'data' => $request->all(),
                'headers' => $request->header(),
                'method' => $request->method(),
                'ip' => $request->ip()
            ]);

            // Проверка токена
            $token = env('TELEGRAM_BOT_TOKEN');
            if (empty($token)) {
                Log::error('Telegram bot token is empty. Check .env file');
                return response()->json(['error' => 'Configuration error'], 500);
            }

            $content = $request->getContent();
            Log::info('Raw request content', ['content' => $content]);

            if (empty($content)) {
                Log::warning('Empty request content');
                return response()->json(['error' => 'Empty request'], 400);
            }

            $update = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON decode error', ['error' => json_last_error_msg()]);
                return response()->json(['error' => 'Invalid JSON'], 400);
            }

            // Обработка callback_query (нажатий inline-кнопок)
            if (isset($update['callback_query'])) {
                $callbackQuery = $update['callback_query'];
                $chatId        = $callbackQuery['message']['chat']['id'];
                $data          = $callbackQuery['data'];

                Log::info('Processing callback query', ['chat_id' => $chatId, 'data' => $data]);

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
                return response()->json(['status' => 'success']);
            }

            // Обработка текстовых сообщений
            if (isset($update['message'])) {
                $message          = $update['message'];
                $chatId           = $message['chat']['id'];
                $text             = trim($message['text'] ?? '');
                $telegramUsername = $message['from']['username'] ?? null;

                Log::info('Processing message', [
                    'chat_id' => $chatId, 
                    'text' => $text, 
                    'username' => $telegramUsername
                ]);

                // Проверяем, если пользователь уже привязан (ищем по telegram_username)
                $userAlreadyBound = false;
                if ($telegramUsername) {
                    $existingUser = User::where('telegram_username', $telegramUsername)->first();
                    if ($existingUser) {
                        $userAlreadyBound = true;
                        Log::info('User already bound', ['user_id' => $existingUser->id]);
                    }
                }

                // Если пришла команда /start
                if ($text === '/start') {
                    Log::info('Received /start command', ['chat_id' => $chatId]);
                    $this->resetState($chatId);
                    if ($userAlreadyBound) {
                        Log::info('Sending main menu to bound user');
                        $this->sendMainMenu($chatId);
                    } else {
                        Log::info('Sending initial menu to new user');
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
                    return response()->json(['status' => 'success']);
                }

                // Проверяем, есть ли сохранённое состояние
                $state = $this->getState($chatId);
                Log::info('Current user state', ['chat_id' => $chatId, 'state' => $state]);

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
                            Log::info("User bound successfully", ['user_id' => $user->id]);

                            $this->sendMessageToUser($chatId, "✅ Телеграм успешно привязан к вашему аккаунту.");
                            $this->sendMainMenu($chatId);
                        } else {
                            Log::info("User with email not found", ['email' => $email]);
                            $this->sendMessageToUser($chatId, "❌ Пользователь с таким email не найден. Зарегистрируйтесь на сайте или попробуйте другой email.");
                        }
                        // Сбрасываем состояние
                        $this->resetState($chatId);
                    } else {
                        Log::info("Invalid email format", ['text' => $text]);
                        $this->sendMessageToUser($chatId, "❌ Некорректный email. Попробуйте снова:");
                    }
                    return response()->json(['status' => 'success']);
                }

                // Если пользователь уже привязан и отправляет любое сообщение – показываем главное меню
                if ($userAlreadyBound) {
                    Log::info("Showing menu to bound user", ['chat_id' => $chatId]);
                    $this->sendMessageToUser($chatId, "Выберите опцию из меню:");
                    $this->sendMainMenu($chatId);
                    return response()->json(['status' => 'success']);
                }

                // Если не распознано, просим начать с команды /start
                Log::info("Unknown command", ['text' => $text]);
                $this->sendMessageToUser($chatId, "Неизвестная команда. Используйте /start для начала.");
            }

            return response()->json(['status' => 'success']);
        } catch (Exception $e) {
            Log::error('Exception in webhook processing', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Отправка информации о профиле пользователю.
     */
    private function handleMyProfile($chatId, array $telegramUser)
    {
        try {
            $telegramUsername = $telegramUser['username'] ?? null;
            if (!$telegramUsername) {
                Log::warning("Username not found in profile", ['user' => $telegramUser]);
                $this->sendMessageToUser($chatId, "Не удалось определить ваш username. Попробуйте снова.");
                return;
            }

            $user = User::where('telegram_username', $telegramUsername)->first();
            if ($user) {
                Log::info("Profile found for user", ['user_id' => $user->id]);
                $profileText = "Ваш профиль:\n" .
                            "Имя: " . ($user->name ?? 'Не указано') . "\n" .
                            "Email: " . ($user->email ?? 'Не указано') . "\n" .
                            "Привязан: " . ($user->telegram_username ? 'Да' : 'Нет');
                $this->sendMessageToUser($chatId, $profileText);
            } else {
                Log::warning("User not found for telegram username", ['username' => $telegramUsername]);
                $this->sendMessageToUser($chatId, "Пользователь не найден. Сначала привяжите Telegram к вашему аккаунту.");
            }
        } catch (Exception $e) {
            Log::error('Error in handleMyProfile', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Отправка сообщения пользователю через Telegram.
     * Если передан $keyboard, он будет включён в сообщение.
     */
    private function sendMessageToUser($chatId, $message, $keyboard = null)
    {
        try {
            $token = env('TELEGRAM_BOT_TOKEN');
            
            if (empty($token)) {
                Log::error('Telegram token is empty. Cannot send message.');
                return false;
            }
            
            $url = "https://api.telegram.org/bot{$token}/sendMessage";

            $payload = [
                'chat_id'    => $chatId,
                'text'       => $message,
                'parse_mode' => 'HTML',
            ];

            if ($keyboard) {
                $payload['reply_markup'] = json_encode($keyboard);
            }

            Log::info('Sending message to Telegram', [
                'chat_id' => $chatId, 
                'message' => $message, 
                'has_keyboard' => !is_null($keyboard)
            ]);
            
            $response = Http::timeout(10)->post($url, $payload);
            
            if ($response->successful()) {
                Log::info('Message sent successfully', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('Failed to send message', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (Exception $e) {
            Log::error('Exception when sending message', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Отправка главного меню для привязанного пользователя.
     */
    private function sendMainMenu($chatId)
    {
        try {
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
        } catch (Exception $e) {
            Log::error('Error in sendMainMenu', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Отправка ответа на callback_query для скрытия "часиков".
     */
    private function answerCallbackQuery($callbackQueryId)
    {
        try {
            $token = env('TELEGRAM_BOT_TOKEN');
            if (empty($token)) {
                Log::error('Telegram token is empty. Cannot answer callback query.');
                return false;
            }
            
            $url = "https://api.telegram.org/bot{$token}/answerCallbackQuery";

            $response = Http::timeout(5)->post($url, [
                'callback_query_id' => $callbackQueryId,
            ]);
            
            if (!$response->successful()) {
                Log::error('Failed to answer callback query', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }
            
            return $response->successful();
        } catch (Exception $e) {
            Log::error('Exception in answerCallbackQuery', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Сохранение состояния диалога в кэше.
     */
    private function setState($chatId, $state)
    {
        try {
            // Сохраняем состояние на 10 минут (можно изменить время жизни)
            Cache::put("telegram_state:{$chatId}", $state, now()->addMinutes(10));
            Log::info('State saved', ['chat_id' => $chatId, 'state' => $state]);
        } catch (Exception $e) {
            Log::error('Error saving state', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Получение сохранённого состояния диалога.
     */
    private function getState($chatId)
    {
        try {
            $state = Cache::get("telegram_state:{$chatId}");
            Log::info('Retrieved state', ['chat_id' => $chatId, 'state' => $state]);
            return $state;
        } catch (Exception $e) {
            Log::error('Error getting state', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Сброс состояния диалога.
     */
    private function resetState($chatId)
    {
        try {
            Cache::forget("telegram_state:{$chatId}");
            Log::info('State reset', ['chat_id' => $chatId]);
        } catch (Exception $e) {
            Log::error('Error resetting state', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Тестовый метод для проверки соединения с API Telegram
     */
    public function testConnection()
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        if (empty($token)) {
            return response()->json([
                'success' => false,
                'message' => 'Токен бота не найден в .env файле'
            ]);
        }
        
        try {
            $url = "https://api.telegram.org/bot{$token}/getMe";
            $response = Http::timeout(10)->get($url);
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Соединение с API Telegram успешно установлено',
                    'bot_info' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при соединении с API Telegram',
                    'error' => $response->body()
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Исключение при соединении с API Telegram',
                'error' => $e->getMessage()
            ]);
        }
    }
}
