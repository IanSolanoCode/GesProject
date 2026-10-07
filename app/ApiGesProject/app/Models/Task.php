<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'project_id',
        'assigned_to',
        'created_by',
        'rejection_reason',
    ];

    // Proyecto al que pertenece la tarea
    public function proyecto()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // Usuario asignado a desarrollar la tarea
    public function responsable()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Usuario que creó y debe aprobar la tarea
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}