<x-guest-layout>
    <x-slot name="heading">
        {{ __('Восстановление пароля') }}
    </x-slot>
    
    <div class="mb-4 text-sm text-gray-300">
        {{ __('Забыли пароль? Введите ваш email, и мы отправим вам ссылку для сброса пароля.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                    </svg>
                </div>
                <x-text-input id="email" class="pl-10" type="email" name="email" :value="old('email')" required autofocus placeholder="your-email@example.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-3 mt-8">
            <x-primary-button>
                {{ __('ОТПРАВИТЬ ССЫЛКУ') }}
            </x-primary-button>
            
            <div class="text-center text-sm text-gray-400 mt-3">
                <a class="auth-link font-medium" href="{{ route('login') }}">
                    {{ __('Вернуться к входу') }}
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>
