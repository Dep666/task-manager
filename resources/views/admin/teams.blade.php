@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4 text-gray-900">Команды</h1>
    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">ID</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Название</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Создатель</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($teams as $team)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2 text-sm text-gray-900">{{ $team->id }}</td>
                    <td class="px-4 py-2 text-sm text-gray-900">{{ $team->name }}</td>
                    <td class="px-4 py-2 text-sm text-gray-900">{{ $team->owner->name ?? 'Неизвестно' }}</td>
                    <td class="px-4 py-2 text-sm">
                        <a href="{{ route('admin.editTeam', $team->id) }}" class="bg-yellow-500 text-white py-1 px-4 rounded hover:bg-yellow-600">Редактировать</a>
                        <a href="{{ route('admin.deleteTeam', $team->id) }}" class="bg-red-500 text-white py-1 px-4 rounded hover:bg-red-600 ml-2" onclick="return confirm('Вы уверены?')">Удалить</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
