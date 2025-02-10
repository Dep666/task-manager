@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6 text-white-800">Редактировать пользователя</h1>
    <form action="{{ route('admin.updateUser', $user->id) }}" method="POST">
        @csrf
        @method('POST')
        
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-white-800">Имя</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-800" required>
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-white-800">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-800" required>
        </div>

        <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-white-800">Роль</label>
            <select name="role" id="role" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-800">
                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>Пользователь</option>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Администратор</option>
            </select>
        </div>

        <div class="mb-4">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Обновить</button>
        </div>
    </form>
</div>
@endsection
