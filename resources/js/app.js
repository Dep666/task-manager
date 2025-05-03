import './bootstrap';

import Alpine from 'alpinejs';
import { isPushNotificationSupported, enablePushNotifications, disablePushNotifications } from './webpush';
import * as TeamAnalytics from './team-analytics';
import Swal from 'sweetalert2';
import { showSuccess, showError, showInfo, confirmAction } from './sweetalert';

window.Alpine = Alpine;

// Делаем функции для работы с Web Push доступными глобально
window.WebPush = {
    isSupported: isPushNotificationSupported,
    enable: enablePushNotifications,
    disable: disablePushNotifications
};

// Делаем SweetAlert доступным глобально
window.Swal = Swal;
window.showSuccess = showSuccess;
window.showError = showError;
window.showInfo = showInfo;
window.confirmAction = confirmAction;

// Делаем функции для работы с аналитикой команд доступными глобально
window.TeamAnalytics = TeamAnalytics;

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
