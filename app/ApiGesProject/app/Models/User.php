<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'code',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Genera un código único de 10 caracteres al crear el usuario
    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->code)) {
                do {
                    $code = strtoupper(Str::random(10));
                } while (DB::table('users')->where('code', $code)->exists());

                $user->code = $code;
            }
        });
    }

    // Proyectos creados por este usuario (como dueño)
    public function ownedProjects()
    {
        return $this->hasMany(Project::class, 'user_id');
    }

    // Proyectos en los que participa como colaborador/miembro
    public function memberProjects()
    {
        return $this->belongsToMany(Project::class, 'project_members', 'user_id', 'project_id')
                    ->withTimestamps();
    }

    // Miembros/Contactos agregados a la lista de equipo de este usuario
    public function teamMembers()
    {
        return $this->belongsToMany(User::class, 'team_members', 'user_id', 'member_id')
                    ->withTimestamps();
    }

    // Tareas asignadas a este usuario
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    // Atributos convertidos
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
