<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Дополнительные стили для шрифтов -->
        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
            h1, h2, h3, h4, h5, h6 {
                font-family: 'Montserrat', sans-serif;
                font-weight: 600;
            }
            .btn, button {
                font-family: 'Montserrat', sans-serif;
                font-weight: 500;
            }
        </style>
        
        <!-- Скрипт для определения темной/светлой темы -->
        <script>
            // Устанавливаем темную тему по умолчанию
            document.documentElement.classList.add('dark');
            localStorage.theme = 'dark';
            
            // Функция для переключения темы
            function toggleTheme() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.theme = 'light';
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.theme = 'dark';
                }
            }
        </script>

        <!-- Web Push VAPID ключ и CSRF-токен -->
        <script>
            window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token(),
                'vapidPublicKey' => config('webpush.vapid.public_key'),
            ]) !!};
        </script>

        <!-- Добавьте в секцию <head> следующий код: -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon"/>
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon"/>
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    </head>
    <body class="font-sans antialiased h-full flex flex-col">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col flex-grow">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-grow">
                @yield('content') <!-- Это будет основной контент страницы -->
            </main>

            <!-- Подвал -->
            <footer class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white py-6 mt-auto border-t border-gray-200 dark:border-gray-700">
                <div class="container mx-auto px-4">
                    <div class="flex flex-col sm:flex-row justify-between items-center">
                        <p class="mb-4 sm:mb-0">&copy; {{ date('Y') }} TaskManager. Все права защищены.</p>
                        
                        <!-- Кнопка переключения темы -->
                        <button onclick="toggleTheme()" class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white p-2 rounded-full mb-4 sm:mb-0 focus:outline-none hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>
                        
                        <div class="flex mt-4 sm:mt-0">
                            <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-white mx-2">О нас</a>
                            <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-white mx-2">Политика конфиденциальности</a>
                            <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-white mx-2">Условия использования</a>
                            <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-white mx-2">Контакты</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
