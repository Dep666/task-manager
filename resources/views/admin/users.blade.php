@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Пользователи</h1>
    @if(session('success'))
        <div class="bg-green-500 text-white p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <table class="min-w-full table-auto border-collapse bg-white shadow-md rounded-lg overflow-hidden">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2 text-left font-medium text-gray-700">ID</th>
                <th class="px-4 py-2 text-left font-medium text-gray-700">Имя</th>
                <th class="px-4 py-2 text-left font-medium text-gray-700">Email</th>
                <th class="px-4 py-2 text-left font-medium text-gray-700">Роль</th>
                <th class="px-4 py-2 text-left font-medium text-gray-700">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-2 text-gray-800">{{ $user->id }}</td>
                <td class="px-4 py-2 text-gray-800">{{ $user->name }}</td>
                <td class="px-4 py-2 text-gray-800">{{ $user->email }}</td>
                <td class="px-4 py-2 text-gray-800">{{ $user->role }}</td>
                <td class="px-4 py-2 text-gray-800">
                    <a href="{{ route('admin.editUser', $user->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Редактировать</a> 
                    <a href="{{ route('admin.deleteUser', $user->id) }}" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600" onclick="return confirm('Вы уверены?')">Удалить</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
