@extends('layouts.app')

@section('content')
    <!-- Подключение Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    
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
                <label for="deadline_display" class="block text-gray-900 dark:text-white text-lg mb-2">Дедлайн</label>
                <input type="text" id="deadline_display" class="flatpickr form-input bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white w-full p-3 rounded-md" placeholder="Выберите дату и время" value="{{ \Carbon\Carbon::parse($task->deadline)->format('d.m.Y H:i') }}" required>
                <!-- Скрытое поле с форматом для MySQL -->
                <input type="hidden" id="deadline" name="deadline" value="{{ $task->deadline }}">
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

    <!-- Подключение Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js"></script>

    <script>
        // Инициализация Flatpickr для поля дедлайна
        document.addEventListener('DOMContentLoaded', function() {
            // Получаем текущую дату и время
            const now = new Date();
            
            // Получаем ссылки на поля
            const deadlineDisplay = document.getElementById('deadline_display');
            const deadlineInput = document.getElementById('deadline');
            
            // Форматирование даты для MySQL
            function formatDateForMySQL(date) {
                const year = date.getFullYear();
                const month = (date.getMonth() + 1).toString().padStart(2, '0');
                const day = date.getDate().toString().padStart(2, '0');
                const hours = date.getHours().toString().padStart(2, '0');
                const minutes = date.getMinutes().toString().padStart(2, '0');
                const seconds = date.getSeconds().toString().padStart(2, '0');
                
                return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
            }
            
            // Инициализируем Flatpickr
            const fp = flatpickr(deadlineDisplay, {
                enableTime: true,          // Включаем выбор времени
                dateFormat: "d.m.Y H:i",   // Формат даты и времени для отображения
                locale: "ru",              // Русская локализация
                time_24hr: true,           // 24-часовой формат времени
                minDate: now,              // Минимальная дата и время - текущие
                defaultHour: now.getHours(),
                defaultMinute: now.getMinutes() + 5, // Устанавливаем минуты на 5 минут вперед
                allowInput: false,         // Запрещаем ввод с клавиатуры
                clickOpens: true,          // Открытие календаря по клику
                position: "auto",          // Автоматическое позиционирование календаря
                
                // Обработчик изменения даты
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        const selectedDate = selectedDates[0];
                        const currentDate = new Date();
                        
                        // Проверяем, что выбранная дата/время не в прошлом
                        if (selectedDate < currentDate) {
                            alert('Нельзя выбрать прошедшее время. Выберите время в будущем.');
                            instance.setDate(currentDate); // Сбрасываем на текущее время
                            return;
                        }
                        
                        // Форматируем дату для MySQL и заполняем скрытое поле
                        deadlineInput.value = formatDateForMySQL(selectedDate);
                    }
                },
                
                // Определяем тему в зависимости от текущей темы сайта
                onReady: function(selectedDates, dateStr, instance) {
                    // Проверяем темную тему
                    if (document.querySelector('html').classList.contains('dark')) {
                        instance.calendarContainer.classList.add('dark-theme');
                    }
                    
                    // Добавляем кнопку "Сейчас + 1 час"
                    const plusHourBtn = document.createElement("button");
                    plusHourBtn.textContent = "+1 час";
                    plusHourBtn.className = "flatpickr-today-btn bg-gray-800 text-white px-3 py-1 rounded-md mx-2 my-2 text-sm";
                    plusHourBtn.addEventListener("click", function() {
                        const newDate = new Date();
                        newDate.setHours(newDate.getHours() + 1);
                        instance.setDate(newDate);
                    });
                    
                    // Добавляем кнопку "Сейчас"
                    const todayBtn = document.createElement("button");
                    todayBtn.textContent = "Сейчас";
                    todayBtn.className = "flatpickr-today-btn bg-gray-800 text-white px-3 py-1 rounded-md mx-2 my-2 text-sm";
                    todayBtn.addEventListener("click", function() {
                        const nowPlusFive = new Date();
                        nowPlusFive.setMinutes(nowPlusFive.getMinutes() + 5);
                        instance.setDate(nowPlusFive);
                    });
                    
                    const container = instance.calendarContainer;
                    const monthNav = container.querySelector(".flatpickr-months");
                    if (monthNav) {
                        const customBtnContainer = document.createElement("div");
                        customBtnContainer.className = "flatpickr-custom-buttons flex justify-center my-2";
                        customBtnContainer.appendChild(todayBtn);
                        customBtnContainer.appendChild(plusHourBtn);
                        container.insertBefore(customBtnContainer, monthNav.nextSibling);
                    }
                    
                    // Установить начальное значение скрытого поля, если дата выбрана
                    if (selectedDates.length > 0) {
                        deadlineInput.value = formatDateForMySQL(selectedDates[0]);
                    }
                },
                
                // Дополнительная проверка перед закрытием календаря
                onClose: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        const selectedDate = selectedDates[0];
                        const currentDate = new Date();
                        
                        // Если выбранная дата в прошлом, корректируем её
                        if (selectedDate < currentDate) {
                            const nowPlusFive = new Date();
                            nowPlusFive.setMinutes(nowPlusFive.getMinutes() + 5);
                            instance.setDate(nowPlusFive);
                            alert('Установлено время на 5 минут вперед от текущего, так как нельзя выбрать прошедшее время.');
                        }
                    }
                }
            });
            
            // Валидация при отправке формы
            const form = document.querySelector('form');
            
            form.addEventListener('submit', function(e) {
                if (!deadlineDisplay.value) {
                    e.preventDefault();
                    alert('Пожалуйста, выберите дату и время дедлайна.');
                    deadlineDisplay.focus();
                    return;
                }
                
                const selectedDateTime = fp.selectedDates[0];
                const currentDate = new Date();
                
                if (selectedDateTime < currentDate) {
                    e.preventDefault();
                    alert('Дедлайн не может быть в прошлом. Пожалуйста, выберите будущую дату.');
                    deadlineDisplay.focus();
                    
                    // Автоматически устанавливаем время на 5 минут вперёд
                    const nowPlusFive = new Date();
                    nowPlusFive.setMinutes(nowPlusFive.getMinutes() + 5);
                    fp.setDate(nowPlusFive);
                }
            });
        });
    </script>

    <style>
        /* Стили для flatpickr */
        .flatpickr-calendar {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            border-radius: 8px !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            font-family: inherit !important;
            width: 325px !important;
            padding: 8px !important;
        }
        
        .flatpickr-day {
            border-radius: 6px !important;
            margin: 2px !important;
            height: 38px !important;
            line-height: 38px !important;
        }
        
        .flatpickr-day.selected {
            background-color: #1f2937 !important;
            border-color: #1f2937 !important;
            color: white !important;
        }
        
        .flatpickr-day:hover {
            background-color: #f3f4f6 !important;
        }
        
        .flatpickr-months {
            padding-bottom: 5px !important;
        }
        
        .flatpickr-current-month {
            font-size: 16px !important;
        }
        
        .numInputWrapper {
            margin-left: 5px !important;
        }
        
        /* Для темной темы */
        .dark-theme {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }
        
        .dark-theme .flatpickr-day {
            color: #e5e7eb !important;
        }
        
        .dark-theme .flatpickr-day.selected {
            background-color: #4b5563 !important;
            border-color: #4b5563 !important;
            color: white !important;
        }
        
        .dark-theme .flatpickr-day:hover {
            background-color: #374151 !important;
        }
        
        .dark-theme .flatpickr-months .flatpickr-month {
            color: #e5e7eb !important;
            fill: #e5e7eb !important;
        }
        
        .dark-theme .flatpickr-current-month .flatpickr-monthDropdown-months,
        .dark-theme .flatpickr-current-month input.cur-year {
            color: #e5e7eb !important;
        }
        
        .dark-theme .flatpickr-time input,
        .dark-theme .flatpickr-time .flatpickr-time-separator {
            color: #e5e7eb !important;
        }
        
        .flatpickr-today-btn {
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .flatpickr-today-btn:hover {
            opacity: 0.85;
        }
    </style>
@endsection
