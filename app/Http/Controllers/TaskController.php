<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TelegramService;

class TaskController extends Controller
{
    // Показать все задачи
    public function index(Request $request)
{
    // Получаем ID всех команд, к которым принадлежит текущий пользователь
    $userTeams = Auth::user()->teams->pluck('id');

    // Начинаем строить запрос для задач
    $query = Task::with('team'); // Подгружаем информацию о командах
    
    // Фильтрация по типу задачи (личные или командные)
    if ($request->has('task_type') && $request->task_type != '') {
        if ($request->task_type == 'team') {
            // Задачи, связанные с командой
            $query->whereIn('team_id', $userTeams); // Задачи только тех команд, к которым принадлежит пользователь
        } elseif ($request->task_type == 'personal') {
            // Личные задачи пользователя
            $query->where('user_id', Auth::id()) // Задачи только для текущего пользователя
                  ->whereNull('team_id'); // Задачи без привязки к команде
        }
    } else {
        // Если не выбран фильтр, показываем задачи как для пользователя, так и для команд
        $query->where('user_id', Auth::id()) // Личные задачи
              ->orWhereIn('team_id', $userTeams); // Задачи команд
    }

    // Фильтрация по дедлайну (сортировка по сроку выполнения)
    if ($request->has('deadline_sort') && $request->deadline_sort != '') {
        if ($request->deadline_sort == 'soonest') {
            $query->orderBy('deadline', 'asc'); // Ближайший дедлайн
        } elseif ($request->deadline_sort == 'latest') {
            $query->orderBy('deadline', 'desc'); // Самый дальний дедлайн
        }
    }

    // Получаем задачи с учетом фильтрации
    $tasks = $query->get();
    
    return view('tasks.index', compact('tasks'));
    }


    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function store(Request $request)
{
    // Валидация входных данных
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'deadline' => 'required|date',
        'team_id' => 'nullable|exists:teams,id',
    ]);

    


    // Создаем задачу
    $task = Task::create([
        'title' => $request->input('title'),
        'description' => $request->input('description'),
        'deadline' => $request->input('deadline'),
        'team_id' => $request->input('team_id'),
        
        'user_id' => Auth::id(),
    ]);

    // Формируем сообщение для Telegram
    $message = "📌 *Новая задача добавлена!* \n\n" .
               "📂 *Название*: {$task->title} \n" .
               "🏢 *Команда*: " . ($task->team ? $task->team->name : 'Без команды') . " \n" .
               "⏳ *Дедлайн*: " . \Carbon\Carbon::parse($task->deadline)->format('d.m.Y H:i') . " \n" .
               "📝 *Описание*: {$task->description} \n" ;

    // Отправка уведомления через TelegramService
    $this->telegramService->sendMessage($message);

    // Перенаправление с сообщением об успехе
    return redirect()->route('tasks.index')->with('success', 'Задача добавлена');
}

    


    // Создать новую задачу
    public function create()
    {
        // Получаем команды текущего пользователя
        $teams = Team::where('owner_id', Auth::id())->get();
        

    
        // Передаем данные в представление
        return view('tasks.create', compact('teams'));
    }
    



    
    

    // Редактировать задачу
    public function edit(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return redirect()->route('tasks.index')->with('error', 'Нет доступа к редактированию этой задачи.');
        }

        $teams = Team::where('owner_id', Auth::id())->get(); // Получаем команды текущего пользователя
        return view('tasks.edit', compact('task', 'teams'));
    }

    // Обновить задачу
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'team_id' => $request->team_id,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Задача успешно обновлена!');
    }

    // Удалить задачу
    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return redirect()->route('tasks.index')->with('error', 'Нет доступа к удалению этой задачи.');
        }

        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Задача успешно удалена!');
    }
    

   

    

    
    
}


