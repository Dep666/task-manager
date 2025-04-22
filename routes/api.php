<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Team;
use App\Models\User;
use App\Http\Controllers\TeamController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API маршрут для получения участников команды, используем контроллер вместо анонимной функции
Route::middleware('auth')->get('/teams/{team}/members', [TeamController::class, 'getTeamMembers']); 