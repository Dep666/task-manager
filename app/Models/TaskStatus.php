<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskStatus extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'slug', 'description', 'type'];

    /**
     * Задачи с данным статусом
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'status_id');
    }

    /**
     * Проверяет, является ли статус командным
     */
    public function isTeamStatus()
    {
        return $this->type === 'team';
    }

    /**
     * Проверяет, является ли статус персональным
     */
    public function isPersonalStatus()
    {
        return $this->type === 'personal';
    }
} 