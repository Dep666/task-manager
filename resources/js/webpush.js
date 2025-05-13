/**
 * Скрипт для работы с Web Push уведомлениями
 */

// Проверяем поддержку Service Worker и Push уведомлений браузером
const isPushNotificationSupported = () => {
    return 'serviceWorker' in navigator && 'PushManager' in window;
};

// Запрашиваем разрешение на отправку уведомлений
async function askNotificationPermission() {
    if (!isPushNotificationSupported()) {
        return false;
    }

    // Проверяем, есть ли уже разрешение
    const permissionResult = await Notification.requestPermission();
    return permissionResult === 'granted';
}

// Регистрация Service Worker
async function registerServiceWorker() {
    try {
        const registration = await navigator.serviceWorker.register('/sw.js');
        return registration;
    } catch (error) {
        console.error('Ошибка регистрации Service Worker:', error);
        return null;
    }
}

// Получение подписки на push-уведомления
async function subscribeToPush(registration) {
    try {
        const subscribeOptions = {
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(window.Laravel.vapidPublicKey)
        };

        const subscription = await registration.pushManager.subscribe(subscribeOptions);
        return subscription;
    } catch (error) {
        console.error('Ошибка подписки на Push уведомления:', error);
        return null;
    }
}

// Отправка подписки на сервер
async function saveSubscription(subscription) {
    try {
        const response = await fetch('/push-subscriptions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.Laravel.csrfToken,
            },
            body: JSON.stringify(subscription)
        });

        if (!response.ok) {
            throw new Error('Сервер вернул ошибку при сохранении подписки');
        }

        return true;
    } catch (error) {
        console.error('Ошибка сохранения подписки:', error);
        return false;
    }
}

// Функция для конвертации строки в Uint8Array для ключа
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

// Основная функция активации подписки
async function enablePushNotifications() {
    const hasPermission = await askNotificationPermission();
    if (!hasPermission) {
        alert('Для получения уведомлений необходимо дать разрешение');
        return false;
    }

    const registration = await registerServiceWorker();
    if (!registration) return false;

    const subscription = await subscribeToPush(registration);
    if (!subscription) return false;

    const saved = await saveSubscription(subscription);
    return saved;
}

// Функция отписки от уведомлений
async function disablePushNotifications() {
    try {
        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();
        
        if (subscription) {
            // Отправка запроса на удаление подписки на сервере
            await fetch('/push-subscriptions/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.Laravel.csrfToken,
                },
                body: JSON.stringify({
                    endpoint: subscription.endpoint
                })
            });
            
            // Отмена подписки на стороне браузера
            await subscription.unsubscribe();
            return true;
        }
        return false;
    } catch (error) {
        console.error('Ошибка отписки от уведомлений:', error);
        return false;
    }
}

// Экспортируем функции для использования из других модулей
export { 
    isPushNotificationSupported, 
    enablePushNotifications, 
    disablePushNotifications 
}; 