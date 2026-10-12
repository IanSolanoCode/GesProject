<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('asigna tareas y las cambia a review_pending', function () {
    $creador = User::factory()->create();
    $responsable = User::factory()->create();
    $proyecto = Project::create([
        'name' => 'Sistema E-Commerce',
        'owner_id' => $creador->id,
    ]);

    Sanctum::actingAs($creador);

    $tarea = $this->postJson('/api/tareas', [
        'title' => 'Migraciones BD',
        'description' => 'Crear migraciones y modelos',
        'project_id' => $proyecto->id,
        'assigned_to' => $responsable->id,
        'due_date' => '2026-10-15 18:00:00',
    ]);

    $tarea
        ->assertCreated()
        ->assertJsonPath('tarea.status', 'pending')
        ->assertJsonPath('tarea.assigned_to', $responsable->id);

    Sanctum::actingAs($responsable);

    $this->patchJson('/api/tareas/'.$tarea->json('tarea.id').'/estado', [
        'status' => 'review_pending',
    ])
        ->assertOk()
        ->assertJsonPath('tarea.status', 'review_pending');
});

it('aprueba tareas como completed y rechaza devolviendo a in_progress', function () {
    $creador = User::factory()->create();
    $responsable = User::factory()->create();
    $proyecto = Project::create([
        'name' => 'Sistema E-Commerce',
        'owner_id' => $creador->id,
    ]);

    $tareaAprobada = Task::create([
        'title' => 'Migraciones BD',
        'project_id' => $proyecto->id,
        'assigned_to' => $responsable->id,
        'created_by' => $creador->id,
        'status' => 'review_pending',
    ]);

    $tareaRechazada = Task::create([
        'title' => 'Controladores API',
        'project_id' => $proyecto->id,
        'assigned_to' => $responsable->id,
        'created_by' => $creador->id,
        'status' => 'review_pending',
    ]);

    Sanctum::actingAs($creador);

    $this->patchJson('/api/tareas/'.$tareaAprobada->id.'/estado', [
        'status' => 'completed',
    ])
        ->assertOk()
        ->assertJsonPath('tarea.status', 'completed');

    $this->patchJson('/api/tareas/'.$tareaRechazada->id.'/estado', [
        'status' => 'in_progress',
        'rejection_reason' => 'Faltan claves foraneas en la tabla tareas.',
    ])
        ->assertOk()
        ->assertJsonPath('tarea.status', 'in_progress')
        ->assertJsonPath('tarea.rejection_reason', 'Faltan claves foraneas en la tabla tareas.');
});
