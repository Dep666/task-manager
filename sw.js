/**
 * Service Worker для обработки Web Push уведомлений
 */

self.addEventListener('push', function(event) {
    if (event.data) {
        try {
            // Получаем данные из уведомления
            const notificationData = event.data.json();
            
            // Создаем уведомление
            const options = {
                body: notificationData.body || 'Новое уведомление',
                icon: notificationData.icon || '/task-manager/public/images/notification-icon.png',
                badge: notificationData.badge || '/task-manager/public/images/badge.png',
                data: notificationData.data || {},
                tag: notificationData.tag || 'deadline-notification',
                // Вибрация: [200, 100, 200] означает вибрация 200мс, пауза 100мс, вибрация 200мс
                vibrate: [200, 100, 200, 100, 200],
                // Показываем уведомление даже если приложение открыто
                requireInteraction: true,
                // Если есть действия в уведомлении
                actions: notificationData.actions || []
            };
            
            event.waitUntil(
                self.registration.showNotification(notificationData.title || 'Уведомление', options)
            );
        } catch (error) {
            console.error('Ошибка при обработке push-уведомления:', error);
            
            // Резервный вариант, если JSON не распарсился
            event.waitUntil(
                self.registration.showNotification('Новое уведомление', {
                    body: event.data.text()
                })
            );
        }
    }
});

// Обработчик клика по уведомлению
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    
    // Если в уведомлении было действие, и пользователь нажал на него
    if (event.action) {
        // Обработка нажатия на кнопку действия
        console.log('Выбрано действие:', event.action);
    } else {
        // Пользователь нажал на само уведомление
        // Получаем данные из уведомления
        const notificationData = event.notification.data;
        
        // Определяем URL, на который нужно перейти
        let url = '/task-manager/public/tasks';
        
        // Если в данных уведомления есть task_id, добавляем его к URL
        if (notificationData && notificationData.task_id) {
            url = `/task-manager/public/tasks/${notificationData.task_id}`;
        }
        
        // Пытаемся открыть окно с нужным URL
        event.waitUntil(
            clients.matchAll({type: 'window'}).then(windowClients => {
                // Проверяем, открыта ли вкладка с нашим приложением
                for (let client of windowClients) {
                    if (client.url.includes(self.location.origin) && 'focus' in client) {
                        // Если открыта, просто переходим на нужный URL
                        client.navigate(url);
                        return client.focus();
                    }
                }
                
                // Если вкладки нет, открываем новую
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
        );
    }
});

// Обработчик закрытия уведомления
self.addEventListener('notificationclose', function(event) {
    console.log('Уведомление закрыто пользователем');
}); 