<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'assigned_to',
        'title',
        'description',
        'status',
    ];

    // Estado por defecto (mismo valor que el default de la migración)
    protected $attributes = [
        'status' => 'pendiente',
    ];

    // Proyecto al que pertenece la tarea
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // Usuario al que se le asignó la tarea
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
