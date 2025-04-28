<section>
    <header>
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
            {{ __('Удалить аккаунт') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('После удаления вашего аккаунта все его ресурсы и данные будут безвозвратно удалены. Перед удалением аккаунта, пожалуйста, скачайте все данные или информацию, которую вы хотите сохранить.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.destroy') }}" class="mt-6">
        @csrf
        @method('delete')

        <div class="mt-6">
            <button type="submit" class="w-full py-3 px-4 bg-red-600 dark:bg-red-500 text-white rounded-lg shadow-sm hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150 uppercase font-semibold">
                УДАЛИТЬ АККАУНТ
            </button>
        </div>
    </form>
</section>
