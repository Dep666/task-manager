import './bootstrap';

import Alpine from 'alpinejs';
import { isPushNotificationSupported, enablePushNotifications, disablePushNotifications } from './webpush';
import * as TeamAnalytics from './team-analytics';
import { showSuccess, showError, showInfo, showWarning, confirmAction } from './notifications';

window.Alpine = Alpine;

// Делаем функции для работы с Web Push доступными глобально
window.WebPush = {
    isSupported: isPushNotificationSupported,
    enable: enablePushNotifications,
    disable: disablePushNotifications
};

// Делаем функции уведомлений доступными глобально
window.showSuccess = showSuccess;
window.showError = showError;
window.showInfo = showInfo;
window.showWarning = showWarning;
window.confirmAction = confirmAction;

// Делаем функции для работы с аналитикой команд доступными глобально
window.TeamAnalytics = TeamAnalytics;

// Если сервис-воркер поддерживается, регистрируем его при загрузке страницы
if ('serviceWorker' in navigator) {
    // Регистрируем Service Worker
    window.addEventListener('load', () => {
        // Учитываем, что приложение находится в подкаталоге
        const swPath = '/task-manager/public/sw.js';
        
        navigator.serviceWorker.register(swPath, {
            scope: '/task-manager/public/'
        })
            .then(registration => {
                // Service Worker успешно зарегистрирован
                console.log('ServiceWorker registration successful');
            })
            .catch(error => {
                // Ошибка при регистрации Service Worker
                console.error('ServiceWorker registration failed:', error);
            });
    });
}

Alpine.start();
