<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\TelegramController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');



Route::get('/tasks', [TaskController::class, 'index'])->name('task.index');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
});
Route::middleware(['auth'])->group(function () {
    Route::resource('tasks', TaskController::class);
});

require __DIR__.'/auth.php';


Route::get('/teams', [TeamController::class, 'index'])->name('teams.index')->middleware('auth');
Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');

Route::middleware('auth')->group(function () {
    // Страница редактирования команды
    Route::get('teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit');

    // Обновление команды
    Route::put('teams/{team}', [TeamController::class, 'update'])->name('teams.update');
});
Route::resource('teams', TeamController::class);
Route::get('/teams/{team}/add-user', [TeamController::class, 'addUserForm'])->name('teams.addUser');
Route::post('/teams/{team}/add-user', [TeamController::class, 'addUser'])->name('teams.addUserPost');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
// Удаление участника из команды
Route::delete('/teams/{team}/users/{user}', [TeamController::class, 'removeUser'])->name('teams.removeUser');
// Маршрут для редактирования участников
Route::get('/teams/{team}/edit-users', [TeamController::class, 'editUsers'])->name('teams.editUsers');

// Маршрут для редактирования команды
Route::get('/teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit');


Route::middleware(['auth', 'admin'])->group(function () {
    // Панель администратора
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // CRUD для пользователей
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/users/edit/{id}', [AdminController::class, 'editUser'])->name('admin.editUser');
    Route::post('/admin/users/update/{id}', [AdminController::class, 'updateUser'])->name('admin.updateUser');
    Route::get('/admin/users/delete/{id}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');

    // CRUD для команд
    Route::get('/admin/teams', [AdminController::class, 'teams'])->name('admin.teams'); // Отображение списка команд
    Route::get('/admin/teams/edit/{id}', [AdminController::class, 'editTeam'])->name('admin.editTeam'); // Страница редактирования команды
    Route::post('/admin/teams/update/{id}', [AdminController::class, 'updateTeam'])->name('admin.updateTeam'); // Обновление команды
    Route::get('/admin/teams/delete/{id}', [AdminController::class, 'deleteTeam'])->name('admin.deleteTeam'); // Удаление команды
});

Route::get('/profile/bind-telegram', [TelegramController::class, 'bind'])->name('telegram.bind');
Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);
