<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name', 'owner_id'];

    // Связь с пользователями через промежуточную таблицу
    public function users()
    {
        return $this->belongsToMany(User::class, 'team_user');
        return $this->belongsToMany(User::class);
    }

    // Связь с владельцем команды
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }


}


