<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TaskManager - Эффективное управление задачами и проектами</title>
    <meta name="description" content="TaskManager - современная платформа для управления проектами, задачами и командами. Повысьте эффективность работы с помощью удобного интерфейса и мощных инструментов для организации задач.">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Стили -->
    <link href="{{ asset('style.css') }}" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased home-page">
    <div class="min-h-screen">
        <!-- Навигационная панель -->
        @include('layouts.navigation')
        
        <!-- Основная секция -->
        <div class="relative isolate px-6 lg:px-8 pt-14 pb-12">
            <!-- Декоративные элементы -->
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl" aria-hidden="true">
                <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#4f46e5] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"></div>
            </div>

            <div class="mx-auto max-w-6xl py-12 sm:py-20">
                <div class="text-center">
                    <h1 class="text-4xl font-bold tracking-tight text-white sm:text-6xl md:text-7xl mb-6 animate-on-scroll fade-in-top">
                        Управляйте задачами <br>на новом уровне
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-300 max-w-3xl mx-auto animate-on-scroll fade-in-bottom">
                        TaskManager — современная платформа для эффективного управления проектами, задачами и командами. 
                        Простой интерфейс, мощные возможности и интеграция с вашими любимыми инструментами.
                    </p>
                    
                    <!-- Кнопки вызова к действию -->
                    <div class="mt-10 flex items-center justify-center gap-x-6 animate-on-scroll fade-in-bottom" style="transition-delay: 0.2s;">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ route('tasks.index') }}" class="rounded-md bg-blue-600 px-6 py-3 text-lg font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all duration-200">Мои задачи</a>
                            @else
                                <a href="{{ route('register') }}" class="rounded-lg bg-blue-600 px-16 py-4 text-lg font-semibold text-white shadow-md hover:bg-blue-500 transition-all duration-200 tracking-wide">Начать бесплатно</a>
                                <a href="{{ route('login') }}" class="text-lg font-semibold leading-6 text-white hover:text-blue-400 transition-all duration-200">Войти <span aria-hidden="true">→</span></a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Анимированные элементы -->
            <div class="relative mt-10 hidden sm:block">
                <div class="absolute -top-10 left-1/4 w-20 h-20 floating">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#4f46e5" class="w-20 h-20">
                        <path d="M11.644 1.59a.75.75 0 01.712 0l9.75 5.25a.75.75 0 010 1.32l-9.75 5.25a.75.75 0 01-.712 0l-9.75-5.25a.75.75 0 010-1.32l9.75-5.25z" />
                        <path d="M3.265 10.602l7.668 4.129a2.25 2.25 0 002.134 0l7.668-4.13 1.37.739a.75.75 0 010 1.32l-9.75 5.25a.75.75 0 01-.71 0l-9.75-5.25a.75.75 0 010-1.32l1.37-.738z" />
                        <path d="M10.933 19.231l-7.668-4.13-1.37.739a.75.75 0 000 1.32l9.75 5.25c.221.12.489.12.71 0l9.75-5.25a.75.75 0 000-1.32l-1.37-.738-7.668 4.13a2.25 2.25 0 01-2.134-.001z" />
                    </svg>
                </div>
                <div class="absolute top-20 right-1/4 w-16 h-16 floating floating-delay-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#f472b6" class="w-16 h-16">
                        <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="absolute top-40 left-1/3 w-12 h-12 floating floating-delay-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#fcd34d" class="w-12 h-12">
                        <path fill-rule="evenodd" d="M9 4.5a.75.75 0 01.721.544l.813 2.846a3.75 3.75 0 002.576 2.576l2.846.813a.75.75 0 010 1.442l-2.846.813a3.75 3.75 0 00-2.576 2.576l-.813 2.846a.75.75 0 01-1.442 0l-.813-2.846a3.75 3.75 0 00-2.576-2.576l-2.846-.813a.75.75 0 010-1.442l2.846-.813A3.75 3.75 0 007.466 7.89l.813-2.846A.75.75 0 019 4.5zM18 1.5a.75.75 0 01.728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 010 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 01-1.456 0l-.258-1.036a2.625 2.625 0 00-1.91-1.91l-1.036-.258a.75.75 0 010-1.456l1.036-.258a2.625 2.625 0 001.91-1.91l.258-1.036A.75.75 0 0118 1.5zM16.5 15a.75.75 0 01.712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 010 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 01-1.422 0l-.395-1.183a1.5 1.5 0 00-.948-.948l-1.183-.395a.75.75 0 010-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0116.5 15z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            
            <!-- Демо-окно с кодом -->
            <div class="mx-auto max-w-5xl mt-12 sm:mt-24 animate-on-scroll scale-in">
                <div class="rounded-2xl bg-gray-900/80 p-4 shadow-2xl ring-1 ring-white/10">
                    <div class="flex items-center border-b border-gray-700 pb-3">
                        <div class="flex space-x-2">
                            <div class="h-3 w-3 rounded-full bg-red-500"></div>
                            <div class="h-3 w-3 rounded-full bg-yellow-500"></div>
                            <div class="h-3 w-3 rounded-full bg-green-500"></div>
                        </div>
                        <div class="ml-4 text-sm text-gray-400">task-manager.ts</div>
                    </div>
                    <div class="mt-4 overflow-hidden">
                        <pre class="text-sm text-blue-400">
<span class="text-pink-400">import</span> { TaskManager } <span class="text-pink-400">from</span> <span class="text-green-400">'@task-manager/core'</span>;

<span class="text-gray-500">// Создаем экземпляр менеджера задач</span>
<span class="text-pink-400">const</span> <span class="text-blue-300">manager</span> = <span class="text-pink-400">new</span> TaskManager();

<span class="text-gray-500">// Добавляем задачу с высоким приоритетом</span>
<span class="text-blue-300">manager</span>.<span class="text-yellow-300">createTask</span>({
  <span class="text-blue-300">title</span>: <span class="text-green-400">'Запустить новый проект'</span>,
  <span class="text-blue-300">description</span>: <span class="text-green-400">'Подготовить все материалы и собрать команду'</span>,
  <span class="text-blue-300">priority</span>: <span class="text-green-400">'high'</span>,
  <span class="text-blue-300">dueDate</span>: <span class="text-green-400">'2023-12-30'</span>,
  <span class="text-blue-300">assignee</span>: <span class="text-green-400">'user_id_123'</span>
});

<span class="text-gray-500">// Добавляем подзадачу</span>
<span class="text-blue-300">manager</span>.<span class="text-yellow-300">addSubtask</span>(<span class="text-green-400">'task_123'</span>, <span class="text-green-400">'Организовать встречу с командой'</span>);

<span class="text-gray-500">// Получаем все актуальные задачи</span>
<span class="text-pink-400">const</span> <span class="text-blue-300">tasks</span> = <span class="text-pink-400">await</span> <span class="text-blue-300">manager</span>.<span class="text-yellow-300">getTasks</span>({ <span class="text-blue-300">status</span>: <span class="text-green-400">'active'</span> });

<span class="text-gray-500">// Выводим список задач</span>
<span class="text-blue-300">console</span>.<span class="text-yellow-300">log</span>(<span class="text-green-400">'Все активные задачи:'</span>, <span class="text-blue-300">tasks</span>);
                        </pre>
                        <div class="bg-gradient-to-r from-gray-900 to-gray-800 p-2 mt-3 rounded-md">
                            <div class="typing-demo text-white text-lg font-semibold pl-1">Эффективно. Просто. Мощно.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Секция-разделитель -->
    <div class="section-divider">
        <div class="divider-line"></div>
    </div>
    
    <!-- Секция преимуществ с явной секцией -->
    <section class="section-block py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center mb-16 animate-on-scroll fade-in-bottom">
                <h2 class="text-xl font-semibold leading-7 text-blue-400 mb-4">Больше возможностей</h2>
                <p class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl mb-8">Все, что нужно для управления проектами</p>
                <p class="mt-6 text-lg leading-8 text-gray-300">Откройте для себя мощный инструмент, который поможет вам и вашей команде организовать работу и повысить продуктивность.</p>
            </div>
            <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-4xl">
                <div class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-2 mx-auto text-center">
                    <div class="flex flex-col items-center animate-on-scroll fade-in-left">
                        <div class="flex-shrink-0 flex items-center justify-center w-24 h-24 rounded-full bg-blue-600 text-white mb-6">
                            <span class="text-4xl" role="img" aria-label="Иконка задач и подзадач">⭐</span>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-4">Задачи и подзадачи</h3>
                        <p class="text-base text-gray-300">Структурируйте свои проекты, разбивайте задачи на подзадачи, устанавливайте приоритеты и сроки.</p>
                    </div>
                    <div class="flex flex-col items-center animate-on-scroll fade-in-right">
                        <div class="flex-shrink-0 flex items-center justify-center w-24 h-24 rounded-full bg-blue-600 text-white mb-6">
                            <span class="text-4xl" role="img" aria-label="Иконка командной работы">👨‍👩‍👧‍👦</span>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-4">Командная работа</h3>
                        <p class="text-base text-gray-300">Приглашайте членов команды, назначайте задачи, отслеживайте прогресс и коммуницируйте без задержек.</p>
                    </div>
                    <div class="flex flex-col items-center animate-on-scroll fade-in-left">
                        <div class="flex-shrink-0 flex items-center justify-center w-24 h-24 rounded-full bg-blue-600 text-white mb-6">
                            <span class="text-4xl" role="img" aria-label="Иконка статистики и отчетов">📊</span>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-4">Статистика и отчеты</h3>
                        <p class="text-base text-gray-300">Анализируйте производительность, следите за временем выполнения задач и формируйте наглядные отчеты.</p>
                    </div>
                    <div class="flex flex-col items-center animate-on-scroll fade-in-right">
                        <div class="flex-shrink-0 flex items-center justify-center w-24 h-24 rounded-full bg-blue-600 text-white mb-6">
                            <span class="text-4xl" role="img" aria-label="Иконка календаря и напоминаний">📆</span>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-4">Календарь и напоминания</h3>
                        <p class="text-base text-gray-300">Планируйте задачи в календаре, устанавливайте напоминания и получайте уведомления о важных событиях.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Секция-разделитель -->
    <div class="section-divider">
        <div class="divider-line"></div>
    </div>

    <!-- CTA секция с явной секцией -->
    <section class="section-block py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center animate-on-scroll fade-in-bottom">
                <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl mb-6">Начните управлять задачами уже сегодня</h2>
                <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-gray-300 mb-8">
                    Присоединяйтесь к тысячам команд, которые уже оптимизировали свою работу с TaskManager.
                </p>
                <div class="mt-12 flex items-center justify-center gap-x-6 animate-on-scroll scale-in" style="transition-delay: 0.2s;">
                    <a href="{{ route('register') }}" class="rounded-lg bg-blue-600 px-16 py-4 text-lg font-semibold text-white shadow-md hover:bg-blue-500 transition-all duration-200 tracking-wide">Начать бесплатно</a>
                    <a href="#" class="text-lg font-semibold text-white px-6 py-2 hover:text-blue-400 transition-all duration-200 flex items-center">
                        Узнать больше 
                        <span class="ml-2 text-blue-400">→</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Секция-разделитель -->
    <div class="section-divider">
        <div class="divider-line"></div>
    </div>

    <!-- Футер -->
    <footer class="bg-gray-950 text-white py-12 relative z-10">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="xl:grid xl:grid-cols-3 xl:gap-8">
                <div class="space-y-8 xl:col-span-1">
                    <h2 class="text-2xl font-bold">TaskManager</h2>
                    <p class="text-gray-400 text-sm">
                        Современная платформа для управления задачами и проектами. Создана для повышения производительности и эффективности работы команд любого размера.
                    </p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-white">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true" role="img" aria-label="Иконка Facebook">
                                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true" role="img" aria-label="Иконка Twitter">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <span class="sr-only">GitHub</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true" role="img" aria-label="Иконка GitHub">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 xl:col-span-2 xl:mt-0">
                    <div class="md:grid md:grid-cols-2 md:gap-8">
                        <div>
                            <h3 class="text-sm font-semibold text-white">Продукт</h3>
                            <ul role="list" class="mt-4 space-y-3">
                                <li><a href="#" class="text-sm text-gray-300 hover:text-white">Возможности</a></li>
                                <li><a href="#" class="text-sm text-gray-300 hover:text-white">Интеграции</a></li>
                                <li><a href="#" class="text-sm text-gray-300 hover:text-white">Цены</a></li>
                                <li><a href="#" class="text-sm text-gray-300 hover:text-white">Обновления</a></li>
                            </ul>
                        </div>
                        <div class="mt-10 md:mt-0">
                            <h3 class="text-sm font-semibold text-white">Поддержка</h3>
                            <ul role="list" class="mt-4 space-y-3">
                                <li><a href="#" class="text-sm text-gray-300 hover:text-white">Документация</a></li>
                                <li><a href="#" class="text-sm text-gray-300 hover:text-white">Руководства</a></li>
                                <li><a href="#" class="text-sm text-gray-300 hover:text-white">Видеоуроки</a></li>
                                <li><a href="#" class="text-sm text-gray-300 hover:text-white">Сообщество</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="md:grid md:grid-cols-2 md:gap-8">
                        <div>
                            <h3 class="text-sm font-semibold text-white">Компания</h3>
                            <ul role="list" class="mt-4 space-y-3">
                                <li><a href="#" class="text-sm text-gray-300 hover:text-white">О нас</a></li>
                                <li><a href="#" class="text-sm text-gray-300 hover:text-white">Карьера</a></li>
                                <li><a href="#" class="text-sm text-gray-300 hover:text-white">Блог</a></li>
                                <li><a href="#" class="text-sm text-gray-300 hover:text-white">Пресса</a></li>
                            </ul>
                        </div>
                        <div class="mt-10 md:mt-0">
                            <h3 class="text-sm font-semibold text-white">Правовая информация</h3>
                            <ul role="list" class="mt-4 space-y-3">
                                <li><a href="#" class="text-sm text-gray-300 hover:text-white">Условия использования</a></li>
                                <li><a href="#" class="text-sm text-gray-300 hover:text-white">Политика конфиденциальности</a></li>
                                <li><a href="#" class="text-sm text-gray-300 hover:text-white">Cookie-файлы</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-12 border-t border-gray-800 pt-8">
                <div class="flex flex-col md:flex-row md:justify-between">
                    <p class="text-xs text-gray-400">&copy; {{ date('Y') }} TaskManager. Все права защищены.</p>
                    <p class="mt-4 md:mt-0 text-xs text-gray-400">Разработано с ❤️ для повышения вашей продуктивности</p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Скрипт для определения темы и анимации при скроллинге -->
    <script>
        // Устанавливаем темную тему по умолчанию
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
        
        // Скрипт для анимации при скроллинге
        document.addEventListener('DOMContentLoaded', function() {
            // Сразу показываем первый экран
            setTimeout(function() {
                document.querySelectorAll('.min-h-screen .animate-on-scroll').forEach(function(el) {
                    el.classList.add('visible');
                });
            }, 300);
            
            // Настраиваем обсервер для остальных элементов
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        setTimeout(function() {
                            entry.target.classList.add('visible');
                        }, 150);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                root: null,
                rootMargin: '0px',
                threshold: 0.15  // Элемент становится видимым, когда 15% его видно в области просмотра
            });
            
            // Наблюдаем за всеми элементами с классом animate-on-scroll
            document.querySelectorAll('.animate-on-scroll').forEach(function(el) {
                if (!el.closest('.min-h-screen')) {
                    observer.observe(el);
                }
            });
        });
    </script>
</body>
</html>
