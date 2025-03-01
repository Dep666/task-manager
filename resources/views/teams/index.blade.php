@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-semibold text-gray-800 dark:text-white mb-6">Управление командами</h1>

        <!-- Сообщение об успешных действиях -->
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Сообщения об ошибках -->
        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">
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
                class="border border-gray-300 rounded-lg px-4 py-2 w-full sm:w-1/2 lg:w-1/3 focus:ring-2 focus:ring-indigo-500"
            >
            <button 
                type="submit" 
                class="bg-blue-600 text-white px-6 py-2 rounded-lg transition duration-300 ease-in-out hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-full sm:w-auto"
            >
                Поиск
            </button>
        </form>

        @if($teams->isEmpty())
            <div class="text-gray-600 text-lg mb-6">
                У вас еще нет команд, но вы можете создать собственную в любой момент.
            </div>
        @else
            <!-- Список команд -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Участники</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($teams as $team)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $team->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @foreach($team->users as $user)
                                        <span>{{ $user->name }}</span><br>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('teams.edit', $team->id) }}" class="text-blue-600 hover:text-blue-900 transition duration-200 ease-in-out">
                                        Редактировать
                                    </a>
                                    <!-- Кнопка добавления пользователя -->
                                    <a href="{{ route('teams.addUser', $team->id) }}" class="ml-4 text-green-600 hover:text-green-900 transition duration-200 ease-in-out">
                                        Добавить пользователя
                                    </a>
                                    <!-- Кнопка редактирования участников -->
                                    <a href="{{ route('teams.editUsers', $team->id) }}" class="ml-4 text-yellow-600 hover:text-yellow-900 transition duration-200 ease-in-out">
                                        Список участников
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Кнопка для создания команды -->
        <div class="mt-6">
            <a href="{{ route('teams.create') }}" class="bg-green-600 text-white px-6 py-2 rounded-lg transition duration-300 ease-in-out hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                Создать команду
            </a>
        </div>
    </div>
@endsection
