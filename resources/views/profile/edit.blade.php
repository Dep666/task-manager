@extends('layouts.app')

@section('content')
    <div class="py-6 bg-gray-100 dark:bg-gray-900 text-center">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Профиль</h1>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Обновление информации профиля -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="max-w-xl mx-auto">
                        @include('profile.partials.update-profile-information-form', ['user' => $user])
                    </div>
                </div>
                
                <!-- Кнопка для привязки Telegram -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="max-w-xl mx-auto">
                        <!-- Если у пользователя уже есть привязанный Telegram, показываем информацию, иначе кнопку -->
                        @if($user->telegram_username)
                            <p class="text-lg text-green-600 dark:text-green-400">Ваш аккаунт Telegram привязан</p>
                        @else
                        <button 
                            class="w-full py-3 px-4 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg shadow-sm hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 uppercase font-semibold"
                            onclick="window.location.href = 'https://t.me/team_task_manager_bot?start={{ urlencode(Auth::user()->email) }}'">
                            Привязать аккаунт Telegram
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Обновление пароля -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="max-w-xl mx-auto">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Удаление пользователя -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="max-w-xl mx-auto">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

