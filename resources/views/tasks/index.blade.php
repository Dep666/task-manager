@extends('layouts.app')

@section('content')
    <div class="container mx-auto my-10 px-6 lg:px-12 dark:bg-gray-900">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Задачи</h1>

        <!-- Форма фильтрации задач -->
        <form method="GET" action="{{ route('tasks.index') }}" class="mb-6">
            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 items-center">
                <!-- Фильтр по типу задачи -->
                <label for="task_type" class="text-gray-900 dark:text-gray-100">Тип задачи:</label>
                <select name="task_type" id="task_type" class="px-4 py-2 bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-md">
                    <option value="">Все задачи</option>
                    <option value="personal" {{ request('task_type') == 'personal' ? 'selected' : '' }}>Личные задачи</option>
                    <option value="team" {{ request('task_type') == 'team' ? 'selected' : '' }}>Задачи в команде</option>
                </select>

                <!-- Фильтр по дедлайну -->
                <label for="deadline_sort" class="text-gray-900 dark:text-gray-100">Сортировка по дедлайну:</label>
                <select name="deadline_sort" id="deadline_sort" class="px-4 py-2 bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-md">
                    <option value="">Без сортировки</option>
                    <option value="soonest" {{ request('deadline_sort') == 'soonest' ? 'selected' : '' }}>Ближайший дедлайн</option>
                    <option value="latest" {{ request('deadline_sort') == 'latest' ? 'selected' : '' }}>Самый дальний дедлайн</option>
                </select>

                <!-- Кнопка фильтра -->
                <button type="submit" class="px-6 py-3 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-300">
                    Применить фильтры
                </button>
            </div>
        </form>

        <a href="{{ route('tasks.create') }}" class="px-6 py-3 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-300 mb-4 inline-block">
            Создать задачу
        </a>

        @if (session('success'))
            <div class="mt-3 p-4 bg-green-500 text-white rounded-md">
                {{ session('success') }}
            </div>
        @elseif (session('error'))
            <div class="mt-3 p-4 bg-red-500 text-white rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <ul class="list-none mt-6 space-y-4">
            @foreach ($tasks as $task)
                <li class="p-4 border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 rounded-md">
                    <strong class="text-lg text-gray-900 dark:text-white">{{ $task->title }}</strong><br>
                    <p class="text-gray-700 dark:text-gray-300">{{ $task->description }}</p><br>
                    <small class="text-gray-500">Дедлайн: {{ $task->deadline }}</small><br>

                    <!-- Статус задачи -->
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        Статус: <span class="font-medium {{ $task->status ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500' }}">
                            {{ $task->status ? $task->status->name : 'Не установлен' }}
                        </span>
                    </p>

                    <!-- Отображение команды, если задача привязана к команде -->
                    @if ($task->team)
                        <p class="text-gray-600 dark:text-gray-400 mt-1">Команда: {{ $task->team->name }}</p>
                    @endif

                    <div class="mt-3 space-x-3 flex flex-wrap justify-start">
                        <a href="{{ route('tasks.edit', $task->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition duration-300 mb-2 sm:mb-0">
                            Редактировать
                        </a>
                        
                        <!-- Кнопка изменения статуса -->
                        @if($task->canChangeStatus(Auth::user()))
                            <a href="{{ route('tasks.change-status', $task->id) }}" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition duration-300 mb-2 sm:mb-0">
                                Изменить статус
                            </a>
                        @endif
                        
                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-300">
                                Удалить
                            </button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>

        <!-- Пагинация -->
        <div class="mt-6">
            {{ $tasks->links() }}
        </div>
    </div>
@endsection
