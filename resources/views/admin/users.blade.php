@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Пользователи</h1>
    
    @if(session('success'))
        <div class="bg-green-500 text-white p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    <!-- Адаптивный дизайн для мобильных устройств: карточки пользователей -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($users as $user)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-blue-100 dark:bg-blue-900 rounded-full p-2 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $user->name }}</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">ID: {{ $user->id }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-semibold border-2 
                          {{ $user->role === 'admin' ? 'bg-red-600 text-white dark:bg-red-700 dark:text-white border-red-700' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 border-blue-300 dark:border-blue-700' }}">
                        {{ $user->role }}
                    </span>
                </div>
            </div>
            <div class="p-4 space-y-3">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="text-gray-600 dark:text-gray-300 text-sm">{{ $user->email }}</span>
                </div>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-900 flex flex-wrap gap-2">
                <a href="{{ route('admin.editUser', $user->id) }}" class="inline-flex items-center px-3 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Редактировать
                </a> 
                <a href="{{ route('admin.deleteUser', $user->id) }}" class="inline-flex items-center px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm" onclick="return confirm('Вы уверены?')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Удалить
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
