/**
 * Система уведомлений на базе Notyf
 */
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

// Настройка Notyf с кастомными типами и опциями
const notyf = new Notyf({
    duration: 5000,
    position: {
        x: 'right',
        y: 'top',
    },
    types: [
        {
            type: 'success',
            background: '#10B981',
            icon: {
                className: 'notyf__icon--success',
                tagName: 'i',
            }
        },
        {
            type: 'error',
            background: '#EF4444',
            duration: 6000,
            dismissible: true
        },
        {
            type: 'warning',
            background: '#F59E0B',
            icon: false
        },
        {
            type: 'info',
            background: '#3B82F6',
            icon: false
        }
    ]
});

/**
 * Показать успешное уведомление
 * @param {string} message Текст уведомления
 */
export function showSuccess(message) {
    notyf.success(message);
}

/**
 * Показать уведомление об ошибке
 * @param {string} message Текст уведомления
 */
export function showError(message) {
    notyf.error(message);
}

/**
 * Показать информационное уведомление
 * @param {string} message Текст уведомления
 */
export function showInfo(message) {
    notyf.open({
        type: 'info',
        message: message
    });
}

/**
 * Показать предупреждение
 * @param {string} message Текст уведомления
 */
export function showWarning(message) {
    notyf.open({
        type: 'warning',
        message: message
    });
}

/**
 * Показать диалог подтверждения
 * @param {Object} options Опции диалога
 * @param {string} options.title Заголовок
 * @param {string} options.text Текст сообщения
 * @param {string} options.confirmButtonText Текст кнопки подтверждения
 * @param {string} options.cancelButtonText Текст кнопки отмены
 * @returns {Promise} Promise, который резолвится в true если пользователь подтвердил, иначе false
 */
export function confirmAction(options = {}) {
    const title = options.title || 'Вы уверены?';
    const text = options.text || 'Это действие нельзя будет отменить.';
    const confirmText = options.confirmButtonText || 'Да';
    const cancelText = options.cancelButtonText || 'Отмена';
    
    return new Promise((resolve) => {
        // Создаем HTML для модального окна
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70';
        modal.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-sm mx-auto w-full sm:w-auto">
                <h3 class="text-lg font-medium mb-3 text-gray-900 dark:text-white">${title}</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-5 px-1">${text}</p>
                <div class="flex justify-end space-x-3">
                    <button id="cancel-btn" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                        ${cancelText}
                    </button>
                    <button id="confirm-btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        ${confirmText}
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Обработчики кнопок
        const confirmBtn = modal.querySelector('#confirm-btn');
        const cancelBtn = modal.querySelector('#cancel-btn');
        
        confirmBtn.addEventListener('click', () => {
            document.body.removeChild(modal);
            resolve(true);
        });
        
        cancelBtn.addEventListener('click', () => {
            document.body.removeChild(modal);
            resolve(false);
        });
        
        // Закрытие по клику на фон
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                document.body.removeChild(modal);
                resolve(false);
            }
        });
        
        // Фокус на кнопке подтверждения
        confirmBtn.focus();
    });
} 