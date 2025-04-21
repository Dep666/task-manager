<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use App\Models\TaskStatus;
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

        // Пагинация задач (10 задач на страницу)
        $tasks = $query->paginate(5);
        
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
        
        // Определение статуса в зависимости от типа задачи
        $statusId = null;
        if ($request->team_id) {
            // Для командной задачи используем 'team_new'
            $statusId = TaskStatus::where('slug', 'team_new')->first()->id;
        } else {
            // Для личной задачи используем 'new'
            $statusId = TaskStatus::where('slug', 'new')->first()->id;
        }
        
        // Создаем задачу
        $task = Task::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'deadline' => $request->input('deadline'),
            'team_id' => $request->input('team_id'),
            'user_id' => Auth::id(),
            'status_id' => $statusId,
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

    // Метод для изменения статуса задачи
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status_id' => 'required|exists:task_statuses,id',
        ]);
        
        $user = Auth::user();
        $newStatus = TaskStatus::findOrFail($request->status_id);
        
        // Проверка прав доступа к изменению статуса
        if (!$task->canChangeStatus($user)) {
            return redirect()->back()->with('error', 'У вас нет прав на изменение статуса этой задачи.');
        }
        
        // Проверка соответствия типа статуса (personal/team) типу задачи
        if (($task->isPersonal() && !$newStatus->isPersonalStatus()) || 
            ($task->isTeam() && !$newStatus->isTeamStatus())) {
            return redirect()->back()->with('error', 'Выбранный статус не соответствует типу задачи.');
        }
        
        // Дополнительная проверка для командных задач
        if ($task->isTeam()) {
            $currentStatus = $task->status;
            
            // Проверяем, является ли пользователь участником команды, но не владельцем
            $isTeamMember = $task->team && $task->team->users()->where('users.id', $user->id)->exists();
            $isTeamOwner = $task->team && $task->team->owner_id === $user->id;
            
            // Участник команды (но не владелец) может устанавливать только "В работе" и "Отправить на проверку"
            if ($isTeamMember && !$isTeamOwner) {
                if (!in_array($newStatus->slug, ['team_in_progress', 'team_reviewing'])) {
                    return redirect()->back()->with('error', 'Вы можете устанавливать только статусы "В работе" и "Отправить на проверку".');
                }
            }
            
            // Если текущий статус "В работе" и новый статус "Отправить на проверку"
            if ($currentStatus && $currentStatus->slug === 'team_in_progress' && $newStatus->slug === 'team_reviewing') {
                // Нет дополнительных проверок, любой участник команды может отправить на проверку
            }
            
            // Если текущий статус "Отправить на проверку" и новый статус "Выполнено"
            if ($currentStatus && $currentStatus->slug === 'team_reviewing' && $newStatus->slug === 'team_completed') {
                // Только владелец команды может одобрить выполнение
                if (!$isTeamOwner) {
                    return redirect()->back()->with('error', 'Только владелец команды может подтвердить выполнение задачи.');
                }
            }
        }
        
        // Обновляем статус задачи
        $task->update([
            'status_id' => $request->status_id,
        ]);
        
        // Формируем сообщение для Telegram, если статус изменился на "Отправить на проверку" или "Выполнено"
        if (in_array($newStatus->slug, ['team_reviewing', 'team_completed', 'completed'])) {
            $statusName = $newStatus->name;
            $message = "🔄 *Статус задачи изменен!* \n\n" .
                       "📂 *Задача*: {$task->title} \n" .
                       "🏢 *Команда*: " . ($task->team ? $task->team->name : 'Личная задача') . " \n" .
                       "📊 *Новый статус*: {$statusName} \n";
            
            // Отправка уведомления через TelegramService
            $this->telegramService->sendMessage($message);
        }
        
        return redirect()->route('tasks.index')->with('success', 'Статус задачи успешно обновлен!');
    }
    
    // Метод для отображения доступных статусов задачи
    public function showChangeStatusForm(Task $task)
    {
        $user = Auth::user();
        
        // Проверка прав доступа
        if (!$task->canChangeStatus($user)) {
            return redirect()->back()->with('error', 'У вас нет прав на изменение статуса этой задачи.');
        }
        
        // Получаем доступные статусы в зависимости от типа задачи
        $statuses = [];
        
        if ($task->isPersonal()) {
            // Для личных задач
            $statuses = TaskStatus::where('type', 'personal')->get();
        } else {
            // Для командных задач
            $currentStatus = $task->status;
            
            // Владелец команды может менять статус "Отправить на проверку" на "Выполнено"
            if ($currentStatus && $currentStatus->slug === 'team_reviewing' && $task->team && $task->team->owner_id === $user->id) {
                $statuses = TaskStatus::where('slug', 'team_completed')->get();
            }
            // Участники команды могут менять статусы ТОЛЬКО на "В работе" и "Отправить на проверку"
            else {
                // Проверяем, является ли пользователь участником команды
                $isTeamMember = $task->team && $task->team->users()->where('users.id', $user->id)->exists();
                
                if ($isTeamMember || $task->user_id === $user->id) {
                    $statuses = TaskStatus::where('type', 'team')
                        ->whereIn('slug', ['team_in_progress', 'team_reviewing'])
                        ->get();
                }
            }
        }
        
        return view('tasks.change-status', compact('task', 'statuses'));
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

        // Определение статуса в зависимости от типа задачи
        $statusId = null;
        if ($request->team_id) {
            // Для командной задачи используем 'team_new'
            $statusId = TaskStatus::where('slug', 'team_new')->first()->id;
        } else {
            // Для личной задачи используем 'new'
            $statusId = TaskStatus::where('slug', 'new')->first()->id;
        }

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'team_id' => $request->team_id,
            'status_id' => $statusId,
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
