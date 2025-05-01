/**
 * Service Worker для обработки Web Push уведомлений
 */

// Обработчик push событий от сервера
self.addEventListener('push', function(event) {
    // Проверяем есть ли данные в событии
    let notificationData = {};
    
    if (event.data) {
        // Пытаемся получить данные из события
        try {
            notificationData = event.data.json();
        } catch (e) {
            // Если данные не в JSON, используем текст как есть
            notificationData = {
                title: 'Новое уведомление',
                body: event.data.text(),
                data: { url: self.location.origin }
            };
        }
    } else {
        // Если данные отсутствуют, показываем стандартное уведомление
        notificationData = {
            title: 'Новое уведомление',
            body: 'Получено новое уведомление без данных',
            data: { url: self.location.origin }
        };
    }
    
    // Показываем уведомление
    const title = notificationData.title || 'Новое уведомление';
    const options = {
        body: notificationData.body || 'Вы получили новое уведомление',
        icon: notificationData.icon || '/task-manager/public/images/notification-icon.png',
        badge: notificationData.badge || '/task-manager/public/images/badge-icon.png',
        data: notificationData.data || {},
        actions: notificationData.actions || []
    };
    
    // Отправляем уведомление и ждем события показа
    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Обработчик клика по уведомлению
self.addEventListener('notificationclick', function(event) {
    // Закрываем уведомление
    event.notification.close();
    
    // Получаем данные из уведомления
    const notificationData = event.notification.data || {};
    let url = notificationData.url || self.location.origin;
    
    // Проверяем, нажата ли кнопка действия
    if (event.action) {
        // Обрабатываем действие
        switch (event.action) {
            case 'open_task':
                url = notificationData.taskUrl || url;
                break;
            case 'mark_as_read':
                // Логика для отметки как прочитано будет на сервере
                if (notificationData.readUrl) {
                    fetch(notificationData.readUrl, { method: 'POST', credentials: 'include' });
                    return;
                }
                break;
        }
    }
    
    // Открываем указанный URL при клике на уведомление
    event.waitUntil(
        // Проверяем открытые вкладки и фокусируемся на нужной, если она уже открыта
        self.clients.matchAll({type: 'window'})
            .then(function(clientList) {
                // Ищем уже открытую вкладку с нужным URL
                for (let i = 0; i < clientList.length; i++) {
                    const client = clientList[i];
                    if (client.url.indexOf(url) !== -1 && 'focus' in client) {
                        return client.focus();
                    }
                }
                // Если нет открытой вкладки - открываем новую
                if (self.clients.openWindow) {
                    return self.clients.openWindow(url);
                }
            })
    );
});

// Обработчик закрытия уведомления пользователем
self.addEventListener('notificationclose', function(event) {
    // Можно добавить аналитику закрытий или другую логику
}); 