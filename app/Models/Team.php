<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'owner_id'];

    // Связь с пользователями через промежуточную таблицу
    public function users()
    {
        return $this->belongsToMany(User::class, 'team_user');
    }

    // Связь с владельцем команды
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    
    // Связь с приглашениями в команду
    public function invitations()
    {
        return $this->hasMany(TeamInvitation::class);
    }
    
    // Получить ожидающие ответа приглашения
    public function pendingInvitations()
    {
        return $this->invitations()->where('status', 'pending');
    }
}


