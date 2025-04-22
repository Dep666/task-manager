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
            
            <!-- Скрытое поле для прогресса - по умолчанию 0% -->
            <input type="hidden" id="progress" name="progress" value="0">
            
            <!-- Команда -->
            <div class="mb-4">
                <label for="team_id" class="block text-gray-900 dark:text-white text-lg font-medium mb-2">Команда</label>
                <select id="team_id" name="team_id" class="w-full p-3 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-md">
                    <option value="">Нет команды</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" data-team-id="{{ $team->id }}">{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Исполнитель задачи (доступно только для владельцев команд) -->
            <div class="mb-4" id="assigned_user_container" style="display: none;">
                <label for="assigned_user_id" class="block text-gray-900 dark:text-white text-lg font-medium mb-2">Назначить исполнителя</label>
                <select id="assigned_user_id" name="assigned_user_id" class="w-full p-3 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-md">
                    <option value="">Выберите исполнителя</option>
                </select>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Вы можете назначить задачу конкретному участнику команды</p>
            </div>
            
            <!-- Кнопка -->
            <div class="flex justify-center mt-6">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-300">Создать задачу</button>
            </div>
            
            <!-- Предзагруженные участники команд (скрытый блок) -->
            <div id="team-members-data" style="display: none;" 
                data-members='@json($teamMembers ?? [])'></div>
        </form>
    </div>

    <script>
        // Динамическое отображение списка участников команды с использованием предзагруженных данных
        document.addEventListener('DOMContentLoaded', function() {
            const teamSelect = document.getElementById('team_id');
            const assignedUserContainer = document.getElementById('assigned_user_container');
            const assignedUserSelect = document.getElementById('assigned_user_id');
            
            // Получаем предзагруженные данные о членах команд
            let teamMembersData;
            try {
                teamMembersData = JSON.parse(document.getElementById('team-members-data').getAttribute('data-members'));
            } catch (e) {
                console.error('Ошибка при разборе данных о членах команд:', e);
                teamMembersData = {};
            }
            
            // Обработчик изменения выбранной команды
            teamSelect.addEventListener('change', function() {
                const teamId = this.value;
                
                // Очистка текущего списка участников
                assignedUserSelect.innerHTML = '<option value="">Выберите исполнителя</option>';
                
                if (!teamId) {
                    // Если команда не выбрана, скрываем поле назначения
                    assignedUserContainer.style.display = 'none';
                    return;
                }
                
                // Показываем контейнер выбора исполнителя
                assignedUserContainer.style.display = 'block';
                
                // Проверяем, есть ли участники для этой команды
                const teamMembers = teamMembersData[teamId] || [];
                
                if (teamMembers.length === 0) {
                    // Если участников нет, добавляем опцию "Нет участников"
                    const noMembersOption = document.createElement('option');
                    noMembersOption.value = "";
                    noMembersOption.textContent = "В команде нет участников";
                    assignedUserSelect.appendChild(noMembersOption);
                    return;
                }
                
                // Добавляем участников в список
                teamMembers.forEach(member => {
                    const option = document.createElement('option');
                    option.value = member.id;
                    option.textContent = member.name;
                    assignedUserSelect.appendChild(option);
                });
            });
        });
    </script>
@endsection
