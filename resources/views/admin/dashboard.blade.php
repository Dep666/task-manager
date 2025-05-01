@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl sm:text-3xl font-bold text-center mb-8 text-gray-900 dark:text-white">Панель администратора</h1>
    
    <div class="max-w-lg mx-auto bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <ul class="space-y-4">
            <li>
                <a href="{{ route('admin.users') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-200 gap-3">
                    <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-800 dark:text-white">Управление пользователями</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Просмотр, редактирование и удаление пользователей</p>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.teams') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-200 gap-3">
                    <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-800 dark:text-white">Управление командами</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Просмотр, редактирование и удаление команд</p>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</div>
@endsection
