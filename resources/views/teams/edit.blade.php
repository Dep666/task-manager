@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Редактировать команду: {{ $team->name }}</h2>

        <!-- Форма редактирования команды -->
        <form action="{{ route('teams.update', $team) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Поле для имени команды -->
            <div>
                <x-input-label for="name" :value="__('Название команды')" />
                <x-text-input 
                    id="name" 
                    name="name" 
                    type="text" 
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                    value="{{ old('name', $team->name) }}" 
                    required 
                    autofocus 
                />
                @error('name')
                    <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Кнопка для сохранения изменений -->
            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Сохранить изменения') }}</x-primary-button>

                <a 
                    href="{{ route('teams.index') }}" 
                    class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200"
                >
                    {{ __('Отмена') }}
                </a>
            </div>
        </form>

        <!-- Форма для удаления команды -->
        <form action="{{ route('teams.destroy', $team) }}" method="POST" class="mt-6">
            @csrf
            @method('DELETE')

            <button 
                type="submit" 
                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700"
                onclick="return confirm('Вы уверены, что хотите удалить эту команду? Это действие необратимо.')"
            >
                Удалить команду
            </button>
        </form>
    </div>
@endsection
