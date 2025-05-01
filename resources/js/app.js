import './bootstrap';

import Alpine from 'alpinejs';
import { isPushNotificationSupported, enablePushNotifications, disablePushNotifications } from './webpush';

window.Alpine = Alpine;

// Делаем функции для работы с Web Push доступными глобально
window.WebPush = {
    isSupported: isPushNotificationSupported,
    enable: enablePushNotifications,
    disable: disablePushNotifications
};

// Если сервис-воркер поддерживается, регистрируем его при загрузке страницы
if ('serviceWorker' in navigator) {
    // Регистрируем Service Worker
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/task-manager/public/sw.js')
            .then(registration => {
                // Service Worker успешно зарегистрирован
            })
            .catch(error => {
                // Ошибка при регистрации Service Worker
            });
    });
}

Alpine.start();
