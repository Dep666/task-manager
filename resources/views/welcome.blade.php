@extends('layouts.app')

@section('content')
    <div class="bg-white dark:bg-gray-900">

        <!-- Основное содержание -->
        <div class="container mx-auto py-12 px-6">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-800 dark:text-white">Добро пожаловать на платформу для управления задачами!</h1>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                    Наш сервис поможет вам эффективно управлять личным временем и работать в команде.
                </p>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                    Создавайте задачи, отслеживайте дедлайны и получайте уведомления прямо в Telegram!
                </p>
            </div>

            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-8">
                <!-- Информация о платформе -->
                <div class="flex flex-col items-center">
                    <img src="{{ asset('img/5252473.jpg') }}" alt="Task Management" class="rounded-lg shadow-lg mb-4 w-3/4 sm:w-1/2 lg:w-40%">
                    <h2 class="text-2xl font-semibold text-gray-800 dark:text-white text-center">Что мы предлагаем?</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-400 text-center">
                        Наша платформа помогает вам не только управлять личными задачами, но и работать с командой, отслеживать важные дедлайны и улучшать продуктивность с помощью интеграции с Telegram.
                    </p>
                </div>

                <!-- Как это работает -->
                <div class="flex flex-col items-center">
                    <img src="{{ asset('img/11743656.png') }}" alt="Task Management" class="rounded-lg shadow-lg mb-4 w-3/4 sm:w-1/2 lg:w-40%">
                    <h2 class="text-2xl font-semibold text-gray-800 dark:text-white text-center">Как это работает?</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-400 text-center">
                        Мы предоставляем простой и удобный интерфейс для создания задач, их приоритезации и совместной работы. Получайте уведомления о задачах прямо в Telegram, чтобы ничего не забыть!
                    </p>
                </div>
            </div>

        </div>
    </div>
@endsection
