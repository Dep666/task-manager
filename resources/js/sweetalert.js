import Swal from 'sweetalert2';

// Определение текущей темы
const isDarkMode = () => document.documentElement.classList.contains('dark');

// Настройка тем для SweetAlert2
const lightTheme = {
    background: '#ffffff',
    text: '#374151',
    confirmButtonColor: '#3b82f6',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Подтвердить',
    cancelButtonText: 'Отмена'
};

const darkTheme = {
    background: '#1e293b', // Темно-синий, как на скриншоте
    text: '#f3f4f6',
    confirmButtonColor: '#3b82f6',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Подтвердить',
    cancelButtonText: 'Отмена'
};

// Получение текущей темы
const getCurrentTheme = () => isDarkMode() ? darkTheme : lightTheme;

// Глобальные настройки SweetAlert2 для темной и светлой темы
const setupSweetAlert = () => {
    const isDark = isDarkMode();
    
    // Устанавливаем базовую конфигурацию для sweetalert2
    Swal.mixin({
        background: isDark ? '#1e293b' : '#ffffff',
        color: isDark ? '#f3f4f6' : '#374151',
        confirmButtonColor: '#3b82f6'
    });
};

// Вызываем функцию настройки при загрузке
if (typeof window !== 'undefined') {
    // Настройка при загрузке страницы
    document.addEventListener('DOMContentLoaded', setupSweetAlert);
    
    // Настройка при изменении темы
    const darkModeObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                setupSweetAlert();
            }
        });
    });
    
    // Начинаем отслеживать класс на html элементе
    document.addEventListener('DOMContentLoaded', () => {
        darkModeObserver.observe(document.documentElement, { attributes: true });
    });
}

// Экспортируем настроенный объект Swal для использования в проекте
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    background: () => getCurrentTheme().background,
    color: () => getCurrentTheme().text,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

// Обработчик изменения темы
const handleThemeChange = () => {
    const theme = getCurrentTheme();
    document.querySelectorAll('.swal2-container').forEach(container => {
        const popup = container.querySelector('.swal2-popup');
        if (popup) {
            if (isDarkMode()) {
                popup.classList.add('dark-theme');
                popup.classList.remove('light-theme');
            } else {
                popup.classList.add('light-theme');
                popup.classList.remove('dark-theme');
            }
        }
    });
};

// Отслеживание изменения темы
if (typeof window !== 'undefined') {
    const darkModeObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                handleThemeChange();
            }
        });
    });
    
    // Начинаем отслеживать класс на html элементе
    document.addEventListener('DOMContentLoaded', () => {
        darkModeObserver.observe(document.documentElement, { attributes: true });
    });
}

// Базовые функции для использования в проекте
export const showSuccess = (message) => {
    const theme = getCurrentTheme();
    Toast.fire({
        icon: 'success',
        title: message
    });
};

export const showError = (message) => {
    const theme = getCurrentTheme();
    Toast.fire({
        icon: 'error',
        title: message
    });
};

export const showInfo = (message) => {
    const theme = getCurrentTheme();
    Toast.fire({
        icon: 'info',
        title: message
    });
};

export const showWarning = (message) => {
    const theme = getCurrentTheme();
    Toast.fire({
        icon: 'warning',
        title: message
    });
};

// Диалог подтверждения действия
export const confirmAction = async (title, text, confirmButtonText = 'Да, подтвердить') => {
    const theme = getCurrentTheme();
    const result = await Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: theme.confirmButtonColor,
        cancelButtonColor: theme.cancelButtonColor,
        confirmButtonText: confirmButtonText,
        cancelButtonText: theme.cancelButtonText
    });
    
    return result.isConfirmed;
};

// Диалог с формой ввода
export const promptInput = async (title, inputPlaceholder) => {
    const theme = getCurrentTheme();
    const result = await Swal.fire({
        title: title,
        input: 'text',
        inputPlaceholder: inputPlaceholder,
        showCancelButton: true,
        confirmButtonColor: theme.confirmButtonColor,
        cancelButtonColor: theme.cancelButtonColor,
        confirmButtonText: theme.confirmButtonText,
        cancelButtonText: theme.cancelButtonText,
        inputValidator: (value) => {
            if (!value) {
                return 'Поле не может быть пустым';
            }
        }
    });
    
    return result.value;
};

// Экспортируем все функции и настроенный Swal для использования в проекте
export default Swal; 