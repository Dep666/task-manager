import { showSuccess, showError, showInfo, showWarning, confirmAction } from './notifications';

// Функции для уведомлений
export function showTaskSuccess(message) {
    showSuccess(message);
}

export function showTaskError(message) {
    showError(message);
}

export function showTaskInfo(message) {
    showInfo(message);
}

export function showTaskWarning(message) {
    showWarning(message);
}

// Функция для подтверждения удаления задачи
export function confirmTaskDelete(taskTitle, deleteUrl) {
    confirmAction({
        title: 'Удалить задачу?',
        text: `Вы действительно хотите удалить задачу "${taskTitle}"? Это действие нельзя будет отменить.`,
        confirmButtonText: 'Да, удалить',
        cancelButtonText: 'Отмена'
    }).then((confirmed) => {
        if (confirmed) {
            // Отправляем форму для удаления задачи
            document.getElementById('delete-task-form').action = deleteUrl;
            document.getElementById('delete-task-form').submit();
        }
    });
    
    return false; // Предотвращаем стандартное действие ссылки
}

// Функция для подтверждения изменения статуса
export function confirmStatusChange(statusName) {
    return confirmAction({
        title: 'Изменить статус задачи?',
        text: `Вы действительно хотите изменить статус задачи на "${statusName}"?`,
        confirmButtonText: 'Подтвердить',
        cancelButtonText: 'Отмена'
    });
}

// Функция для показа уведомления о сохранении задачи
export function taskSaved() {
    showTaskSuccess('Задача успешно сохранена!');
}

// Функция для показа уведомления о создании задачи
export function taskCreated() {
    showSuccess('Задача успешно создана!');
}

// Функция для показа уведомления об обновлении задачи
export function taskUpdated() {
    showTaskSuccess('Задача успешно обновлена!');
}

// Функция для показа уведомления об изменении статуса
export function statusChanged(statusName) {
    showTaskSuccess(`Статус задачи изменен на "${statusName}"!`);
}

// Глобальные функции для вызова из HTML
window.taskActions = {
    confirmDelete: confirmTaskDelete,
    confirmStatusChange: confirmStatusChange,
    showSuccess: showTaskSuccess,
    showError: showTaskError,
    showInfo: showTaskInfo,
    showWarning: showTaskWarning
}; 