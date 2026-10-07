<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_code',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Generar un código de usuario único automáticamente al crear la cuenta.
     */
    protected static function booted(): void
    {
        static::creating(function ($usuario) {
            if (empty($usuario->user_code)) {
                do {
                    $codigo = 'USR-' . strtoupper(Str::random(6));
                } while (static::where('user_code', $codigo)->exists());

                $usuario->user_code = $codigo;
            }
        });
    }

    // Proyectos creados por este usuario
    public function misProyectos()
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    // Proyectos en los que participa como miembro/colaborador
    public function proyectosComoMiembro()
    {
        return $this->belongsToMany(Project::class, 'project_members')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    // Solicitudes de contacto/participante enviadas por este usuario
    public function contactosEnviados()
    {
        return $this->hasMany(Contact::class, 'user_id');
    }

    // Solicitudes de contacto/participante recibidas por este usuario
    public function contactosRecibidos()
    {
        return $this->hasMany(Contact::class, 'contact_id');
    }

    // Tareas asignadas a este usuario
    public function tareasAsignadas()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }
}