@extends('layouts.app')

@section('content')
    <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-white">Профиль</h1>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Обновление информации профиля -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl mx-auto"> <!-- mx-auto добавлен для центрирования -->
                        @include('profile.partials.update-profile-information-form', ['user' => $user])
                    </div>
                </div>
                
                <!-- Кнопка для привязки Telegram -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl mx-auto">
                        <!-- Если у пользователя уже есть привязанный Telegram, показываем информацию, иначе кнопку -->
                        @if($user->telegram_username)
                            <p class="text-lg text-green-600">Ваш аккаунт Telegram привязан</p>
                        @else
                        <button 
    class="w-full py-2 px-4 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
    onclick="window.location.href = 'https://t.me/team_task_manager_bot?start={{ urlencode(Auth::user()->email) }}'">
    Привязать аккаунт Telegram
</button>





                        @endif
                    </div>
                </div>

                <!-- Обновление пароля -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl mx-auto"> <!-- mx-auto добавлен для центрирования -->
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Удаление пользователя -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl mx-auto"> <!-- mx-auto добавлен для центрирования -->
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

