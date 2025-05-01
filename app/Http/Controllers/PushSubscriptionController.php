<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use NotificationChannels\WebPush\PushSubscription;

class PushSubscriptionController extends Controller
{
    /**
     * Создание новой подписки на уведомления
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::info('Получен запрос на создание подписки', [
            'user_id' => Auth::id(),
            'endpoint' => $request->has('endpoint') ? substr($request->endpoint, 0, 50) . '...' : 'не указан'
        ]);

        try {
            $validator = Validator::make($request->all(), [
                'endpoint' => 'required|string',
                'keys.auth' => 'required|string',
                'keys.p256dh' => 'required|string',
            ]);
            
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }
        } catch (\Exception $e) {
            Log::error('Ошибка валидации в PushSubscriptionController::store', [
                'error' => $e->getMessage(),
                'payload' => $request->all()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Ошибка валидации: ' . $e->getMessage()
            ], 422);
        }

        $user = Auth::user();

        try {
            $endpoint = $request->endpoint;
            $key = $request->keys['p256dh'];
            $token = $request->keys['auth'];

            Log::info('Данные подписки подготовлены', [
                'user_id' => $user->id,
                'endpoint' => substr($endpoint, 0, 50) . '...',
            ]);

            // Проверяем, есть ли уже такая подписка
            $existing = PushSubscription::where('endpoint', $endpoint)->first();
            if ($existing) {
                Log::info('Найдена существующая подписка с таким же endpoint, обновляем', [
                    'subscription_id' => $existing->id,
                    'old_user_id' => $existing->user_id,
                    'new_user_id' => $user->id
                ]);
                
                // Если подписка принадлежит другому пользователю, меняем владельца
                if ($existing->user_id != $user->id) {
                    $existing->user_id = $user->id;
                    $existing->save();
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Существующая подписка обновлена',
                    'is_new' => false
                ]);
            }

            // Сохраняем подписку для пользователя
            $subscription = $user->updatePushSubscription($endpoint, $key, $token);

            Log::info('Создана подписка на Web Push уведомления', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'endpoint' => substr($endpoint, 0, 50) . '...',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Подписка успешно создана',
                'is_new' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка при создании подписки на Push уведомления', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false, 
                'message' => 'Серверная ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удаление подписки на уведомления
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        Log::info('Получен запрос на удаление подписки', [
            'user_id' => Auth::id(),
            'endpoint' => $request->has('endpoint') ? substr($request->endpoint, 0, 50) . '...' : 'не указан'
        ]);

        try {
            $validator = Validator::make($request->all(), [
                'endpoint' => 'required|string',
            ]);
            
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }
        } catch (\Exception $e) {
            Log::error('Ошибка валидации в PushSubscriptionController::destroy', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Ошибка валидации: ' . $e->getMessage()
            ], 422);
        }

        $user = Auth::user();
        $endpoint = $request->endpoint;

        try {
            // Получаем подписку по endpoint
            $subscription = PushSubscription::where('endpoint', $endpoint)->first();

            if (!$subscription) {
                Log::warning('Подписка не найдена для удаления', [
                    'user_id' => $user->id,
                    'endpoint' => substr($endpoint, 0, 50) . '...'
                ]);
                
                return response()->json([
                    'success' => false, 
                    'message' => 'Подписка не найдена'
                ], 404);
            }

            // Удаляем проверку принадлежности подписки пользователю
            // Любой пользователь может удалить подписку со своего устройства
            Log::info('Удаление подписки на Push уведомления', [
                'user_id' => $user->id,
                'subscription_user_id' => $subscription->user_id,
                'endpoint' => substr($endpoint, 0, 50) . '...'
            ]);

            // Удаляем подписку
            $subscription->delete();
            
            Log::info('Удалена подписка на Web Push уведомления', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'endpoint' => substr($endpoint, 0, 50) . '...'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Подписка успешно удалена'
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка при удалении подписки на Push уведомления', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false, 
                'message' => 'Серверная ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Альтернативный способ удаления подписки через POST (для серверов, не поддерживающих DELETE)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyViaPost(Request $request)
    {
        return $this->destroy($request);
    }

    /**
     * Обработка GET-запросов (для предварительной проверки браузером)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Используйте POST для создания и DELETE для удаления подписок.'
        ], 200);
    }
} 