// Настройка темной темы для SweetAlert2
const darkTheme = {
    background: '#1f2937',
    text: '#f3f4f6',
    confirmButtonColor: '#3b82f6',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Подтвердить',
    cancelButtonText: 'Отмена'
};

// Настраиваем Toast уведомления
const TaskToast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

// Функции для уведомлений
export function showTaskSuccess(message) {
    TaskToast.fire({
        icon: 'success',
        title: message,
        background: darkTheme.background,
        color: darkTheme.text
    });
}

export function showTaskError(message) {
    TaskToast.fire({
        icon: 'error',
        title: message,
        background: darkTheme.background,
        color: darkTheme.text
    });
}

export function showTaskInfo(message) {
    TaskToast.fire({
        icon: 'info',
        title: message,
        background: darkTheme.background,
        color: darkTheme.text
    });
}

export function showTaskWarning(message) {
    TaskToast.fire({
        icon: 'warning',
        title: message,
        background: darkTheme.background,
        color: darkTheme.text
    });
}

// Функция для подтверждения удаления задачи
export function confirmTaskDelete(taskTitle, deleteUrl) {
    Swal.fire({
        title: 'Удалить задачу?',
        text: `Вы действительно хотите удалить задачу "${taskTitle}"? Это действие нельзя будет отменить.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: darkTheme.confirmButtonColor,
        cancelButtonColor: darkTheme.cancelButtonColor,
        confirmButtonText: 'Да, удалить',
        cancelButtonText: darkTheme.cancelButtonText,
        background: darkTheme.background,
        color: darkTheme.text
    }).then((result) => {
        if (result.isConfirmed) {
            // Отправляем форму для удаления задачи
            document.getElementById('delete-task-form').action = deleteUrl;
            document.getElementById('delete-task-form').submit();
        }
    });
    
    return false; // Предотвращаем стандартное действие ссылки
}

// Функция для подтверждения изменения статуса
export function confirmStatusChange(statusName) {
    return Swal.fire({
        title: 'Изменить статус задачи?',
        text: `Вы действительно хотите изменить статус задачи на "${statusName}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: darkTheme.confirmButtonColor,
        cancelButtonColor: darkTheme.cancelButtonColor,
        confirmButtonText: darkTheme.confirmButtonText,
        cancelButtonText: darkTheme.cancelButtonText,
        background: darkTheme.background,
        color: darkTheme.text
    }).then((result) => {
        return result.isConfirmed;
    });
}

// Функция для показа уведомления о сохранении задачи
export function taskSaved() {
    showTaskSuccess('Задача успешно сохранена!');
}

// Функция для показа уведомления о создании задачи
export function taskCreated() {
    Swal.fire({
        title: 'Задача создана!',
        text: 'Новая задача успешно создана.',
        icon: 'success',
        background: darkTheme.background,
        color: darkTheme.text,
        confirmButtonColor: darkTheme.confirmButtonColor
    });
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