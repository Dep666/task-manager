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
        
        // Получаем ID статусов "выполнено" - эти задачи не будут отображаться на главной странице
        $completedStatusIds = TaskStatus::where('name', 'like', '%выполнен%')
            ->orWhere('name', 'like', '%завершен%')
            ->pluck('id');
        
        // Получаем все статусы задач для фильтрации
        $statuses = TaskStatus::all();
        
        // Начинаем строить запрос для задач
        $query = Task::with('team') // Подгружаем информацию о командах
            ->whereNotIn('status_id', $completedStatusIds); // Исключаем выполненные задачи
        
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
            $query->where(function($q) use ($userTeams) {
                $q->where('user_id', Auth::id()) // Личные задачи
                  ->orWhereIn('team_id', $userTeams); // Задачи команд
            });
        }

        // Фильтрация по статусу задачи
        if ($request->has('status') && $request->status != '') {
            $query->where('status_id', $request->status);
        }

        // Фильтрация по дедлайну (сортировка по сроку выполнения)
        if ($request->has('deadline_sort') && $request->deadline_sort != '') {
            if ($request->deadline_sort == 'asc') {
                $query->orderBy('deadline', 'asc'); // Ближайший дедлайн
            } elseif ($request->deadline_sort == 'desc') {
                $query->orderBy('deadline', 'desc'); // Самый дальний дедлайн
            }
        }

        // Пагинация задач (5 задач на страницу)
        $tasks = $query->paginate(5)->withQueryString();
        
        // Если запрос отправлен через AJAX, возвращаем только часть страницы с задачами
        if ($request->ajax()) {
            return view('tasks.index', compact('tasks', 'statuses'))
                ->renderSections()['content'];
        }
        
        return view('tasks.index', compact('tasks', 'statuses'));
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
            'progress' => 'nullable|integer|min:0|max:100',
            'assigned_user_id' => 'nullable|exists:users,id',
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
            'progress' => $request->input('progress', 0),
            'assigned_user_id' => $request->input('assigned_user_id'),
        ]);
        
        // Формируем сообщение для Telegram
        $message = "📌 *Новая задача добавлена!* \n\n" .
                   "📂 *Название*: {$task->title} \n" .
                   "🏢 *Команда*: " . ($task->team ? $task->team->name : 'Без команды') . " \n" .
                   "⏳ *Дедлайн*: " . \Carbon\Carbon::parse($task->deadline)->format('d.m.Y H:i') . " \n" .
                   "📝 *Описание*: {$task->description} \n";
        
        // Добавляем информацию о назначенном исполнителе, если он есть
        if ($task->assigned_user_id) {
            $assignedUser = \App\Models\User::find($task->assigned_user_id);
            if ($assignedUser) {
                $message .= "👤 *Исполнитель*: {$assignedUser->name} \n";
            }
        }

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
        
        // Предварительно загружаем участников каждой команды
        $teamMembers = [];
        foreach ($teams as $team) {
            // Получаем всех пользователей команды
            $members = $team->users()
                ->select('users.id', 'users.name')
                ->get()
                ->toArray();
            
            // Сохраняем в ассоциативный массив, где ключ - это ID команды
            $teamMembers[$team->id] = $members;
        }
        
        // Передаем данные в представление
        return view('tasks.create', compact('teams', 'teamMembers'));
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
            'feedback' => 'nullable|string',
        ]);
        
        $user = Auth::user();
        $newStatus = TaskStatus::findOrFail($request->status_id);
        $currentStatus = $task->status;
        
        \Log::debug('Attempting to change status', [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'current_status' => $currentStatus ? $currentStatus->slug : 'none',
            'new_status' => $newStatus->slug,
            'is_assigned_user' => $task->assigned_user_id === $user->id,
        ]);
        
        // Проверка прав доступа к изменению статуса
        if (!$task->canChangeStatus($user)) {
            \Log::debug('Cannot change status: permission denied');
            return redirect()->back()->with('error', 'У вас нет прав на изменение статуса этой задачи.');
        }
        
        // Проверка соответствия типа статуса (personal/team) типу задачи
        if (($task->isPersonal() && !$newStatus->isPersonalStatus()) || 
            ($task->isTeam() && !$newStatus->isTeamStatus())) {
            \Log::debug('Cannot change status: status type mismatch');
            return redirect()->back()->with('error', 'Выбранный статус не соответствует типу задачи.');
        }
        
        // Дополнительная проверка для командных задач
        if ($task->isTeam()) {
            $isTeamMember = $task->team && $task->team->users()->where('users.id', $user->id)->exists();
            $isTeamOwner = $task->team && $task->team->owner_id === $user->id;
            $isAssignedUser = $task->assigned_user_id === $user->id;
            
            \Log::debug('Team task checks', [
                'isTeamMember' => $isTeamMember,
                'isTeamOwner' => $isTeamOwner,
                'isAssignedUser' => $isAssignedUser,
            ]);
            
            // Ограничения для участников команды (не владельцев)
            if ($isTeamMember && !$isTeamOwner) {
                // Специальный случай: Если текущий статус "На доработку" и исполнитель пытается отправить задачу на проверку
                if ($currentStatus && $currentStatus->slug === 'team_revision' && $newStatus->slug === 'team_reviewing') {
                    // Разрешаем только если пользователь - исполнитель задачи
                    if ($isAssignedUser) {
                        // Просто продолжаем выполнение, разрешая смену статуса
                        \Log::debug('Allowing assigned user to send task back to review from revision');
                    } else {
                        \Log::debug('Cannot change status: user is not assigned to this task');
                        return redirect()->back()->with('error', 'Только назначенный исполнитель может отправить задачу на проверку.');
                    }
                }
                // Иначе стандартная проверка: участники команды могут устанавливать только статусы "В работе" и "Отправить на проверку"
                else if (!in_array($newStatus->slug, ['team_in_progress', 'team_reviewing'])) {
                    \Log::debug('Cannot change status: restricted status for team member');
                    return redirect()->back()->with('error', 'Вы можете устанавливать только статусы "В работе" и "Отправить на проверку".');
                }
                
                // Участники команды не могут добавлять НОВЫЕ комментарии
                if ($request->has('feedback') && !empty($request->feedback) && $task->feedback !== $request->feedback) {
                    \Log::debug('Cannot change status: team members cannot leave comments');
                    return redirect()->back()->with('error', 'Только владелец команды может оставлять комментарии к задачам.');
                }
            }
            
            // Дополнительные проверки для владельца команды
            if ($currentStatus) {
                // Обработка перевода задачи на доработку или возврата в работу
                if ($currentStatus->slug === 'team_reviewing' && 
                    ($newStatus->slug === 'team_in_progress' || $newStatus->slug === 'team_revision')) {
                    
                    // Только владелец команды может отправить задачу на доработку
                    if (!$isTeamOwner) {
                        \Log::debug('Cannot change status: only team owner can send task to revision');
                        return redirect()->back()->with('error', 'Только владелец команды может отправить задачу на доработку.');
                    }
                    
                    // Требуем указать комментарий при отправке на доработку
                    if (empty($request->feedback)) {
                        \Log::debug('Cannot change status: feedback required for revision');
                        return redirect()->back()->with('error', 'При отправке задачи на доработку необходимо указать комментарий.');
                    }
                }
                
                // Проверка для изменения статуса с "Отправить на проверку" на "Выполнено"
                if ($currentStatus->slug === 'team_reviewing' && $newStatus->slug === 'team_completed') {
                    // Только владелец команды может одобрить выполнение
                    if (!$isTeamOwner) {
                        \Log::debug('Cannot change status: only team owner can mark task as completed');
                        return redirect()->back()->with('error', 'Только владелец команды может подтвердить выполнение задачи.');
                    }
                }
            }
        }
        
        // Обновляем статус задачи
        $updateData = [
            'status_id' => $request->status_id,
        ];
        
        // Добавляем комментарий, если он был предоставлен И изменился
        if ($request->has('feedback') && !empty($request->feedback)) {
            // Если комментарий не изменился, просто оставляем его как есть
            if ($task->feedback === $request->feedback) {
                \Log::debug('Feedback unchanged, preserving existing comment');
            } else {
                // Проверяем права на добавление комментария (для командных задач - только владелец команды)
                if ($task->isTeam() && $task->team && $task->team->owner_id !== $user->id) {
                    \Log::debug('Cannot change status: only team owner can add comments');
                    return redirect()->back()->with('error', 'Только владелец команды может оставлять комментарии к задачам.');
                }
                
                // Комментарий изменился, обновляем его
                $updateData['feedback'] = $request->feedback;
            }
        }
        
        \Log::debug('Updating task', $updateData);
        $task->update($updateData);
        
        // Формируем сообщение для Telegram при изменении статуса
        $statusName = $newStatus->name;
        $message = "🔄 *Статус задачи изменен!* \n\n" .
                   "📂 *Задача*: {$task->title} \n" .
                   "🏢 *Команда*: " . ($task->team ? $task->team->name : 'Личная задача') . " \n" .
                   "📊 *Новый статус*: {$statusName} \n";
        
        // Добавляем информацию о комментарии, если он есть
        if (!empty($task->feedback)) {
            $message .= "💬 *Комментарий*: {$task->feedback} \n";
        }
        
        // Отправка уведомления через TelegramService
        $this->telegramService->sendMessage($message);
        
        return redirect()->route('tasks.index')->with('success', 'Статус задачи успешно обновлен!');
    }
    
    // Метод для отображения доступных статусов задачи
    public function showChangeStatusForm(Task $task)
    {
        $user = Auth::user();
        
        // Проверяем, не является ли задача выполненной
        $completedStatusIds = TaskStatus::where('name', 'like', '%выполнен%')
            ->orWhere('name', 'like', '%завершен%')
            ->pluck('id');
        
        if (in_array($task->status_id, $completedStatusIds->toArray())) {
            return redirect()->back()->with('error', 'Нельзя изменить статус выполненной задачи.');
        }
        
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
            $isTeamOwner = $task->team && $task->team->owner_id === $user->id;
            $isAssignedUser = $task->assigned_user_id === $user->id;
            $isTeamMember = $task->team && $task->team->users()->where('users.id', $user->id)->exists();
            
            // Статусы для владельца команды при проверке задачи
            if ($currentStatus && $currentStatus->slug === 'team_reviewing' && $isTeamOwner) {
                // Владелец может: выполнить задачу или отправить на доработку (только 2 статуса)
                $statuses = TaskStatus::where('type', 'team')
                    ->whereIn('slug', ['team_completed', 'team_revision'])
                    ->get();
            }
            // Статусы для исполнителя, когда задача "На доработке" или "В работе"
            elseif ($currentStatus && ($currentStatus->slug === 'team_revision' || $currentStatus->slug === 'team_in_progress') 
                    && ($isAssignedUser || ($isTeamMember && is_null($task->assigned_user_id)))) {
                $statuses = TaskStatus::where('type', 'team')
                    ->whereIn('slug', ['team_in_progress', 'team_reviewing'])
                    ->get();
            }
            // Участники команды могут менять статусы ТОЛЬКО на "В работе" и "Отправить на проверку"
            else {
                // Проверяем, является ли пользователь участником команды
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
            'progress' => 'nullable|integer|min:0|max:100',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        // Определение статуса в зависимости от типа задачи если он изменился
        $statusId = $task->status_id;
        if (($request->team_id && !$task->team_id) || (!$request->team_id && $task->team_id)) {
            if ($request->team_id) {
                // Для командной задачи используем 'team_new'
                $statusId = TaskStatus::where('slug', 'team_new')->first()->id;
            } else {
                // Для личной задачи используем 'new'
                $statusId = TaskStatus::where('slug', 'new')->first()->id;
            }
        }

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'team_id' => $request->team_id,
            'status_id' => $statusId,
            'progress' => $request->progress ?? 0,
            'assigned_user_id' => $request->assigned_user_id,
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

    // Показать детали задачи
    public function show(Task $task)
    {
        // Проверяем доступ: пользователь должен быть создателем задачи, 
        // участником команды или владельцем команды
        $user = Auth::user();
        $canView = false;
        
        if ($task->user_id === $user->id) {
            // Пользователь - создатель задачи
            $canView = true;
        } elseif ($task->isTeam() && $task->team) {
            // Задача принадлежит команде, проверяем, является ли пользователь участником
            $canView = $task->team->users()->where('users.id', $user->id)->exists();
        }
        
        if (!$canView) {
            return redirect()->route('tasks.index')
                ->with('error', 'У вас нет доступа к просмотру этой задачи.');
        }
        
        return view('tasks.show', compact('task'));
    }

    // Метод для отображения архива выполненных задач
    public function archive(Request $request)
    {
        // Получаем ID всех команд, к которым принадлежит текущий пользователь
        $userTeams = Auth::user()->teams->pluck('id');
        
        // Получаем ID статусов "выполнено"
        $completedStatusIds = TaskStatus::where('name', 'like', '%выполнен%')
            ->orWhere('name', 'like', '%завершен%')
            ->pluck('id');
        
        // Получаем все статусы задач для отображения в фильтре
        $statuses = TaskStatus::all();
        
        // Начинаем строить запрос для выполненных задач
        $query = Task::with(['team', 'status', 'user', 'assignedUser'])
            ->whereIn('status_id', $completedStatusIds);
        
        // Фильтрация по типу задачи (личные или командные)
        if ($request->has('task_type') && $request->task_type != '') {
            if ($request->task_type == 'team') {
                // Задачи, связанные с командой
                $query->whereIn('team_id', $userTeams);
            } elseif ($request->task_type == 'personal') {
                // Личные задачи пользователя
                $query->where('user_id', Auth::id())
                      ->whereNull('team_id');
            }
        } else {
            // Если не выбран фильтр, показываем задачи как для пользователя, так и для команд
            $query->where(function($q) use ($userTeams) {
                $q->where('user_id', Auth::id())
                  ->orWhereIn('team_id', $userTeams);
            });
        }
        
        // Фильтрация по дате завершения
        if ($request->has('completed_sort') && $request->completed_sort != '') {
            if ($request->completed_sort == 'newest') {
                $query->orderBy('updated_at', 'desc'); // Сначала недавно завершённые
            } elseif ($request->completed_sort == 'oldest') {
                $query->orderBy('updated_at', 'asc'); // Сначала давно завершённые
            }
        } else {
            // По умолчанию показываем сначала недавно завершённые задачи
            $query->orderBy('updated_at', 'desc');
        }
        
        // Пагинация задач (10 задач на страницу)
        $tasks = $query->paginate(10)->withQueryString();
        
        // Если запрос отправлен через AJAX, возвращаем только часть страницы с задачами
        if ($request->ajax()) {
            return view('tasks.archive', compact('tasks', 'statuses'))
                ->renderSections()['content'];
        }
        
        return view('tasks.archive', compact('tasks', 'statuses'));
    }
}
