@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl sm:text-3xl font-semibold text-gray-800 dark:text-white mb-6">Участники команды: {{ $team->name }}</h1>

        <!-- Сообщение об успешных действиях -->
        @if(session('success'))
            <div class="bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 p-4 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Сообщения об ошибках -->
        @if(session('error'))
            <div class="bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 p-4 rounded-lg mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mb-8">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Имя участника</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Роль</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Действия</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Сначала выводим создателя команды -->
                    @php
                        $owner = $team->users->firstWhere('id', $team->owner_id);
                        $members = $team->users->filter(function($user) use ($team) {
                            return $user->id != $team->owner_id;
                        });
                    @endphp
                    
                    @if($owner)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $owner->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <span style="background-color: #16A34A; color: white; border-radius: 9999px; padding: 4px 12px; font-size: 0.75rem; font-weight: 600;">
                                Создатель команды
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <!-- Пустая ячейка - создателя нельзя удалить -->
                        </td>
                    </tr>
                    @endif
                    
                    <!-- Затем выводим остальных участников -->
                    @foreach($members as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <span style="background-color: #3B82F6; color: white; border-radius: 9999px; padding: 4px 12px; font-size: 0.75rem; font-weight: 600;">
                                    Участник
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if ($team->owner_id == auth()->user()->id) <!-- Проверка, если текущий пользователь является владельцем -->
                                    <form action="{{ route('teams.removeUser', ['team' => $team->id, 'user' => $user->id]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="flex items-center text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition duration-200 ease-in-out"
                                            onclick="return confirm('Вы уверены, что хотите удалить этого участника?')"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Удалить
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap gap-3">
            <a 
                href="{{ route('teams.index') }}" 
                class="flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-sm font-medium border border-gray-600"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Назад
            </a>
            
            <a 
                href="{{ route('teams.edit', $team->id) }}" 
                class="flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-sm font-medium border border-gray-600"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Редактировать команду
            </a>
            
            <a 
                href="{{ route('teams.addUser', $team->id) }}" 
                class="flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-sm font-medium border border-gray-600"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Добавить пользователя
            </a>
        </div>
    </div>
@endsection
