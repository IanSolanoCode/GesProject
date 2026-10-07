<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'owner_id',
    ];

    // Creador/Dueño del proyecto
    public function creador()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Miembros o participantes asignados a este proyecto
    public function miembros()
    {
        return $this->belongsToMany(User::class, 'project_members')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    // Tareas pertenecientes a este proyecto
    public function tareas()
    {
        return $this->hasMany(Task::class);
    }
}