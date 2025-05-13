@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Демонстрация Notyf</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold mb-4">Основные уведомления</h2>
                
                <div class="space-y-4">
                    <button id="success-notification" class="w-full p-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                        Успешное уведомление
                            </button>
                    
                    <button id="error-notification" class="w-full p-3 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                Ошибка
                            </button>
                    
                    <button id="info-notification" class="w-full p-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                Информация
                            </button>
                    
                    <button id="warning-notification" class="w-full p-3 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition">
                        Предупреждение
                            </button>
                        </div>
                    </div>
                    
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold mb-4">Диалоги</h2>
                
                <div class="space-y-4">
                    <button id="basic-dialog" class="w-full p-3 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                        Простой диалог
                            </button>
                    
                    <button id="confirm-dialog" class="w-full p-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                        Диалог подтверждения
                            </button>
                    
                    <button id="delete-dialog" class="w-full p-3 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                        Диалог удаления
                            </button>
                        </div>
                    </div>
                </div>
        
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-lg font-semibold mb-4">Кастомизация уведомлений</h2>
            
            <p class="mb-4 text-gray-600 dark:text-gray-300">
                Примеры более сложных уведомлений, демонстрирующие возможности Notyf.
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button id="custom-position" class="p-3 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                    Другая позиция
                </button>
                
                <button id="custom-duration" class="p-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                    Длительное уведомление (10 сек)
                </button>
                
                <button id="custom-icon" class="p-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Без иконки
                </button>
                
                <button id="dismissible" class="p-3 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition">
                    С кнопкой закрытия
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Основные уведомления
        document.getElementById('success-notification').addEventListener('click', function() {
            showSuccess('Операция успешно выполнена!');
        });
        
        document.getElementById('error-notification').addEventListener('click', function() {
            showError('Произошла ошибка при выполнении операции.');
        });
        
        document.getElementById('info-notification').addEventListener('click', function() {
            showInfo('Это информационное сообщение.');
        });
        
        document.getElementById('warning-notification').addEventListener('click', function() {
            showWarning('Внимание! Это предупреждение.');
        });
        
        // Диалоги
        document.getElementById('basic-dialog').addEventListener('click', function() {
            confirmAction({
                title: 'Информация',
                text: 'Это простой диалог с одной кнопкой',
                confirmButtonText: 'Понятно'
            });
        });
        
        document.getElementById('confirm-dialog').addEventListener('click', function() {
            confirmAction({
                title: 'Вы уверены?',
                text: 'Вы хотите продолжить выполнение операции?',
                confirmButtonText: 'Да, продолжить',
                cancelButtonText: 'Отмена'
            }).then(result => {
                if (result) {
                    showSuccess('Операция подтверждена!');
                } else {
                    showInfo('Операция отменена');
                }
            });
        });
        
        document.getElementById('delete-dialog').addEventListener('click', function() {
            confirmAction({
                title: 'Удалить запись?',
                text: 'Это действие нельзя будет отменить!',
                confirmButtonText: 'Да, удалить',
                cancelButtonText: 'Отмена'
            }).then(result => {
                if (result) {
                    setTimeout(() => {
                        showSuccess('Запись успешно удалена!');
                    }, 500);
                }
            });
        });
        
        // Кастомные уведомления
        document.getElementById('custom-position').addEventListener('click', function() {
            const notyf = new Notyf({
                position: {
                    x: 'left',
                    y: 'top'
                }
            });
            notyf.success('Уведомление в левом верхнем углу');
        });
        
        document.getElementById('custom-duration').addEventListener('click', function() {
            const notyf = new Notyf({
                duration: 10000,
                ripple: true
            });
            notyf.success('Это уведомление будет отображаться 10 секунд');
        });
        
        document.getElementById('custom-icon').addEventListener('click', function() {
            const notyf = new Notyf({
                types: [{
                    type: 'custom',
                    background: '#3B82F6',
                    icon: false
                }]
            });
            notyf.open({
                type: 'custom',
                message: 'Уведомление без иконки'
            });
        });
        
        document.getElementById('dismissible').addEventListener('click', function() {
            const notyf = new Notyf({
                types: [{
                    type: 'warning',
                    background: '#F59E0B',
                    dismissible: true
                }]
            });
            notyf.open({
                type: 'warning',
                message: 'Нажмите на X, чтобы закрыть'
            });
        });
    });
</script>
@endsection 