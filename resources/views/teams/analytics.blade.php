@extends('layouts.app')

@section('head')
<style>
    /* Стили для графиков и отчетов */
    .chart-container {
        position: relative;
        margin: auto;
        height: 80vh;
        width: 100%;
    }
    .chart-container canvas {
        max-height: 400px;
    }
    .stats-card {
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: transform 0.3s;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .card-value {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .report-card {
        border-radius: 0.5rem;
        transition: transform 0.3s;
        overflow: hidden;
    }
    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    /* Стили для цветных меток в модальных окнах */
    .stat-value-blue {
        color: #3b82f6;
    }
    .stat-value-green {
        color: #10b981;
    }
    .stat-value-red {
        color: #ef4444;
    }
    .stat-value-purple {
        color: #8b5cf6;
    }
    .stat-value-yellow {
        color: #f59e0b;
    }
    
    /* Стили для темной темы */
    .dark .stat-value-blue {
        color: #60a5fa;
    }
    .dark .stat-value-green {
        color: #34d399;
    }
    .dark .stat-value-red {
        color: #f87171;
    }
    .dark .stat-value-purple {
        color: #a78bfa;
    }
    .dark .stat-value-yellow {
        color: #fbbf24;
    }
</style>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl sm:text-3xl font-semibold text-gray-800 dark:text-white">Аналитика команды: {{ $team->name }}</h1>
        <div class="flex gap-2">
            <div class="relative">
                <button id="exportDropdownButton" class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-700 text-white rounded hover:bg-indigo-700 dark:hover:bg-indigo-600 transition duration-300 text-sm font-medium border border-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Экспорт
                </button>
                <div id="exportDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10 border border-gray-200 dark:border-gray-700">
                    <div class="py-1">
                        <a href="{{ route('teams.analytics.export', ['id' => $team->id, 'format' => 'pdf', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Экспорт в PDF
                            </div>
                        </a>
                        <a href="{{ route('teams.analytics.export', ['id' => $team->id, 'format' => 'csv', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Экспорт в CSV
                            </div>
                        </a>
                        <a href="{{ route('teams.analytics.export', ['id' => $team->id, 'format' => 'excel', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Экспорт в Excel
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <a href="{{ route('teams.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-sm font-medium border border-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Назад к командам
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 p-4 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Фильтры дат -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-medium text-gray-800 dark:text-white mb-4">Фильтры по дате</h2>
        <form action="{{ route('teams.analytics', $team->id) }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4">
            <div class="w-full sm:w-auto">
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Начальная дата</label>
                <input type="date" id="start_date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" 
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div class="w-full sm:w-auto">
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Конечная дата</label>
                <input type="date" id="end_date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" 
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div class="w-full sm:w-auto mt-4 sm:mt-0">
                <button type="submit" class="w-full px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-sm font-medium border border-gray-600">
                    Применить фильтр
                </button>
            </div>
        </form>
    </div>

    <!-- Общая статистика команды -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-medium text-gray-800 dark:text-white mb-4">Общая статистика команды</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Диаграмма для визуализации статистики -->
            <div class="w-full h-64">
                <canvas id="teamStatsChart"></canvas>
            </div>
            
            <!-- Легенда и числа для ясности -->
            <div class="flex flex-col justify-center space-y-4">
                <div>
                    <div class="text-gray-700 dark:text-gray-300 font-medium mb-1">Всего задач: <span class="font-bold text-blue-600 dark:text-blue-400 text-xl">{{ $teamStats['total_tasks'] }}</span></div>
                </div>
                
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full mr-2" style="background-color: #10b981;"></div>
                    <div class="text-gray-700 dark:text-gray-300 font-medium">Выполненных задач: <span class="font-bold text-green-600 dark:text-green-400">{{ $teamStats['completed_tasks'] }}</span></div>
                </div>
                
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full mr-2" style="background-color: #3b82f6;"></div>
                    <div class="text-gray-700 dark:text-gray-300 font-medium">В работе: <span class="font-bold text-blue-600 dark:text-blue-400">{{ $teamStats['in_progress_tasks'] ?? 0 }}</span></div>
                </div>
                
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full mr-2" style="background-color: #a855f7;"></div>
                    <div class="text-gray-700 dark:text-gray-300 font-medium">Новые задачи: <span class="font-bold text-purple-600 dark:text-purple-400">{{ $teamStats['new_tasks'] ?? 0 }}</span></div>
                </div>
                
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full mr-2" style="background-color: #f97316;"></div>
                    <div class="text-gray-700 dark:text-gray-300 font-medium">На доработке: <span class="font-bold text-orange-600 dark:text-orange-400">{{ $teamStats['revision_tasks'] ?? 0 }}</span></div>
                </div>
                
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full mr-2" style="background-color: #0ea5e9;"></div>
                    <div class="text-gray-700 dark:text-gray-300 font-medium">На проверке: <span class="font-bold text-sky-600 dark:text-sky-400">{{ $teamStats['review_tasks'] ?? 0 }}</span></div>
                </div>
                
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full mr-2" style="background-color: #ef4444;"></div>
                    <div class="text-gray-700 dark:text-gray-300 font-medium">Просроченных задач: <span class="font-bold text-red-600 dark:text-red-400">{{ $teamStats['overdue_tasks'] }}</span></div>
                </div>
                
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-gray-700 dark:text-gray-300 font-medium">Процент выполнения: <span class="font-bold text-purple-600 dark:text-purple-400 text-xl">{{ $teamStats['completion_rate'] }}%</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Эффективность команды -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-xl font-medium text-gray-800 dark:text-white">Эффективность команды</h2>
            <button type="button" id="show-team-details" 
                    class="inline-flex items-center px-3 py-1.5 bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-sm font-medium border border-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Подробнее
            </button>
        </div>
        
        @if($teamStats['total_tasks'] > 0)
            <div>
                <!-- Индикатор эффективности -->
                <div class="flex items-center mb-2">
                    <div class="w-3 h-3 rounded-full mr-2" 
                        style="background-color: {{ $teamStats['completion_rate'] >= 70 ? '#10b981' : ($teamStats['completion_rate'] >= 40 ? '#f59e0b' : '#ef4444') }};"></div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Выполнение задач
                    </span>
                    <span class="ml-auto text-base font-semibold" 
                        style="color: {{ $teamStats['completion_rate'] >= 70 ? '#10b981' : ($teamStats['completion_rate'] >= 40 ? '#f59e0b' : '#ef4444') }};">
                        {{ $teamStats['completion_rate'] }}%
                    </span>
                </div>
                
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mb-3">
                    <div class="h-2.5 rounded-full transition-all duration-500 ease-out"
                        style="width: {{ $teamStats['completion_rate'] }}%; background-color: {{ $teamStats['completion_rate'] >= 70 ? '#10b981' : ($teamStats['completion_rate'] >= 40 ? '#f59e0b' : '#ef4444') }};">
                    </div>
                </div>
                
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    @if($teamStats['completion_rate'] >= 70)
                        Отличный результат! Команда работает эффективно.
                    @elseif($teamStats['completion_rate'] >= 40)
                        Средний результат. Есть потенциал для улучшения.
                    @else
                        Низкий результат. Рекомендуется пересмотреть организацию работы.
                    @endif
                </div>
            </div>
        @else
            <!-- Сообщение при отсутствии данных -->
            <div class="flex flex-col items-center justify-center py-6 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium mb-2">Нет данных для анализа</p>
                <p class="text-gray-400 dark:text-gray-500 text-sm">За выбранный период нет задач для расчета эффективности команды.</p>
            </div>
        @endif
    </div>

    <!-- График активности команды -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-medium text-gray-800 dark:text-white mb-4">График активности команды</h2>
        <div class="w-full h-80">
            <canvas id="teamActivityChart"></canvas>
        </div>
    </div>

    <!-- Статистика по участникам команды -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-xl font-medium text-gray-800 dark:text-white mb-4">Статистика по участникам команды</h2>
        
        @if(count($userStats) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Участник</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Всего задач</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Выполнено</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Просрочено</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Выполнение (%)</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($userStats as $userId => $stats)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $stats['name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $stats['email'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $stats['total_tasks'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $stats['completed_tasks'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $stats['overdue_tasks'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center">
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $stats['completion_rate'] }}%"></div>
                                        </div>
                                        <span class="ml-2 text-gray-500 dark:text-gray-300">{{ $stats['completion_rate'] }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <button type="button" 
                                            class="inline-flex items-center px-3 py-1.5 bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-xs font-medium border border-gray-600 member-details-link"
                                            data-user-id="{{ $userId }}"
                                            data-user-name="{{ $stats['name'] }}"
                                            data-user-stats="{{ json_encode($stats) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Детали
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">В команде пока нет участников.</div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Сохраняем данные статистики команды в глобальном объекте window
    window.teamStats = @json($teamStats);
    
    document.addEventListener('DOMContentLoaded', function() {
        // Управление выпадающим меню экспорта
        const exportDropdownButton = document.getElementById('exportDropdownButton');
        const exportDropdown = document.getElementById('exportDropdown');
        
        if (exportDropdownButton && exportDropdown) {
            // Открытие/закрытие меню при клике на кнопку
            exportDropdownButton.addEventListener('click', function() {
                exportDropdown.classList.toggle('hidden');
            });
            
            // Закрытие меню при клике вне меню
            document.addEventListener('click', function(event) {
                if (!exportDropdownButton.contains(event.target) && !exportDropdown.contains(event.target)) {
                    exportDropdown.classList.add('hidden');
                }
            });
        }
        
        // Инициализация круговой диаграммы для статистики команды
        const statsCtx = document.getElementById('teamStatsChart');
        
        if (statsCtx) {
            const ctx = statsCtx.getContext('2d');
            const isDarkMode = document.documentElement.classList.contains('dark');
            const textColor = isDarkMode ? '#f3f4f6' : '#000000';
            
            // Проверяем, есть ли данные для круговой диаграммы
            const pieChartData = [
                {{ $teamStats['completed_tasks'] }}, 
                {{ $teamStats['in_progress_tasks'] ?? 0 }},
                {{ $teamStats['new_tasks'] ?? 0 }},
                {{ $teamStats['revision_tasks'] ?? 0 }},
                {{ $teamStats['review_tasks'] ?? 0 }},
                {{ $teamStats['overdue_tasks'] }}
            ];
            
            const hasPieData = pieChartData.some(value => value > 0);
            
            // Получаем контейнер для круговой диаграммы
            const pieChartContainer = statsCtx.parentNode;
            
            // Если данных нет, показываем сообщение
            if (!hasPieData) {
                // Удаляем canvas элемент
                statsCtx.remove();
                
                // Создаем сообщение
                const noDataMessage = document.createElement('div');
                noDataMessage.className = 'flex flex-col items-center justify-center h-full text-center p-8';
                noDataMessage.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-lg font-medium mb-2">Нет данных для отображения</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm">За выбранный период нет задач для построения диаграммы.</p>
                `;
                
                // Добавляем сообщение в контейнер
                pieChartContainer.appendChild(noDataMessage);
            } else {
                console.log('Создаем круговую диаграмму');
                const teamStatsChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Выполнено', 'В работе', 'Новая', 'На доработке', 'Отправлено на проверку', 'Просрочено'],
                        datasets: [{
                            data: pieChartData,
                            backgroundColor: [
                                '#10b981', // зеленый для выполненных
                                '#3b82f6', // синий для в работе
                                '#a855f7', // фиолетовый для новых
                                '#f97316', // оранжевый для на доработке
                                '#0ea5e9', // голубой для отправленных на проверку
                                '#ef4444'  // красный для просроченных
                            ],
                            borderWidth: 0, // убираем обводку
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false, // скрываем легенду под диаграммой
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                        const percentage = Math.round((value / total) * 100);
                                        return `${label}: ${value} (${percentage}%)`;
                                    },
                                    title: function(context) {
                                        return ''; // Убираем заголовок подсказки
                                    }
                                },
                                backgroundColor: isDarkMode ? '#374151' : 'rgba(0, 0, 0, 0.8)',
                                titleColor: isDarkMode ? '#ffffff' : '#ffffff',
                                bodyColor: isDarkMode ? '#ffffff' : '#ffffff',
                                bodyFont: {
                                    weight: 'bold',
                                    size: 14
                                },
                                padding: 12,
                                displayColors: false
                            }
                        }
                    }
                });
                
                // Обработчик изменения темы для диаграммы
                const darkModeObserver = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.attributeName === 'class') {
                            // Обновляем диаграмму при изменении темы
                            teamStatsChart.update();
                        }
                    });
                });
                darkModeObserver.observe(document.documentElement, { attributes: true });
            }
        }
        
        // Добавляем обработчик нажатия на кнопку "Подробнее" в разделе эффективности команды
        const detailsButton = document.getElementById('show-team-details');
        if (detailsButton) {
            detailsButton.addEventListener('click', window.TeamAnalytics.showTeamEfficiencyInfo);
        }
        
        // Добавляем обработчик для кнопок детализации по участникам
        document.querySelectorAll('.member-details-link').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.dataset.userId;
                const userName = this.dataset.userName;
                const userStats = JSON.parse(this.dataset.userStats);
                window.TeamAnalytics.showMemberDetailsModal(userId, userName, userStats);
            });
        });
        
        // Получаем данные для графика из переданных в представление данных
        const chartData = @json($chartData);
        
        // Проверяем, есть ли данные для графика
        const hasData = chartData.dates.length > 0 && (
            chartData.totalTasks.some(value => value > 0) || 
            chartData.completedTasks.some(value => value > 0)
        );
        
        // Определяем цвета в зависимости от темы
        const isDarkMode = document.documentElement.classList.contains('dark');
        const graphTextColor = '#e0e0e0'; // Серебристо-серый цвет как на скриншоте
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
        
        // Получаем контейнер для графика
        const activityChart = document.getElementById('teamActivityChart');
        if (activityChart) {
            const chartContainer = activityChart.parentNode;
            
            // Если данных нет, показываем сообщение
            if (!hasData) {
                // Удаляем canvas элемент
                activityChart.remove();
                
                // Создаем сообщение
                const noDataMessage = document.createElement('div');
                noDataMessage.className = 'flex flex-col items-center justify-center h-full text-center p-8';
                noDataMessage.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-lg font-medium mb-2">Нет данных для отображения</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm">За выбранный период нет задач для построения графика активности.</p>
                `;
                
                // Добавляем сообщение в контейнер
                chartContainer.appendChild(noDataMessage);
            } else {
                console.log('Создаем график активности');
                // Создаем график активности команды
                const ctx = activityChart.getContext('2d');
                
                // Настраиваем дополнительные параметры для случая с одним днем
                const chartOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: graphTextColor,
                                font: {
                                    size: 14
                                },
                                padding: 15
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: gridColor,
                                lineWidth: isDarkMode ? 0.5 : 0.5
                            },
                            ticks: {
                                color: graphTextColor,
                                font: {
                                    size: 12,
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: gridColor,
                                lineWidth: isDarkMode ? 0.5 : 0.5
                            },
                            ticks: {
                                color: graphTextColor,
                                precision: 0,
                                font: {
                                    size: 12,
                                }
                            }
                        }
                    }
                };
                
                // Если выбран только один день, добавляем специальные настройки для корректного отображения
                if (chartData.sameDay) {
                    // Настройки подсказок для режима одного дня
                    chartOptions.plugins.tooltip.callbacks = {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            const datasetLabel = context.dataset.label || '';
                            const value = context.parsed.y;
                            const periodLabel = context[0].label.includes('утро') ? 'первая половина дня (00:00-12:00)' : 'вторая половина дня (12:00-24:00)';
                            return `${datasetLabel}: ${value} (${periodLabel})`;
                        }
                    };
                    
                    // Форсируем последовательность слева направо
                    chartOptions.scales.x.reverse = false;
                    chartOptions.scales.x.min = chartData.dates[0];
                    chartOptions.scales.x.max = chartData.dates[1];
                    
                    // Увеличиваем читаемость точек данных
                    chartOptions.elements = {
                        point: {
                            radius: 5,
                            hoverRadius: 7
                        }
                    };
                }
                
                const teamActivityChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.dates,
                        datasets: [
                            {
                                label: 'Всего задач',
                                data: chartData.totalTasks,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Выполненных задач',
                                data: chartData.completedTasks,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true
                            }
                        ]
                    },
                    options: chartOptions
                });
                
                // Обработчик изменения темы
                const darkModeObserver = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.attributeName === 'class') {
                            // Используем переменные из внешней области видимости, не объявляем isDarkMode заново
                            const currentIsDarkMode = document.documentElement.classList.contains('dark');
                            const currentGridColor = currentIsDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
                            
                            teamActivityChart.options.scales.x.ticks.color = graphTextColor;
                            teamActivityChart.options.scales.y.ticks.color = graphTextColor;
                            teamActivityChart.options.plugins.legend.labels.color = graphTextColor;
                            teamActivityChart.options.scales.x.grid.color = currentGridColor;
                            teamActivityChart.options.scales.y.grid.color = currentGridColor;
                            teamActivityChart.update();
                        }
                    });
                });
                
                darkModeObserver.observe(document.documentElement, { attributes: true });
            }
        }
    });
</script>
@endsection 