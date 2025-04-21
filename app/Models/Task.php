<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Task extends Model
{
    use HasFactory;
    
    protected $fillable = ['title', 'description', 'deadline', 'user_id', 'team_id', 'status_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    
    public function status()
    {
        return $this->belongsTo(TaskStatus::class, 'status_id');
    }

    // Связь с таблицей приоритетов
    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }
    
    /**
     * Проверяет, является ли задача персональной
     */
    public function isPersonal()
    {
        return is_null($this->team_id);
    }
    
    /**
     * Проверяет, является ли задача командной
     */
    public function isTeam()
    {
        return !is_null($this->team_id);
    }
    
    /**
     * Проверяет, может ли пользователь изменить статус задачи
     */
    public function canChangeStatus(User $user)
    {
        // Для личной задачи - только владелец может менять статус
        if ($this->isPersonal()) {
            return $this->user_id === $user->id;
        }
        
        // Для командной задачи
        if ($this->isTeam() && $this->team) {
            $isReviewingStatus = $this->status && $this->status->slug === 'team_reviewing';
            $isTeamOwner = $this->team->owner_id === $user->id;
            
            // Если статус "Отправить на проверку", только владелец команды может изменить его
            if ($isReviewingStatus) {
                return $isTeamOwner;
            }
            
            // Проверяем, является ли пользователь участником команды
            $isTeamMember = $this->team->users()->where('users.id', $user->id)->exists();
            
            // Участник команды может изменить статус, даже если он не создавал эту задачу
            if ($isTeamMember) {
                return true;
            }
            
            // Создатель задачи также может менять статус
            return $this->user_id === $user->id;
        }
        
        return false;
    }
}

