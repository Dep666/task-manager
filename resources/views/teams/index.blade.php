@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl sm:text-3xl font-semibold text-gray-800 dark:text-white mb-6">Управление командами</h1>

        <!-- Сообщение об успешных действиях -->
        @if(session('success'))
            <div class="bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 p-4 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Сообщения об ошибках -->
        @if($errors->any())
            <div class="bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 p-4 rounded-lg mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Форма фильтрации команд -->
        <form method="GET" action="{{ route('teams.index') }}" class="mb-6 flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
            <input 
                type="text" 
                name="name" 
                value="{{ request('name') }}" 
                placeholder="Поиск по имени команды" 
                class="border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 w-full sm:w-1/2 lg:w-1/3 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
            >
            <button 
                type="submit" 
                class="flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded-lg hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-sm font-medium border border-gray-600"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Поиск
            </button>
        </form>

        @if($teams->isEmpty())
            <div class="text-gray-600 dark:text-gray-400 text-lg mb-6">
                У вас пока нет команд. Создайте свою первую команду или попросите кого-нибудь добавить вас в существующую.
            </div>
        @else
            <!-- Для мобильных устройств - список карточек -->
            <div class="grid grid-cols-1 gap-4 mb-8">
                @foreach($teams as $team)
                    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden rounded-lg p-4">
                        <div class="font-medium text-lg text-gray-900 dark:text-white mb-2">{{ $team->name }}</div>
                        
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <p class="font-medium mb-1">Участники:</p>
                            @foreach($team->users as $user)
                                <div>{{ $user->name }}</div>
                            @endforeach
                        </div>
                        
                        <div class="flex flex-wrap gap-2 mt-4">
                            @if(auth()->id() === $team->owner_id)
                                <a href="{{ route('teams.edit', $team->id) }}" class="flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-sm font-medium border border-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Редактировать
                                </a>
                            @endif
                            
                            <a href="{{ route('teams.editUsers', $team->id) }}" class="flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-sm font-medium border border-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Список участников
                            </a>
                            
                            @if(auth()->id() === $team->owner_id)
                            <a href="{{ route('teams.analytics', $team->id) }}" class="flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-sm font-medium border border-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Аналитика
                            </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Кнопка для создания команды -->
        <div class="mt-6">
                <a href="{{ route('teams.create') }}" 
                    class="inline-flex items-center px-4 py-2 max-w-xs truncate bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-sm font-medium border border-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Создать команду
        </a>
        </div>
    </div>
@endsection
