@extends('layouts.app')

@section('content')
    <div class="container mx-auto my-10 px-6 lg:px-12 dark:bg-gray-900">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Список задач</h1>
            <div class="flex space-x-4">
                <a href="{{ route('tasks.create') }}" class="flex items-center px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition duration-300 text-sm font-medium border border-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Создать задачу
                </a>
                <a href="{{ route('tasks.archive') }}" class="flex items-center px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition duration-300 text-sm font-medium border border-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    Архив задач
                </a>
            </div>
        </div>

        <!-- Кнопка-триггер для фильтра -->
        <button id="filter-toggle" class="mb-4 bg-white dark:bg-gray-800 rounded shadow px-4 py-3 flex items-center justify-between w-72 transition-all duration-300 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700">
            <span class="flex items-center text-gray-800 dark:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Фильтры и сортировка
            </span>
            <svg id="filter-arrow" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Форма фильтрации задач с анимацией -->
        <div id="filter-container" class="hidden bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-4 origin-top">
            <div class="p-6">
                <form method="GET" action="{{ route('tasks.index') }}" class="mb-0">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                        <!-- Фильтр по типу задачи -->
                        <div>
                            <label for="task_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Тип задачи:</label>
                            <select name="task_type" id="task_type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Все задачи</option>
                                <option value="personal" {{ request('task_type') == 'personal' ? 'selected' : '' }}>Личные задачи</option>
                                <option value="team" {{ request('task_type') == 'team' ? 'selected' : '' }}>Задачи в команде</option>
                            </select>
                        </div>

                        <!-- Фильтр по дедлайну -->
                        <div>
                            <label for="deadline_sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Сортировка по дедлайну:</label>
                            <select name="deadline_sort" id="deadline_sort" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Без сортировки</option>
                                <option value="asc" {{ request('deadline_sort') == 'asc' ? 'selected' : '' }}>Сначала ближайшие</option>
                                <option value="desc" {{ request('deadline_sort') == 'desc' ? 'selected' : '' }}>Сначала дальние</option>
                            </select>
                        </div>

                        <!-- Фильтр по статусу -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Статус задачи:</label>
                            <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Все статусы</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->name }}" {{ request('status') == $status->name ? 'selected' : '' }}>{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Кнопка фильтра -->
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition duration-300 text-sm font-medium border border-gray-600">
                                Применить фильтры
                            </button>
                            @if(request()->has('task_type') || request()->has('deadline_sort') || request()->has('status'))
                                <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition duration-300 text-sm font-medium border border-gray-600">
                                    Сбросить
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="task-content" class="mt-6">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-800 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @elseif (session('error'))
                <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 dark:bg-red-800 dark:text-red-200">
                    {{ session('error') }}
                </div>
            @endif
            
            @if ($tasks->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 text-center">
                    <p class="text-gray-700 dark:text-gray-300 text-lg">У вас пока нет активных задач.</p>
                </div>
            @else
                <ul class="list-none space-y-4">
                    @foreach ($tasks as $task)
                        <li class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 transition-transform hover:shadow-lg relative">
                            <!-- Оборачиваем только содержимое, кроме кнопок, в ссылку -->
                            <div class="task-content" style="position: relative;">
                                <a href="{{ route('tasks.show', $task->id) }}" class="block absolute inset-0 z-10"></a>

                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-xl font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $task->title }}
                                    </span>
                                    
                                    <!-- Статус задачи (как бейдж) -->
                                    <span class="inline-block px-4 py-2 text-base font-bold rounded-md shadow bg-white dark:bg-gray-700
                                        @if($task->status && str_contains(strtolower($task->status->name), 'выполнен'))
                                            text-gray-700 border-2 border-green-600 dark:text-gray-300 dark:border-green-500
                                        @elseif($task->status && str_contains(strtolower($task->status->name), 'доработ'))
                                            text-gray-700 border-2 border-yellow-600 dark:text-gray-300 dark:border-yellow-500
                                        @elseif($task->status && str_contains(strtolower($task->status->name), 'проверк'))
                                            text-gray-700 border-2 border-blue-600 dark:text-gray-300 dark:border-blue-500
                                        @else
                                            text-gray-700 border-2 border-gray-600 dark:text-gray-300 dark:border-gray-500
                                        @endif
                                    ">
                                        {{ $task->status ? $task->status->name : 'Не установлен' }}
                                    </span>
                                </div>
                                
                                <p class="text-gray-700 dark:text-gray-300 mb-4">{{ $task->description }}</p>
                                
                                <!-- Отображение комментария к задаче, если он есть -->
                                @if(isset($task->feedback) && $task->feedback)
                                    <div class="mb-4 p-4 bg-indigo-50 dark:bg-gray-700 rounded-md border border-indigo-200 dark:border-indigo-800">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                            </svg>
                                            <span class="font-semibold text-indigo-700 dark:text-indigo-300">Комментарий:</span>
                                        </div>
                                        <p class="ml-7 whitespace-pre-line text-gray-700 dark:text-white">{{ $task->feedback }}</p>
                                    </div>
                                @endif

                                <div class="flex flex-wrap items-center gap-4 mb-4">
                                    <span class="inline-flex items-center text-gray-600 dark:text-gray-300">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-medium">Дедлайн:</span> {{ \Carbon\Carbon::parse($task->deadline)->format('d.m.Y H:i') }}
                                    </span>
                                    
                                    <!-- Отображение исполнителя, если он назначен -->
                                    @if(isset($task->assigned_user_id) && $task->assigned_user_id)
                                        <span class="inline-flex items-center text-gray-600 dark:text-gray-300">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span class="font-medium">Исполнитель:</span> {{ $task->assignedUser ? $task->assignedUser->name : 'Не назначен' }}
                                        </span>
                                    @endif

                                    <!-- Отображение команды, если задача привязана к команде -->
                                    @if ($task->team)
                                        <span class="inline-flex items-center text-gray-600 dark:text-gray-300">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <span class="font-medium">Команда:</span> {{ $task->team->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2 mt-4" style="position: relative; z-index: 20;">
                                @if($task->user_id === Auth::id() || ($task->team && $task->team->owner_id === Auth::id()))
                                    <a href="{{ route('tasks.edit', $task->id) }}" class="flex items-center px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition duration-300 text-sm font-medium border border-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Редактировать
                                    </a>
                                    
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex items-center px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition duration-300 text-sm font-medium border border-gray-600" onclick="return confirm('Вы уверены, что хотите удалить эту задачу?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Удалить
                                        </button>
                                    </form>
                                @endif
                                
                                <!-- Кнопка изменения статуса -->
                                @php
                                    $completedStatusSlugs = ['completed', 'team_completed'];
                                    $isCompleted = $task->status && in_array($task->status->slug, $completedStatusSlugs);
                                @endphp
                                
                                @if(!$isCompleted && $task->canChangeStatus(Auth::user()))
                                    <a href="{{ route('tasks.change-status', $task->id) }}" class="flex items-center px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition duration-300 text-sm font-medium border border-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Изменить статус
                                    </a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>

                <!-- Пагинация -->
                <div class="mt-6">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- JavaScript для анимации фильтра -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterToggle = document.getElementById('filter-toggle');
            const filterContainer = document.getElementById('filter-container');
            const filterArrow = document.getElementById('filter-arrow');
            const filterForm = document.querySelector('#filter-container form');
            const taskContent = document.getElementById('task-content');
            
            // Проверка, были ли установлены фильтры
            const urlParams = new URLSearchParams(window.location.search);
            const hasFilters = urlParams.has('task_type') || urlParams.has('deadline_sort') || urlParams.has('status');
            
            // Добавляем стиль анимации в head
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideDown {
                    0% {
                        transform: scaleY(0);
                        opacity: 0;
                    }
                    20% {
                        transform: scaleY(0.2);
                        opacity: 0.3;
                    }
                    60% {
                        transform: scaleY(0.8);
                        opacity: 0.8;
                    }
                    100% {
                        transform: scaleY(1);
                        opacity: 1;
                    }
                }
                
                @keyframes slideUp {
                    0% {
                        transform: scaleY(1);
                        opacity: 1;
                    }
                    40% {
                        transform: scaleY(0.8);
                        opacity: 0.8;
                    }
                    80% {
                        transform: scaleY(0.2);
                        opacity: 0.3;
                    }
                    100% {
                        transform: scaleY(0);
                        opacity: 0;
                    }
                }
                
                .slide-down {
                    animation: slideDown 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
                    transform-origin: top;
                }
                
                .slide-up {
                    animation: slideUp 0.4s cubic-bezier(0.55, 0.085, 0.68, 0.53) forwards;
                    transform-origin: top;
                }
                
                .loading-indicator {
                    display: inline-block;
                    position: relative;
                    width: 80px;
                    height: 20px;
                }
                .loading-indicator div {
                    position: absolute;
                    top: 5px;
                    width: 10px;
                    height: 10px;
                    border-radius: 50%;
                    background: #3b82f6;
                    animation-timing-function: cubic-bezier(0, 1, 1, 0);
                    box-shadow: 0 0 3px rgba(0, 0, 0, 0.2);
                }
                .loading-indicator div:nth-child(1) {
                    left: 8px;
                    animation: loading1 0.6s infinite;
                }
                .loading-indicator div:nth-child(2) {
                    left: 8px;
                    animation: loading2 0.6s infinite;
                }
                .loading-indicator div:nth-child(3) {
                    left: 32px;
                    animation: loading2 0.6s infinite;
                }
                .loading-indicator div:nth-child(4) {
                    left: 56px;
                    animation: loading3 0.6s infinite;
                }
                @keyframes loading1 {
                    0% {
                        transform: scale(0);
                    }
                    100% {
                        transform: scale(1);
                    }
                }
                @keyframes loading3 {
                    0% {
                        transform: scale(1);
                    }
                    100% {
                        transform: scale(0);
                    }
                }
                @keyframes loading2 {
                    0% {
                        transform: translate(0, 0);
                    }
                    100% {
                        transform: translate(24px, 0);
                    }
                }
            `;
            document.head.appendChild(style);
            
            // Если фильтры установлены, показываем форму фильтров по умолчанию
            if (hasFilters) {
                filterContainer.classList.remove('hidden');
                filterContainer.classList.add('slide-down');
                filterArrow.classList.add('transform', 'rotate-180');
            }
            
            filterToggle.addEventListener('click', function() {
                if (filterContainer.classList.contains('hidden')) {
                    // Открываем фильтр
                    filterContainer.classList.remove('hidden', 'slide-up');
                    filterContainer.classList.add('slide-down');
                    filterArrow.classList.add('transform', 'rotate-180');
                } else {
                    // Закрываем фильтр
                    filterContainer.classList.remove('slide-down');
                    filterContainer.classList.add('slide-up');
                    filterArrow.classList.remove('transform', 'rotate-180');
                    
                    // После завершения анимации скрываем
                    filterContainer.addEventListener('animationend', function handler() {
                        filterContainer.classList.add('hidden');
                        filterContainer.classList.remove('slide-up');
                        filterContainer.removeEventListener('animationend', handler);
                    }, { once: true });
                }
            });

            // AJAX отправка формы фильтрации
            if (filterForm) {
                filterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Показываем индикатор загрузки
                    taskContent.innerHTML = '<div class="flex justify-center items-center py-12"><div class="relative w-24 h-6"><div class="loading-indicator"><div></div><div></div><div></div><div></div></div></div></div>';
                    
                    const formData = new FormData(filterForm);
                    const searchParams = new URLSearchParams(formData);
                    const url = `${filterForm.action}?${searchParams.toString()}`;
                    
                    // Обновляем URL без перезагрузки
                    window.history.pushState({ path: url }, '', url);
                    
                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Создаем временный элемент для парсинга HTML
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Извлекаем только содержимое блока с задачами
                        const newTaskContent = doc.getElementById('task-content');
                        if (newTaskContent) {
                            taskContent.innerHTML = newTaskContent.innerHTML;
                            
                            // Переподключаем обработчики событий для кнопок в обновленном контенте
                            setupTaskEventListeners();
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка при загрузке задач:', error);
                        taskContent.innerHTML = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert"><p>Произошла ошибка при загрузке задач. Пожалуйста, попробуйте еще раз.</p></div>';
                    });
                });
                
                // Обработка кнопки "Сбросить"
                const resetButton = filterForm.querySelector('a[href="{{ route('tasks.index') }}"]');
                if (resetButton) {
                    resetButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        // Сбрасываем значения полей формы
                        filterForm.reset();
                        
                        // Имитируем отправку формы с пустыми значениями
                        const event = new Event('submit', { bubbles: true });
                        filterForm.dispatchEvent(event);
                    });
                }
            }
            
            // Функция для обработки AJAX-пагинации
            function setupPagination() {
                const paginationLinks = document.querySelectorAll('#task-content .pagination a');
                
                paginationLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        const url = this.getAttribute('href');
                        
                        // Показываем индикатор загрузки
                        taskContent.innerHTML = '<div class="flex justify-center items-center py-12"><div class="relative w-24 h-6"><div class="loading-indicator"><div></div><div></div><div></div><div></div></div></div></div>';
                        
                        // Обновляем URL без перезагрузки
                        window.history.pushState({ path: url }, '', url);
                        
                        fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            
                            const newTaskContent = doc.getElementById('task-content');
                            if (newTaskContent) {
                                taskContent.innerHTML = newTaskContent.innerHTML;
                                
                                // Переподключаем обработчики событий
                                setupTaskEventListeners();
                                setupPagination();
                            }
                        })
                        .catch(error => {
                            console.error('Ошибка при загрузке задач:', error);
                            taskContent.innerHTML = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert"><p>Произошла ошибка при загрузке задач. Пожалуйста, попробуйте еще раз.</p></div>';
                        });
                    });
                });
            }
            
            function setupTaskEventListeners() {
                // Здесь можно добавить код для работы с кнопками в обновленном контенте
                // Например, подтверждения удаления и т.д.
                const deleteButtons = document.querySelectorAll('form[action*="tasks/"][action*="destroy"] button');
                deleteButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        if (!confirm('Вы уверены, что хотите удалить эту задачу?')) {
                            e.preventDefault();
                        }
                    });
                });
                
                // Настраиваем пагинацию для работы через AJAX
                setupPagination();
            }
            
            // Инициализация обработчиков при загрузке страницы
            setupTaskEventListeners();
        });
    </script>
@endsection