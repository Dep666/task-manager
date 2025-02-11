<nav class="bg-blue-600 p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
        
        <div class="space-x-4">
            @auth
                <a href="{{ route('welcome') }}" class="text-white">Главная</a>
                <a href="{{ route('tasks.index') }}" class="text-white">Задачи</a>
                <a href="{{ route('profile.edit') }}" class="text-white">Профиль</a>
                <a href="{{ route('teams.index') }}" class="text-white">Команда</a>
            @else
                <a href="{{ route('login') }}" class="text-white">Войти</a>
                <a href="{{ route('register') }}" class="text-white">Регистрация</a>
            @endauth
            @auth
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="text-white">Админ панель</a>
                @endif
            @endauth
        </div>

        <div class="ml-auto"> <!-- Добавляем класс ml-auto для выравнивания по правому краю -->
            @auth
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-white">Выйти</button>
                </form>
            @endauth
            

        </div>
    </div>
</nav>
