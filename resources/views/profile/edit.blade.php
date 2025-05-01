@extends('layouts.app')

@section('content')
    <div class="py-6 bg-gray-100 dark:bg-gray-900 text-center">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Профиль</h1>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Обновление информации профиля -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="max-w-xl mx-auto">
                        @include('profile.partials.update-profile-information-form', ['user' => $user])
                    </div>
                </div>
                
                <!-- Кнопка для привязки Telegram -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="max-w-xl mx-auto">
                        <!-- Если у пользователя уже есть привязанный Telegram, показываем информацию, иначе кнопку -->
                        @if($user->telegram_username)
                            <p class="text-lg text-green-600 dark:text-green-400">Ваш аккаунт Telegram привязан</p>
                        @else
                        <button 
                            class="w-full py-3 px-4 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg shadow-sm hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 uppercase font-semibold"
                            onclick="window.location.href = 'https://t.me/team_task_manager_bot?start={{ urlencode(Auth::user()->email) }}'">
                            Привязать аккаунт Telegram
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Секция Web Push уведомлений -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="max-w-xl mx-auto">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Web Push уведомления
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Получайте уведомления о дедлайнах задач даже когда не находитесь на сайте.
                        </p>

                        <div class="mt-6">
                            <button id="enablePushBtn" 
                                class="py-3 px-4 bg-green-600 dark:bg-green-500 text-white rounded-lg shadow-sm hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150 uppercase font-semibold">
                                Подключить уведомления
                            </button>
                            
                            <button id="disablePushBtn" 
                                class="ml-3 py-3 px-4 bg-red-600 dark:bg-red-500 text-white rounded-lg shadow-sm hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150 uppercase font-semibold hidden">
                                Отключить уведомления
                            </button>
                            
                            <p id="pushStatus" class="mt-3 text-sm text-gray-600 dark:text-gray-400"></p>
                        </div>
                    </div>
                </div>

                <!-- Обновление пароля -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="max-w-xl mx-auto">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Удаление пользователя -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="max-w-xl mx-auto">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            const enablePushBtn = document.getElementById('enablePushBtn');
            const disablePushBtn = document.getElementById('disablePushBtn');
            const pushStatus = document.getElementById('pushStatus');
            
            // Обновляем статус для отображения отладочной информации
            function updateStatus(message, type = 'info') {
                const colorClass = type === 'error' ? 'text-red-600 dark:text-red-400' :
                                 type === 'success' ? 'text-green-600 dark:text-green-400' :
                                 type === 'warning' ? 'text-yellow-600 dark:text-yellow-400' :
                                 'text-gray-600 dark:text-gray-400';
                
                pushStatus.innerHTML = `<span class="${colorClass}">${message}</span>`;
            }
            
            // Проверяем, поддерживаются ли push-уведомления браузером
            if (!('serviceWorker' in navigator)) {
                updateStatus('Ваш браузер не поддерживает Service Worker.', 'error');
                enablePushBtn.disabled = true;
                return;
            }
            
            if (!('PushManager' in window)) {
                updateStatus('Ваш браузер не поддерживает Push API.', 'error');
                enablePushBtn.disabled = true;
                return;
            }
            
            updateStatus('Проверка текущей подписки...');
            
            try {
                // Регистрируем Service Worker заранее
                let registration;
                try {
                    registration = await navigator.serviceWorker.register('/task-manager/public/sw.js', { scope: '/task-manager/public/' });
                    updateStatus('Service Worker зарегистрирован успешно.');
                } catch (error) {
                    updateStatus(`Ошибка при регистрации Service Worker: ${error.message}`, 'error');
                    return;
                }
                
                try {
                    // Дожидаемся активации Service Worker
                    await navigator.serviceWorker.ready;
                    
                    // Проверяем наличие подписки
                    const subscription = await registration.pushManager.getSubscription();
                    
                    if (subscription) {
                        // Если подписка уже есть
                        updateStatus('Уведомления подключены', 'success');
                        enablePushBtn.classList.add('hidden');
                        disablePushBtn.classList.remove('hidden');
                    } else {
                        // Если нет активной подписки
                        updateStatus('Уведомления отключены', 'warning');
                        enablePushBtn.classList.remove('hidden');
                        disablePushBtn.classList.add('hidden');
                    }
                } catch (error) {
                    updateStatus(`Ошибка при проверке подписки: ${error.message}`, 'error');
                }
            } catch (error) {
                updateStatus(`Неизвестная ошибка: ${error.message}`, 'error');
            }
            
            // Обработчик нажатия на кнопку подключения уведомлений
            enablePushBtn.addEventListener('click', async function() {
                updateStatus('Запрашиваем разрешение на уведомления...');
                
                try {
                    // Запрашиваем разрешение
                    const permission = await Notification.requestPermission();
                    if (permission !== 'granted') {
                        updateStatus('Вы отклонили разрешение на уведомления', 'error');
                        return;
                    }
                    
                    updateStatus('Разрешение получено, настраиваем подписку...');
                    
                    // Получаем VAPID ключ
                    if (!window.Laravel || !window.Laravel.vapidPublicKey) {
                        // Если VAPID ключ отсутствует, пробуем получить его через API
                        try {
                            const vapidResponse = await fetch('/task-manager/public/vapidPublicKey');
                            if (vapidResponse.ok) {
                                const vapidData = await vapidResponse.json();
                                window.Laravel = window.Laravel || {};
                                window.Laravel.vapidPublicKey = vapidData.vapidPublicKey;
                                updateStatus('VAPID ключ получен через API');
                            } else {
                                updateStatus('Ошибка: Не удалось получить VAPID ключ', 'error');
                                return;
                            }
                        } catch (vapidError) {
                            updateStatus('Ошибка: VAPID публичный ключ не найден', 'error');
                        }
                    }
                    
                    const vapidKey = window.Laravel.vapidPublicKey;
                    updateStatus(`VAPID ключ получен: ${vapidKey.substring(0, 10)}...`);
                    
                    // Конвертируем ключ в формат для Web Push API
                    function urlBase64ToUint8Array(base64String) {
                        const padding = '='.repeat((4 - base64String.length % 4) % 4);
                        const base64 = (base64String + padding)
                            .replace(/\-/g, '+')
                            .replace(/_/g, '/');
                        
                        const rawData = window.atob(base64);
                        const outputArray = new Uint8Array(rawData.length);
                        
                        for (let i = 0; i < rawData.length; ++i) {
                            outputArray[i] = rawData.charCodeAt(i);
                        }
                        return outputArray;
                    }
                    
                    // Получаем регистрацию Service Worker
                    const registration = await navigator.serviceWorker.ready;
                    updateStatus('Service Worker готов, создаем подписку...');
                    
                    // Подписываемся на push-уведомления
                    try {
                        const applicationServerKey = urlBase64ToUint8Array(vapidKey);
                        
                        // Проверяем, что registration и pushManager существуют
                        if (!registration || !registration.pushManager) {
                            return;
                        }
                        
                        const subscription = await registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: applicationServerKey
                        });
                        
                        updateStatus('Подписка создана, отправляем на сервер...');
                        
                        // Проверяем, что у нас есть CSRF-токен
                        if (!window.Laravel || !window.Laravel.csrfToken) {
                            return;
                        }
                        
                        // Отправляем подписку на сервер
                        const response = await fetch('/task-manager/public/push-subscriptions', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': window.Laravel.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(subscription)
                        });
                        
                        if (response.ok) {
                            enablePushBtn.classList.add('hidden');
                            disablePushBtn.classList.remove('hidden');
                            updateStatus('Уведомления успешно подключены!', 'success');
                        } else {
                            const errorData = await response.json();
                            throw new Error(errorData.message || `Ошибка сервера: ${response.status}`);
                        }
                    } catch (subscribeError) {
                        updateStatus(`Ошибка при создании подписки: ${subscribeError.message}`, 'error');
                    }
                } catch (error) {
                    updateStatus(`Ошибка при подключении уведомлений: ${error.message}`, 'error');
                }
            });
            
            // Обработчик кнопки отключения уведомлений
            disablePushBtn.addEventListener('click', async function() {
                try {
                    updateStatus('Отключаем подписку...');
                    
                    const registration = await navigator.serviceWorker.ready;
                    const subscription = await registration.pushManager.getSubscription();
                    
                    if (subscription) {
                        updateStatus('Подписка найдена, отправляем запрос на удаление...');
                        
                        // Отправляем запрос на удаление подписки с сервера
                        try {
                            const response = await fetch('/task-manager/public/push-subscriptions/remove', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': window.Laravel.csrfToken,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    endpoint: subscription.endpoint
                                })
                            });
                            
                            if (!response.ok) {
                                try {
                                    const errorData = await response.json();
                                } catch (parseError) {
                                }
                                // Продолжаем, даже если есть ошибка на сервере
                            } else {
                            }
                        } catch (serverError) {
                        }
                        
                        // Отменяем подписку на стороне браузера
                        try {
                            await subscription.unsubscribe();
                            updateStatus('Уведомления отключены', 'warning');
                            disablePushBtn.classList.add('hidden');
                            enablePushBtn.classList.remove('hidden');
                        } catch (unsubError) {
                            updateStatus(`Ошибка при отключении подписки: ${unsubError.message}`, 'error');
                        }
                    } else {
                        updateStatus('Нет активной подписки для отключения', 'warning');
                        disablePushBtn.classList.add('hidden');
                        enablePushBtn.classList.remove('hidden');
                    }
                } catch (error) {
                    updateStatus(`Ошибка при отключении уведомлений: ${error.message}`, 'error');
                }
            });
        });
    </script>
@endsection

