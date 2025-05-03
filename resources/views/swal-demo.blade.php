@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h1 class="text-2xl font-bold mb-6">Демонстрация SweetAlert2</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Простые уведомления -->
                    <div class="bg-gray-700 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Уведомления</h2>
                        <div class="space-y-3">
                            <button onclick="showSuccess('Операция успешно выполнена!')" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded">
                                Успех
                            </button>
                            <button onclick="showError('Произошла ошибка при выполнении операции')" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded">
                                Ошибка
                            </button>
                            <button onclick="showWarning('Внимание! Эта операция не может быть отменена')" 
                                    class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded">
                                Предупреждение
                            </button>
                            <button onclick="showInfo('Это информационное сообщение')" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                                Информация
                            </button>
                        </div>
                    </div>
                    
                    <!-- Диалоги с подтверждением -->
                    <div class="bg-gray-700 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Диалоги подтверждения</h2>
                        <div class="space-y-4">
                            <button onclick="confirmDelete()" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded">
                                Удалить элемент
                            </button>
                            <button onclick="confirmAction()" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                                Подтвердить действие
                            </button>
                            <button onclick="inputPrompt()" 
                                    class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded">
                                Запросить ввод
                            </button>
                        </div>
                    </div>
                    
                    <!-- Расширенные примеры -->
                    <div class="bg-gray-700 p-6 rounded-lg col-span-1 md:col-span-2">
                        <h2 class="text-xl font-semibold mb-4">Расширенные примеры</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button onclick="showCustomModal()" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded">
                                Кастомное модальное окно
                            </button>
                            <button onclick="showImageModal()" 
                                    class="bg-pink-600 hover:bg-pink-700 text-white py-2 px-4 rounded">
                                Модальное окно с изображением
                            </button>
                            <button onclick="showAnimatedModal()" 
                                    class="bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-4 rounded">
                                Анимированное модальное окно
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Подключение SweetAlert2 через CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<script>
    // Настройка темной темы для SweetAlert2
    const darkTheme = {
        background: '#1f2937',
        text: '#f3f4f6',
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Подтвердить',
        cancelButtonText: 'Отмена'
    };
    
    // Настраиваем Toast уведомления
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    
    // Функции для уведомлений
    function showSuccess(message) {
        Toast.fire({
            icon: 'success',
            title: message,
            background: darkTheme.background,
            color: darkTheme.text
        });
    }
    
    function showError(message) {
        Toast.fire({
            icon: 'error',
            title: message,
            background: darkTheme.background,
            color: darkTheme.text
        });
    }
    
    function showInfo(message) {
        Toast.fire({
            icon: 'info',
            title: message,
            background: darkTheme.background,
            color: darkTheme.text
        });
    }
    
    function showWarning(message) {
        Toast.fire({
            icon: 'warning',
            title: message,
            background: darkTheme.background,
            color: darkTheme.text
        });
    }
    
    // Функция для демонстрации диалога удаления
    function confirmDelete() {
        Swal.fire({
            title: 'Удалить элемент?',
            text: 'Вы действительно хотите удалить этот элемент? Это действие нельзя будет отменить.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: darkTheme.confirmButtonColor,
            cancelButtonColor: darkTheme.cancelButtonColor,
            confirmButtonText: 'Да, удалить',
            cancelButtonText: darkTheme.cancelButtonText,
            background: darkTheme.background,
            color: darkTheme.text
        }).then((result) => {
            if (result.isConfirmed) {
                showSuccess('Элемент успешно удален!');
            }
        });
    }
    
    // Функция для демонстрации диалога подтверждения
    function confirmAction() {
        Swal.fire({
            title: 'Подтверждение действия',
            text: 'Вы действительно хотите выполнить это действие?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: darkTheme.confirmButtonColor,
            cancelButtonColor: darkTheme.cancelButtonColor,
            confirmButtonText: darkTheme.confirmButtonText,
            cancelButtonText: darkTheme.cancelButtonText,
            background: darkTheme.background,
            color: darkTheme.text
        }).then((result) => {
            if (result.isConfirmed) {
                showSuccess('Действие успешно выполнено!');
            } else {
                showInfo('Действие отменено');
            }
        });
    }
    
    // Функция для демонстрации запроса ввода
    function inputPrompt() {
        Swal.fire({
            title: 'Введите название новой задачи',
            input: 'text',
            inputPlaceholder: 'Например: Создать презентацию',
            showCancelButton: true,
            confirmButtonColor: darkTheme.confirmButtonColor,
            cancelButtonColor: darkTheme.cancelButtonColor,
            confirmButtonText: darkTheme.confirmButtonText,
            cancelButtonText: darkTheme.cancelButtonText,
            background: darkTheme.background,
            color: darkTheme.text,
            inputValidator: (value) => {
                if (!value) {
                    return 'Поле не может быть пустым';
                }
            }
        }).then((result) => {
            if (result.value) {
                showSuccess(`Задача "${result.value}" создана!`);
            }
        });
    }
    
    // Функция для демонстрации кастомного модального окна
    function showCustomModal() {
        Swal.fire({
            title: 'Кастомное модальное окно',
            html: `
                <div class="py-3">
                    <p class="mb-3">Это модальное окно с кастомным HTML-содержимым.</p>
                    <div class="flex justify-center">
                        <div class="bg-blue-900 p-3 rounded-lg">
                            <h3 class="text-lg font-bold text-blue-300">Преимущества TaskManager</h3>
                            <ul class="text-white list-disc list-inside text-left">
                                <li>Управление задачами</li>
                                <li>Трекинг времени</li>
                                <li>Командная работа</li>
                                <li>Интеграции с сервисами</li>
                            </ul>
                        </div>
                    </div>
                </div>
            `,
            background: darkTheme.background,
            color: darkTheme.text,
            confirmButtonText: 'Круто!',
            confirmButtonColor: darkTheme.confirmButtonColor
        });
    }
    
    // Функция для демонстрации модального окна с изображением
    function showImageModal() {
        Swal.fire({
            title: 'Красивое модальное окно',
            imageUrl: 'https://media.sweetalert2.com//assets/examples/9.png',
            imageWidth: 400,
            imageHeight: 200,
            imageAlt: 'Картинка',
            background: darkTheme.background,
            color: darkTheme.text,
            confirmButtonText: 'Закрыть',
            confirmButtonColor: darkTheme.confirmButtonColor
        });
    }
    
    // Функция для демонстрации анимированного модального окна
    function showAnimatedModal() {
        Swal.fire({
            title: 'Анимированное модальное окно',
            text: 'С разными анимациями',
            background: darkTheme.background,
            color: darkTheme.text,
            confirmButtonText: 'Закрыть',
            confirmButtonColor: darkTheme.confirmButtonColor,
            showClass: {
                popup: 'animate__animated animate__bounceIn'
            },
            hideClass: {
                popup: 'animate__animated animate__bounceOut'
            }
        });
    }
</script>
@endsection 