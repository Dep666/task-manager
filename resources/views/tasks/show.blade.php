@extends('layouts.app')

@section('content')
    <div class="container mx-auto my-10 px-6 lg:px-12 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <style>
                    /* Запрет выделения и изменения курсора в полях */
                    .task-detail-page input,
                    .task-detail-page textarea,
                    .task-detail-page select {
                        pointer-events: none;
                        user-select: none;
                    }
                </style>
                <div class="task-detail-page">
                    <div class="flex justify-between items-start mb-6">
                        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $task->title }}</h2>
                        
                        <!-- Статус задачи (как бейдж) -->
                        <span class="inline-block px-3 py-1 text-sm font-medium rounded-full
                            @if($task->status && str_contains(strtolower($task->status->name), 'выполнен'))
                                bg-green-700 text-white dark:bg-green-900 dark:text-white border-2 border-green-500 dark:border-green-500
                            @elseif($task->status && str_contains(strtolower($task->status->name), 'доработ'))
                                bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100 border-2 border-yellow-200 dark:border-yellow-500
                            @elseif($task->status && str_contains(strtolower($task->status->name), 'проверк'))
                                bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 border-2 border-blue-200 dark:border-blue-500
                            @else
                                bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100 border-2 border-gray-200 dark:border-gray-500
                            @endif
                        ">
                            {{ $task->status ? $task->status->name : 'Не установлен' }}
                        </span>
                    </div>
                    
                    <!-- Детали задачи -->
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Описание</h3>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-md mb-4">
                            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $task->description }}</p>
                        </div>
                        
                        <!-- Информация о дедлайне и исполнителе -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Сроки</h4>
                                <p class="text-gray-800 dark:text-gray-200 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Дедлайн: {{ \Carbon\Carbon::parse($task->deadline)->format('d.m.Y H:i') }}
                                </p>
                            </div>
                            
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Ответственные</h4>
                                <p class="text-gray-800 dark:text-gray-200 flex items-center mb-2">
                                    <svg class="w-5 h-5 mr-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Создатель: {{ $task->user->name }}
                                </p>
                                
                                @if(isset($task->assigned_user_id) && $task->assigned_user_id)
                                    <p class="text-gray-800 dark:text-gray-200 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                                        </svg>
                                        Исполнитель: {{ $task->assignedUser ? $task->assignedUser->name : 'Не назначен' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Информация о команде, если задача командная -->
                        @if($task->team)
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-md mb-6">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Команда</h4>
                                <p class="text-gray-800 dark:text-gray-200 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    {{ $task->team->name }}
                                </p>
                            </div>
                        @endif
                        
                        <!-- Комментарий от руководителя -->
                        @if(isset($task->feedback) && $task->feedback)
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-indigo-700 dark:text-indigo-300 mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                    </svg>
                                    Комментарий от руководителя:
                                </h3>
                                <div class="p-4 bg-indigo-50 dark:bg-gray-700 text-gray-800 dark:text-white rounded-md border border-indigo-200 dark:border-indigo-800">
                                    <p class="whitespace-pre-line">{{ $task->feedback }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Кнопки действий -->
                    <div class="flex flex-wrap gap-4 mt-6">
                        <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition duration-300 text-sm font-medium border border-gray-600 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Назад к списку
                        </a>
                        
                        @php
                            $completedStatusSlugs = ['completed', 'team_completed'];
                            $isCompleted = $task->status && in_array($task->status->slug, $completedStatusSlugs);
                        @endphp
                        
                        @if(!$isCompleted && $task->canChangeStatus(Auth::user()))
                            <a href="{{ route('tasks.change-status', $task->id) }}" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition duration-300 text-sm font-medium border border-gray-600 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Изменить статус
                            </a>
                        @endif
                        
                        @if($task->user_id === Auth::id() || ($task->team && $task->team->owner_id === Auth::id()))
                            <a href="{{ route('tasks.edit', $task->id) }}" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition duration-300 text-sm font-medium border border-gray-600 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Редактировать
                            </a>
                            
                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition duration-300 text-sm font-medium border border-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Удалить
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Подтверждение удаления
        const deleteForms = document.querySelectorAll('.delete-form');
        
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (confirm('Вы уверены, что хотите удалить эту задачу? Это действие нельзя отменить.')) {
                    this.submit();
                }
            });
        });
    });
    </script>
@endsection 