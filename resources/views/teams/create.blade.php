@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl sm:text-3xl font-semibold text-gray-800 dark:text-white mb-6">Создание команды</h1>

        <form method="POST" action="{{ route('teams.store') }}" class="space-y-6">
            @csrf

            <div class="mb-4">
                <x-input-label for="name" :value="__('Название команды')" class="text-gray-900 dark:text-gray-300" />
                <x-text-input 
                    id="name" 
                    name="name" 
                    type="text" 
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:bg-gray-700 dark:text-white" 
                    required 
                    autofocus 
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="flex flex-wrap gap-3">
            <a 
                    href="{{ route('teams.index') }}" 
                    class="flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-sm font-medium border border-gray-600"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Отмена
                </a>
                <button 
                    type="submit" 
                    class="flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-300 text-sm font-medium border border-gray-600"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ __('Создать команду') }}
                </button>
                
                
            </div>
        </form>
    </div>
@endsection
