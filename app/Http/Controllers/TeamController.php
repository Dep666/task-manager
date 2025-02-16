<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\Controller;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $query = Team::query();

        // Фильтрация по имени команды
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // Получаем все команды с фильтром
        $teams = $query->get();

        return view('teams.index', compact('teams'));
    }
    public function store(Request $request)
    {
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    // Создание команды с владельцем (текущий авторизованный пользователь)
    $team = Team::create([
        'name' => $request->input('name'),
        'owner_id' => auth()->id(), // ID текущего пользователя
    ]);

    // Добавляем владельца в команду
    $team->users()->attach(auth()->id()); // добавляем пользователя в команду

    return redirect()->route('teams.index')->with('status', 'Команда успешно создана!');
    }
    public function create()
    {
        return view('teams.create');
    }

public function edit(Team $team)
{
    if (Gate::denies('update', $team)) {
        return redirect()->route('teams.index')->with('error', 'У вас нет прав для редактирования этой команды');
    }

    return view('teams.edit', compact('team'));
}


    // Метод для обновления команды
    public function update(Request $request, Team $team)
    {
        // Валидация данных
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Обновление названия команды
        $team->name = $request->input('name');
        $team->save();

        // Перенаправление обратно на страницу команд
        return redirect()->route('teams.index')->with('status', 'Команда успешно обновлена!');
    }
    public function destroy(Team $team)
    {
    if (Gate::denies('delete', $team)) {
        abort(403);
    }

    // Логика удаления команды
    $team->delete();

    return redirect()->route('teams.index')->with('success', 'Команда успешно удалена!');
    }




    public function addUserForm(Team $team)
{
    return view('teams.addUser', compact('team'));
}




public function addUser(Request $request, Team $team)
{
    // Валидация введенных данных
    $request->validate([
        'user_identifier' => 'required|string', // Проверяем, что поле заполнено
    ]);

    // Попробуем найти пользователя по email или ID
    $user = User::where('email', $request->user_identifier)
                ->orWhere('id', $request->user_identifier)
                ->first();

    // Проверяем, найден ли пользователь
    if (!$user) {
        return back()->withErrors(['user_identifier' => 'Пользователь с таким email или ID не найден.']);
    }

    // Проверяем, не добавлен ли пользователь уже в эту команду
    if ($team->users->contains($user->id)) {
        return back()->withErrors(['user_identifier' => 'Этот пользователь уже добавлен в команду.']);
    }

    // Добавляем пользователя в команду
    $team->users()->attach($user);

    return redirect()->route('teams.index')->with('success', 'Пользователь успешно добавлен в команду.');
}

    public function tasks(Request $request)
    {
        // Получаем все команды, к которым принадлежит пользователь
        $userTeams = Auth::user()->teams->pluck('id');

        // Выбираем задачи, которые относятся к этим командам
        $tasks = \App\Models\Task::whereIn('team_id', $userTeams)
            ->with('team') // Загрузка информации о командах для задач
            ->paginate(10);

        return view('teams.tasks', compact('tasks'));
    }
    public function editUsers(Team $team)
{
    // Загружаем команду с её участниками
    $team->load('users');

    return view('teams.editUsers', compact('team'));
}

    // Метод для удаления участника из команды
    // Контроллер TeamController

    public function removeUser(Team $team, User $user)
    {
        // Проверка, что текущий пользователь — владелец команды
        if ($team->owner_id != auth()->user()->id) {
            return redirect()->route('teams.index')->with('error', 'Вы не можете удалить этого участника.');
        }
    
        // Удаление пользователя из команды
        $team->users()->detach($user);
    
        return redirect()->route('teams.editUsers', $team->id)->with('success', 'Участник удален из команды.');
    }
    

}

