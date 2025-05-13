import { showSuccess, showError, showInfo, showWarning, confirmAction } from './notifications';

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
    const totalTasks = userStats.total_tasks || 0;
    const completedTasks = userStats.completed_tasks || 0;
    const overdueTasks = userStats.overdue_tasks || 0;
    const completionRate = userStats.completion_rate || 0;
    
    // Создаем HTML для модального окна
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70';
    modal.innerHTML = `
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md mx-auto w-full sm:w-auto">
            <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-white">Статистика участника: ${userName}</h3>
            <div class="mb-4 px-2">
                <p class="mb-2 font-semibold">Всего задач: <span class="text-blue-600 dark:text-blue-400">${totalTasks}</span></p>
                <p class="mb-2 font-semibold">Выполненных задач: <span class="text-green-600 dark:text-green-400">${completedTasks}</span></p>
                <p class="mb-2 font-semibold">Просроченных задач: <span class="text-red-600 dark:text-red-400">${overdueTasks}</span></p>
                <p class="mb-2 font-semibold">Выполнение: <span class="text-purple-600 dark:text-purple-400">${completionRate}%</span></p>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 mb-4 mx-2">
                <div class="bg-blue-600 h-4 rounded-full" style="width: ${completionRate}%"></div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-4 px-2">Статистика показывает эффективность работы участника в команде.</p>
            <div class="flex justify-end mt-4">
                <button id="close-btn" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                    Закрыть
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Обработчик закрытия
    const closeBtn = modal.querySelector('#close-btn');
    closeBtn.addEventListener('click', () => {
        document.body.removeChild(modal);
    });
    
    // Закрытие по клику на фон
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            document.body.removeChild(modal);
        }
    });
}

// Функция для отображения информации об эффективности команды
function showTeamEfficiencyInfo() {
    // Получаем данные статистики команды из глобальной переменной, которую мы установим в шаблоне
    const teamStats = window.teamStats || {};
    
    // Создаем HTML для модального окна
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70';
    modal.innerHTML = `
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md mx-auto w-full sm:w-auto">
            <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-white">Эффективность команды</h3>
            <div class="text-left px-2">
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
            <div class="flex justify-end mt-4">
                <button id="close-efficiency-btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Понятно
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Обработчик закрытия
    const closeBtn = modal.querySelector('#close-efficiency-btn');
    closeBtn.addEventListener('click', () => {
        document.body.removeChild(modal);
    });
    
    // Закрытие по клику на фон
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            document.body.removeChild(modal);
        }
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

// Функция экспорта аналитики в PDF
export async function exportToPDF(teamId) {
    try {
        const response = await fetch(`/teams/${teamId}/analytics/export-pdf`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        });
        
        if (!response.ok) {
            throw new Error('Не удалось создать PDF');
        }
        
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = `team-analytics-${teamId}.pdf`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        showSuccess('PDF успешно создан и скачан');
    } catch (error) {
        console.error('Ошибка при экспорте в PDF:', error);
        showError('Произошла ошибка при создании PDF');
    }
}

// Функция экспорта аналитики в Excel
export async function exportToExcel(teamId) {
    try {
        const response = await fetch(`/teams/${teamId}/analytics/export-excel`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        });
        
        if (!response.ok) {
            throw new Error('Не удалось создать Excel-файл');
        }
        
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = `team-analytics-${teamId}.xlsx`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        showSuccess('Excel-файл успешно создан и скачан');
    } catch (error) {
        console.error('Ошибка при экспорте в Excel:', error);
        showError('Произошла ошибка при создании Excel-файла');
    }
}

// Функция для формирования отчета за период
export async function generateReport(teamId, startDate, endDate) {
    try {
        confirmAction({
            title: 'Сформировать отчет?',
            text: `Будет создан отчет за период с ${startDate} по ${endDate}`,
            confirmButtonText: 'Сформировать',
            cancelButtonText: 'Отмена'
        }).then(async (result) => {
            if (result) {
                const formData = new FormData();
                formData.append('start_date', startDate);
                formData.append('end_date', endDate);
                
                const response = await fetch(`/teams/${teamId}/analytics/generate-report`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error('Не удалось сформировать отчет');
                }
                
                const data = await response.json();
                showSuccess('Отчет успешно сформирован');
                
                // Обновляем страницу для отображения нового отчета
                window.location.reload();
            }
        });
    } catch (error) {
        console.error('Ошибка при формировании отчета:', error);
        showError('Произошла ошибка при формировании отчета');
    }
}

// Функция для отправки отчета на email
export async function sendReportByEmail(teamId, email, reportType) {
    try {
        confirmAction({
            title: 'Отправить отчет на email?',
            text: `Отчет будет отправлен на адрес ${email}`,
            confirmButtonText: 'Отправить',
            cancelButtonText: 'Отмена'
        }).then(async (result) => {
            if (result) {
                const formData = new FormData();
                formData.append('email', email);
                formData.append('report_type', reportType);
                
                const response = await fetch(`/teams/${teamId}/analytics/send-report`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error('Не удалось отправить отчет');
                }
                
                const data = await response.json();
                showSuccess('Отчет успешно отправлен');
            }
        });
    } catch (error) {
        console.error('Ошибка при отправке отчета:', error);
        showError('Произошла ошибка при отправке отчета');
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