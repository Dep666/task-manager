<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Обновление пароля') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Для обеспечения безопасности вашей учетной записи используйте длинный, случайный пароль.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="current_password" :value="__('Текущий пароль')" class="text-gray-900 dark:text-gray-100" />
            <x-text-input id="current_password" name="current_password" type="password" 
                class="w-full pr-3 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white"
                autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-red-500 dark:text-red-400" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Новый пароль')" class="text-gray-900 dark:text-gray-100" />
            <x-text-input id="password" name="password" type="password" 
                class="w-full pr-3 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-red-500 dark:text-red-400" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Подтверждение пароля')" class="text-gray-900 dark:text-gray-100" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" 
                class="w-full pr-3 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-red-500 dark:text-red-400" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-indigo-600 dark:bg-indigo-500 text-white hover:bg-indigo-700 dark:hover:bg-indigo-400 transition duration-300">
                {{ __('Сохранить') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Сохранено.') }}</p>
            @endif
        </div>
    </form>
</section>
