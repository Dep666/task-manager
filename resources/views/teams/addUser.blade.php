<!-- resources/views/teams/addUser.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Пригласить пользователя в команду: {{ $team->name }}</h2>
        
        <p class="mb-5 text-gray-600 dark:text-gray-400">
            Пользователю будет отправлено приглашение, которое он сможет принять или отклонить.
        </p>

        <!-- Форма добавления пользователя -->
        <form method="POST" action="{{ route('teams.addUserPost', $team->id) }}" class="space-y-6">
            @csrf

            <!-- Поле для ввода email или ID пользователя -->
            <div class="mb-4">
                <label for="user_identifier" class="block text-sm font-medium text-gray-900 dark:text-gray-300 mb-1">Введите email, ID или код пользователя</label>
                <input 
                    type="text" 
                    name="user_identifier" 
                    id="user_identifier" 
                    class="block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:bg-gray-700 dark:text-white"
                    placeholder="email, ID или код пользователя"
                    value="{{ old('user_identifier') }}"
                    required
                >

                @if($errors->has('user_identifier'))
                    <div class="text-red-500 dark:text-red-400 text-sm mt-2">
                        {{ $errors->first('user_identifier') }}
                    </div>
                @endif
            </div>

            <div class="flex flex-wrap gap-3">
                <!-- Кнопка для добавления -->
                <a 
                    href="{{ route('teams.editUsers', $team->id) }}" 
                    class="flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-sm font-medium border border-gray-600"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Назад
                </a>
                <button 
                    type="submit" 
                    class="flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-sm font-medium border border-gray-600"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Отправить приглашение
                </button>
                
                
            </div>
        </form>
    </div>
@endsection
