<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Task extends Model
{use HasFactory;
    protected $fillable = ['title', 'description', 'deadline', 'status', 'user_id', 'team_id'];

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
        return $this->belongsTo(Status::class);
    }

    // Связь с таблицей приоритетов
    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }
    
}

