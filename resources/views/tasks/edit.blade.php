@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center p-6">
        <form action="{{ route('tasks.update', $task->id) }}" method="POST" class="max-w-lg w-full bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
            @csrf
            @method('PUT')

            <h1 class="text-center text-3xl font-bold text-gray-900 dark:text-white mb-6">Редактировать задачу</h1>

            <!-- Название задачи -->
            <div class="mb-4">
                <label for="title" class="block text-gray-900 dark:text-white text-lg mb-2">Название задачи</label>
                <input type="text" id="title" name="title" class="form-input bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white w-full p-3 rounded-md" value="{{ $task->title }}" required>
            </div>

            <!-- Описание -->
            <div class="mb-4">
                <label for="description" class="block text-gray-900 dark:text-white text-lg mb-2">Описание</label>
                <textarea id="description" name="description" class="form-textarea bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white w-full p-3 rounded-md" rows="4" required>{{ $task->description }}</textarea>
            </div>

            <!-- Дедлайн -->
            <div class="mb-4">
                <label for="deadline" class="block text-gray-900 dark:text-white text-lg mb-2">Дедлайн</label>
                <input type="datetime-local" id="deadline" name="deadline" class="form-input bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white w-full p-3 rounded-md" value="{{ \Carbon\Carbon::parse($task->deadline)->format('Y-m-d\TH:i') }}" required>
            </div>

            <!-- Команда -->
            <div class="mb-4">
                <label for="team_id" class="block text-gray-900 dark:text-white text-lg mb-2">Команда</label>
                <select id="team_id" name="team_id" class="form-select bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white w-full p-3 rounded-md">
                    <option value="">Нет команды</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" @if($task->team_id == $team->id) selected @endif>{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Кнопка отправки -->
            <div class="text-center">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition duration-300 text-sm font-medium border border-gray-600">
                    Обновить задачу
                </button>
            </div>
        </form>
    </div>
@endsection
