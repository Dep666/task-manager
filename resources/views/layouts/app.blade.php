<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
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
            <main>
                @yield('content') <!-- Это будет основной контент страницы -->
            </main>

            <!-- Подвал -->
            <footer class="bg-gray-800 text-white py-6 mt-10">
                <div class="container mx-auto text-center">
                    <p>&copy; 2025 Моя Платформа. Все права защищены.</p>
                    <div class="mt-2">
                        <a href="#" class="text-gray-400 hover:text-white mx-2">О нас</a>
                        <a href="#" class="text-gray-400 hover:text-white mx-2">Политика конфиденциальности</a>
                        <a href="#" class="text-gray-400 hover:text-white mx-2">Контакты</a>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
