@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-center text-3xl font-bold text-white mb-6">Редактировать задачу</h1>
        <form action="{{ route('tasks.update', $task->id) }}" method="POST" class="max-w-lg mx-auto bg-gray-800 p-6 rounded-lg shadow-lg">
            @csrf
            @method('PUT')

            <!-- Название задачи -->
            <div class="mb-4">
                <label for="title" class="block text-white text-lg mb-2">Название задачи</label>
                <input type="text" id="title" name="title" class="form-input bg-gray-700 text-white w-full p-3 rounded-md" value="{{ $task->title }}" required>
            </div>

            <!-- Описание -->
            <div class="mb-4">
                <label for="description" class="block text-white text-lg mb-2">Описание</label>
                <textarea id="description" name="description" class="form-textarea bg-gray-700 text-white w-full p-3 rounded-md" rows="4" required>{{ $task->description }}</textarea>
            </div>

            <!-- Дедлайн -->
            <div class="mb-4">
                <label for="deadline" class="block text-white text-lg mb-2">Дедлайн</label>
                <input type="datetime-local" id="deadline" name="deadline" class="form-input bg-gray-700 text-white w-full p-3 rounded-md" value="{{ \Carbon\Carbon::parse($task->deadline)->format('Y-m-d\TH:i') }}" required>
            </div>

            <!-- Команда -->
            <div class="mb-4">
                <label for="team_id" class="block text-white text-lg mb-2">Команда</label>
                <select id="team_id" name="team_id" class="form-select bg-gray-700 text-white w-full p-3 rounded-md">
                    <option value="">Нет команды</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" @if($task->team_id == $team->id) selected @endif>{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Кнопка отправки -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-md">
                    Обновить задачу
                </button>
            </div>
        </form>
    </div>
@endsection
