@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6 text-white-800">Редактирование команды</h1>
    <form action="{{ route('admin.updateTeam', $team->id) }}" method="POST">
        @csrf
        @method('POST')

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-white-800">Название команды</label>
            <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-800" id="name" name="name" value="{{ $team->name }}" required>
        </div>

        <div class="mb-4">
            <label for="owner_id" class="block text-sm font-medium text-white-800">Создатель</label>
            <select class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-800" id="owner_id" name="owner_id" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $user->id == $team->owner_id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">Обновить</button>
        </div>
    </form>
</div>
@endsection
