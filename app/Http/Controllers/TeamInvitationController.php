<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\TeamInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamInvitationController extends Controller
{
    /**
     * Показать список приглашений пользователя
     */
    public function index()
    {
        $user = Auth::user();
        $pendingInvitations = $user->pendingTeamInvitations()
            ->with(['team', 'inviter'])
            ->get();

        return view('invitations.index', [
            'pendingInvitations' => $pendingInvitations
        ]);
    }

    /**
     * Отправить приглашение пользователю
     */
    public function invite(Request $request, Team $team)
    {
        // Проверка, что пользователь является владельцем команды
        if ($team->owner_id !== Auth::id()) {
            return back()->with('error', 'Только владелец команды может отправлять приглашения.');
        }

        // Валидация данных
        $request->validate([
            'user_identifier' => 'required|string',
        ]);

        // Поиск пользователя по identifier (email, ID или user_code)
        $user = User::where('email', $request->user_identifier)
            ->orWhere('id', $request->user_identifier)
            ->orWhere('user_code', $request->user_identifier)
            ->first();

        if (!$user) {
            return back()->withErrors(['user_identifier' => 'Пользователь с таким email, ID или кодом не найден.']);
        }

        // Проверка, не является ли пользователь уже членом команды
        if ($team->users->contains($user->id)) {
            return back()->withErrors(['user_identifier' => 'Этот пользователь уже является членом команды.']);
        }

        // Проверяем существующее приглашение
        $existingInvitation = TeamInvitation::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingInvitation) {
            // Если приглашение отклонено, обновляем его статус на pending
            if ($existingInvitation->status === 'declined') {
                $existingInvitation->update([
                    'status' => 'pending',
                    'invited_by' => Auth::id(),
                    'declined_at' => null
                ]);
                return redirect()->route('teams.editUsers', $team->id)
                    ->with('success', 'Приглашение повторно отправлено пользователю.');
            }
            
            // Если приглашение в статусе pending, возвращаем ошибку
            if ($existingInvitation->status === 'pending') {
                return back()->withErrors(['user_identifier' => 'Этому пользователю уже отправлено приглашение.']);
            }
        }

        // Создание нового приглашения
        TeamInvitation::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'invited_by' => Auth::id(),
            'status' => 'pending'
        ]);

        return redirect()->route('teams.editUsers', $team->id)
            ->with('success', 'Приглашение отправлено пользователю.');
    }

    /**
     * Принять приглашение
     */
    public function accept(TeamInvitation $invitation)
    {
        // Проверка, что приглашение относится к текущему пользователю
        if ($invitation->user_id !== Auth::id()) {
            return redirect()->route('invitations.index')
                ->with('error', 'У вас нет доступа к этому приглашению.');
        }

        // Проверка, что приглашение еще в статусе ожидания
        if (!$invitation->isPending()) {
            return redirect()->route('invitations.index')
                ->with('error', 'Это приглашение уже не активно.');
        }

        // Принятие приглашения
        $invitation->accept();

        return redirect()->route('invitations.index')
            ->with('success', 'Вы присоединились к команде ' . $invitation->team->name . '.');
    }

    /**
     * Отклонить приглашение
     */
    public function decline(TeamInvitation $invitation)
    {
        // Проверка, что приглашение относится к текущему пользователю
        if ($invitation->user_id !== Auth::id()) {
            return redirect()->route('invitations.index')
                ->with('error', 'У вас нет доступа к этому приглашению.');
        }

        // Проверка, что приглашение еще в статусе ожидания
        if (!$invitation->isPending()) {
            return redirect()->route('invitations.index')
                ->with('error', 'Это приглашение уже не активно.');
        }

        // Отклонение приглашения
        $invitation->decline();

        return redirect()->route('invitations.index')
            ->with('success', 'Вы отклонили приглашение в команду ' . $invitation->team->name . '.');
    }
}
