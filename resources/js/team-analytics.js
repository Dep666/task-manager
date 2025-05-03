import Swal from 'sweetalert2';
import { showSuccess, showError, showInfo, confirmAction } from './sweetalert';

// Инициализация страницы аналитики
document.addEventListener('DOMContentLoaded', function() {
    initDateFilters();
    initMemberDetailsLinks();
});

// Инициализация фильтров по дате
function initDateFilters() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    if (!startDateInput || !endDateInput) return;
    
    // Проверка, что начальная дата не больше конечной
    startDateInput.addEventListener('change', function() {
        if (startDateInput.value && endDateInput.value && startDateInput.value > endDateInput.value) {
            showError('Начальная дата не может быть позже конечной');
            startDateInput.value = endDateInput.value;
        }
    });
    
    // Проверка, что конечная дата не меньше начальной
    endDateInput.addEventListener('change', function() {
        if (startDateInput.value && endDateInput.value && endDateInput.value < startDateInput.value) {
            showError('Конечная дата не может быть раньше начальной');
            endDateInput.value = startDateInput.value;
        }
    });
}

// Инициализация ссылок для просмотра деталей участников
function initMemberDetailsLinks() {
    const memberDetailsLinks = document.querySelectorAll('.member-details-link');
    
    memberDetailsLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            const userStats = JSON.parse(this.dataset.userStats);
            
            showMemberDetailsModal(userId, userName, userStats);
        });
    });
}

// Отображение модального окна с деталями участника
function showMemberDetailsModal(userId, userName, userStats) {
    const isDarkMode = document.documentElement.classList.contains('dark');
    const backgroundColor = isDarkMode ? '#1f2937' : '#ffffff';
    const textColor = isDarkMode ? '#f3f4f6' : '#374151';
    
    const totalTasks = userStats.total_tasks || 0;
    const completedTasks = userStats.completed_tasks || 0;
    const overdueTasks = userStats.overdue_tasks || 0;
    const completionRate = userStats.completion_rate || 0;
    
    Swal.fire({
        title: `Статистика участника: ${userName}`,
        html: `
            <div class="text-left">
                <div class="mb-4">
                    <p class="mb-2 font-semibold">Всего задач: <span class="text-blue-600 dark:text-blue-400">${totalTasks}</span></p>
                    <p class="mb-2 font-semibold">Выполненных задач: <span class="text-green-600 dark:text-green-400">${completedTasks}</span></p>
                    <p class="mb-2 font-semibold">Просроченных задач: <span class="text-red-600 dark:text-red-400">${overdueTasks}</span></p>
                    <p class="mb-2 font-semibold">Выполнение: <span class="text-purple-600 dark:text-purple-400">${completionRate}%</span></p>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 mb-4">
                    <div class="bg-blue-600 h-4 rounded-full" style="width: ${completionRate}%"></div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">Статистика показывает эффективность работы участника в команде.</p>
            </div>
        `,
        width: '32rem',
        background: backgroundColor,
        color: textColor,
        showClass: {
            popup: 'animate__animated animate__fadeIn'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOut'
        }
    });
}

// Функция для отображения информации об эффективности команды
function showTeamEfficiencyInfo() {
    // Получаем данные статистики команды из глобальной переменной, которую мы установим в шаблоне
    const teamStats = window.teamStats || {};
    
    const isDarkMode = document.documentElement.classList.contains('dark');
    const backgroundColor = isDarkMode ? '#1f2937' : '#ffffff';
    const textColor = isDarkMode ? '#f3f4f6' : '#374151';
    
    Swal.fire({
        title: 'Эффективность команды',
        html: `
            <div class="text-left">
                <p class="mb-4">Эффективность команды рассчитывается на основе соотношения выполненных задач к общему количеству задач за выбранный период.</p>
                
                <div class="mb-4">
                    <p class="mb-2 font-semibold">Всего задач: <span class="text-blue-600 dark:text-blue-400">${teamStats.total_tasks || 0}</span></p>
                    <p class="mb-2 font-semibold">Выполненных задач: <span class="text-green-600 dark:text-green-400">${teamStats.completed_tasks || 0}</span></p>
                    <p class="mb-2 font-semibold">В процессе: <span class="text-yellow-600 dark:text-yellow-400">${teamStats.in_progress_tasks || 0}</span></p>
                    <p class="mb-2 font-semibold">Просроченных задач: <span class="text-red-600 dark:text-red-400">${teamStats.overdue_tasks || 0}</span></p>
                </div>
                
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 mb-4">
                    <div class="bg-blue-600 h-4 rounded-full" style="width: ${teamStats.completion_rate || 0}%"></div>
                </div>
                
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">Показатель эффективности: <strong>${teamStats.completion_rate || 0}%</strong></p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    ${getEfficiencyMessage(teamStats.completion_rate || 0)}
                </p>
            </div>
        `,
        background: backgroundColor,
        color: textColor,
        confirmButtonColor: '#3b82f6',
        confirmButtonText: 'Понятно'
    });
}

// Функция для генерации сообщения об эффективности команды
function getEfficiencyMessage(rate) {
    if (rate >= 90) {
        return 'Отличный результат! Команда работает очень эффективно.';
    } else if (rate >= 70) {
        return 'Хороший результат. Команда справляется с большинством задач.';
    } else if (rate >= 50) {
        return 'Средний результат. Есть потенциал для улучшения.';
    } else if (rate >= 30) {
        return 'Ниже среднего. Рекомендуется обратить внимание на организацию работы.';
    } else {
        return 'Низкий результат. Необходимо серьезно пересмотреть рабочие процессы.';
    }
}

// Экспорт функций для использования в других модулях
export {
    initDateFilters,
    initMemberDetailsLinks,
    showMemberDetailsModal,
    showTeamEfficiencyInfo,
    getEfficiencyMessage
}; 