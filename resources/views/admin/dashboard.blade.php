@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-center mb-6">Панель администратора</h1>
    
    <div class="bg-white shadow-md rounded-lg p-6">
        <ul class="space-y-4">
            <li>
                <a href="{{ route('admin.users') }}" class="text-blue-600 hover:text-blue-800 text-lg font-semibold">Управление пользователями</a>
            </li>
            <li>
                <a href="{{ route('admin.teams') }}" class="text-blue-600 hover:text-blue-800 text-lg font-semibold">Управление командами</a>
            </li>
        </ul>
    </div>
</div>
@endsection
