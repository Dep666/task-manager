@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Редактировать команду: {{ $team->name }}</h2>

        <!-- Форма редактирования команды -->
        <div class="space-y-6">
            <form action="{{ route('teams.update', $team) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Поле для имени команды -->
                <div>
                    <x-input-label for="name" :value="__('Название команды')" class="text-gray-900 dark:text-gray-300" />
                    <x-text-input 
                        id="name" 
                        name="name" 
                        type="text" 
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:bg-gray-700 dark:text-white" 
                        value="{{ old('name', $team->name) }}" 
                        required 
                        autofocus 
                    />
                    @error('name')
                        <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Кнопки управления -->
                <div class="flex flex-wrap items-center gap-3">
                    <a 
                        href="{{ route('teams.index') }}" 
                        class="flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-sm font-medium border border-gray-600"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Отмена
                    </a>
                    <button 
                        type="submit" 
                        class="flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-sm font-medium border border-gray-600"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Сохранить изменения
                    </button>
                    
                    <!-- Кнопка удаления команды -->
                    <button 
                        type="button"
                        onclick="if(confirm('Вы уверены, что хотите удалить эту команду? Это действие необратимо.')) document.getElementById('delete-form').submit();"
                        class="flex items-center px-4 py-2 bg-red-600 dark:bg-red-700 text-white rounded hover:bg-red-700 dark:hover:bg-red-800 transition duration-300 text-sm font-medium border border-red-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Удалить команду
                    </button>
                </div>
            </form>

            <!-- Скрытая форма для удаления -->
            <form id="delete-form" action="{{ route('teams.destroy', $team) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
@endsection
