<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    // Показать всех пользователей
    public function index()
    {
        return view('admin.dashboard'); // Страница с панелью администратора
    }

    // Показать всех пользователей
    public function users()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    // Показать форму редактирования пользователя
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit_user', compact('user'));
    }

    // Обновить данные пользователя
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);
        return redirect()->route('admin.users')->with('success', 'Пользователь обновлен');
    }

    // Удалить пользователя
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'Пользователь удален');
    }
    public function teams()
    {
        $teams = Team::all();
        return view('admin.teams', compact('teams')); // Передаем команды в представление
    }

    // Показать форму для редактирования команды
    public function editTeam($id)
    {
        $team = Team::findOrFail($id);
        $users = User::all(); // Список пользователей для выбора создателя
        return view('admin.edit_team', compact('team', 'users')); // Страница редактирования команды
    }

    // Обновить команду
    public function updateTeam(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'owner_id' => 'required|exists:users,id', // Убедимся, что создатель существует
        ]);

        $team->update([
            'name' => $request->name,
            'owner_id' => $request->owner_id, // Обновляем владельца
        ]);

        return redirect()->route('admin.teams')->with('success', 'Команда обновлена');
    }

    // Удалить команду
    public function deleteTeam($id)
    {
        $team = Team::findOrFail($id);
        $team->delete();

        return redirect()->route('admin.teams')->with('success', 'Команда удалена');
    }
}