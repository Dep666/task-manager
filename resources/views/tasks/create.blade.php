@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center p-6">
        <form action="{{ route('tasks.store') }}" method="POST" class="max-w-lg w-full bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
            @csrf
            <h1 class="text-center mb-6 text-gray-900 dark:text-white text-3xl font-bold">Создание задачи</h1>
            <!-- Название задачи -->
            <div class="mb-4">
                <label for="title" class="block text-gray-900 dark:text-white text-lg font-medium mb-2">Название задачи</label>
                <input type="text" id="title" name="title" class="w-full p-3 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-md" required>
            </div>
            <!-- Описание -->
            <div class="mb-4">
                <label for="description" class="block text-gray-900 dark:text-white text-lg font-medium mb-2">Описание</label>
                <textarea id="description" name="description" class="w-full p-3 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-md" rows="4" required></textarea>
            </div>
            <!-- Дедлайн -->
            <div class="mb-4">
                <label for="deadline" class="block text-gray-900 dark:text-white text-lg font-medium mb-2">Дедлайн</label>
                <input type="datetime-local" id="deadline" name="deadline" class="w-full p-3 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-md" required>
            </div>
            <!-- Команда -->
            <div class="mb-4">
                <label for="team_id" class="block text-gray-900 dark:text-white text-lg font-medium mb-2">Команда</label>
                <select id="team_id" name="team_id" class="w-full p-3 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-md">
                    <option value="">Нет команды</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Кнопка -->
            <div class="flex justify-center mt-6">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-300">Создать задачу</button>
            </div>
        </form>
    </div>
@endsection
