<!-- Адаптивное навигационное меню с поддержкой светлой/темной темы -->
<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 sticky top-0 z-50 shadow-md border-b border-gray-200 dark:border-gray-700">
    <div class="container mx-auto px-4">
        <div class="flex justify-between h-16">
            <!-- Логотип - смещен левее -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('welcome') }}" class="text-blue-700 dark:text-blue-400 font-bold text-2xl tracking-wide">
                    TaskManager
                </a>
            </div>
            
            <!-- Навигационные ссылки - смещены правее -->
            <div class="hidden sm:flex sm:items-center sm:space-x-6 ml-auto">
                @auth
                    <a href="{{ route('welcome') }}" class="{{ request()->routeIs('welcome') ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-400' }} transition px-4 py-2 rounded-md font-medium">
                        Главная
                    </a>
                    <a href="{{ route('tasks.index') }}" class="{{ request()->routeIs('tasks.index') ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-400' }} transition px-4 py-2 rounded-md font-medium">
                        Задачи
                    </a>
                    <a href="{{ route('tasks.archive') }}" class="{{ request()->routeIs('tasks.archive') ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-400' }} transition px-4 py-2 rounded-md font-medium">
                        Архив задач
                    </a>
                    <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-400' }} transition px-4 py-2 rounded-md font-medium">
                        Профиль
                    </a>
                    <a href="{{ route('teams.index') }}" class="{{ request()->routeIs('teams.*') ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-400' }} transition px-4 py-2 rounded-md font-medium">
                        Команда
                    </a>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-400' }} transition px-4 py-2 rounded-md font-medium">
                            Админ панель
                        </a>
                    @endif
                    
                    <!-- Кнопка Выйти в стиле навигации -->
                    <form method="POST" action="{{ route('logout') }}" class="inline-flex items-center">
                        @csrf
                        <button type="submit" class="text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-red-600 hover:text-white transition px-4 py-2 rounded-md font-medium">
                            Выйти
                        </button>
                    </form>
                @else
                    <div class="flex space-x-4">
                        <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-400 transition px-4 py-2 rounded-md font-medium">
                            Войти
                        </a>
                        <a href="{{ route('register') }}" class="text-white bg-blue-600 hover:bg-blue-700 transition px-4 py-2 rounded-md font-medium">
                            Регистрация
                        </a>
                    </div>
                @endauth
            </div>
            
            <!-- Кнопка мобильного меню -->
            <div class="sm:hidden flex items-center">
                <button id="mobile-menu-button" class="text-gray-700 dark:text-gray-200 hover:text-blue-700 dark:hover:text-blue-400 p-2 focus:outline-none bg-gray-100 dark:bg-gray-700 rounded-md">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Мобильное меню -->
    <div id="mobile-menu" class="hidden sm:hidden transform transition-all duration-300 ease-in-out origin-top bg-white dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="px-3 pt-3 pb-4 space-y-2">
                @auth
                    <a href="{{ route('welcome') }}" class="{{ request()->routeIs('welcome') ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-400' }} block px-4 py-2 rounded-md text-base font-medium">
                        Главная
                    </a>
                    <a href="{{ route('tasks.index') }}" class="{{ request()->routeIs('tasks.index') ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-400' }} block px-4 py-2 rounded-md text-base font-medium">
                        Задачи
                    </a>
                    <a href="{{ route('tasks.archive') }}" class="{{ request()->routeIs('tasks.archive') ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-400' }} block px-4 py-2 rounded-md text-base font-medium">
                        Архив задач
                    </a>
                    <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-400' }} block px-4 py-2 rounded-md text-base font-medium">
                        Профиль
                    </a>
                    <a href="{{ route('teams.index') }}" class="{{ request()->routeIs('teams.*') ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-400' }} block px-4 py-2 rounded-md text-base font-medium">
                        Команда
                    </a>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-400' }} block px-4 py-2 rounded-md text-base font-medium">
                            Админ панель
                        </a>
                    @endif
                    <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-center text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-red-600 hover:text-white transition px-4 py-2 rounded-md text-base font-medium">
                                Выйти
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="block text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-400 px-4 py-2 rounded-md text-base font-medium mb-2">
                        Войти
                    </a>
                    <a href="{{ route('register') }}" class="block text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-md text-base font-medium">
                        Регистрация
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<script>
    // Управление мобильным меню
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuButton.addEventListener('click', function() {
            if (mobileMenu.classList.contains('hidden')) {
                // Показываем меню с анимацией
                mobileMenu.classList.remove('hidden');
                mobileMenu.classList.add('animate-fade-in-down');
                
                // Удаляем класс анимации после завершения
                setTimeout(() => {
                    mobileMenu.classList.remove('animate-fade-in-down');
                }, 300);
            } else {
                // Анимация закрытия
                mobileMenu.classList.add('animate-fade-out-up');
                
                // После завершения анимации скрываем меню
                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                    mobileMenu.classList.remove('animate-fade-out-up');
                }, 300);
            }
        });
    });
</script>

<style>
    /* Анимации для мобильного меню */
    .animate-fade-in-down {
        animation: fadeInDown 0.3s ease-out forwards;
    }
    
    .animate-fade-out-up {
        animation: fadeOutUp 0.3s ease-out forwards;
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeOutUp {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-10px);
        }
    }
</style>
