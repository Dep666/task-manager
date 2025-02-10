@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-semibold text-gray-800 dark:text-white mb-6">Создание команды</h1>

        <form method="POST" action="{{ route('teams.store') }}">
            @csrf

            <div class="mb-4">
                <x-input-label for="name" :value="__('Название команды')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-primary-button>{{ __('Создать команду') }}</x-primary-button>
            </div>
        </form>
    </div>
@endsection
