@extends('layouts.app')

@section('content')
    <div class="container mx-auto my-10 px-6 lg:px-12 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6" id="task-status-form">
                <h2 class="text-xl font-extrabold mb-4 text-gray-900 dark:text-white">Изменение статуса задачи</h2>
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $task->title }}</h3>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $task->description }}</p>
                    <div class="mt-2">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Дедлайн: {{ \Carbon\Carbon::parse($task->deadline)->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="mt-1">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Текущий статус: {{ $task->status ? $task->status->name : 'Не установлен' }}</span>
                    </div>
                </div>

                @if(count($statuses) > 0)
                    <form action="{{ route('tasks.update-status', $task) }}" method="POST" class="mt-4">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-4">
                            <label for="status_id" class="block text-sm font-bold text-gray-900 dark:text-white">Новый статус</label>
                            <select id="status_id" name="status_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-semibold text-gray-900 dark:text-white">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ ($task->status && $task->status->id === $status->id) ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-900 dark:text-white uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 active:bg-gray-500 dark:active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:shadow-outline-gray disabled:opacity-25 transition mr-2">
                                Отмена
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue disabled:opacity-25 transition">
                                Сохранить
                            </button>
                        </div>
                    </form>
                @else
                    <div class="mt-4 font-bold text-red-500 dark:text-red-400">
                        Нет доступных статусов для изменения.
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-900 dark:text-white uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 active:bg-gray-500 dark:active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:shadow-outline-gray disabled:opacity-25 transition">
                            Назад
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Явно устанавливаем белый цвет для текста в темной теме */
        @media (prefers-color-scheme: dark) {
            /* Специальный селектор для темной темы */
            .dark-bg-gray-800 h2, 
            .dark-bg-gray-800 h3, 
            .dark-bg-gray-800 p, 
            .dark-bg-gray-800 span, 
            .dark-bg-gray-800 label,
            .dark-bg-gray-800 select,
            .dark-bg-gray-800 option,
            .dark-bg-gray-800 a:not(.bg-blue-600) {
                color: white !important;
            }
        }
    </style>
@endsection 