<!-- resources/views/teams/addUser.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Добавить пользователя в команду: {{ $team->name }}</h2>

        <!-- Форма добавления пользователя -->
        <form method="POST" action="{{ route('teams.addUserPost', $team->id) }}">
            @csrf

            <!-- Поле для ввода email или ID пользователя -->
            <div class="mb-4">
                <label for="user_identifier" class="font-medium">Введите email или ID пользователя</label>
                <input 
                    type="text" 
                    name="user_identifier" 
                    id="user_identifier" 
                    class="form-control mt-1 block w-full"
                    placeholder="email или ID"
                    value="{{ old('user_identifier') }}"
                    required
                >

                @if($errors->has('user_identifier'))
                    <div class="text-red-500 text-sm mt-2">
                        {{ $errors->first('user_identifier') }}
                    </div>
                @endif
            </div>

            <!-- Кнопка для добавления -->
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg">Добавить пользователя</button>
        </form>
    </div>
@endsection
