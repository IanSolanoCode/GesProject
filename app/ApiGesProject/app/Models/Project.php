<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
    ];

    // Creador/Dueño del proyecto
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Miembros del equipo que han sido vinculados a este proyecto
    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members', 'project_id', 'user_id')
                    ->withTimestamps();
    }

    // Tareas pertenecientes al proyecto
    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }
}
