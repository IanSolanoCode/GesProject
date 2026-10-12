<?php

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('crea proyectos para el usuario autenticado', function () {
    $usuario = User::factory()->create();

    Sanctum::actingAs($usuario);

    $this->postJson('/api/proyectos', [
        'name' => 'Sistema E-Commerce',
        'description' => 'Desarrollo de API backend',
        'start_date' => '2026-10-01',
        'end_date' => '2026-12-31',
    ])
        ->assertCreated()
        ->assertJsonPath('proyecto.name', 'Sistema E-Commerce')
        ->assertJsonPath('proyecto.owner_id', $usuario->id);

    $this->assertDatabaseHas('projects', [
        'name' => 'Sistema E-Commerce',
        'owner_id' => $usuario->id,
    ]);
});

it('agrega miembros al proyecto cuando son contactos aceptados', function () {
    $creador = User::factory()->create();
    $miembro = User::factory()->create();

    Contact::create([
        'user_id' => $creador->id,
        'contact_id' => $miembro->id,
        'status' => 'accepted',
    ]);

    Sanctum::actingAs($creador);

    $proyectoId = $this->postJson('/api/proyectos', [
        'name' => 'Sistema E-Commerce',
    ])->json('proyecto.id');

    $this->postJson('/api/proyectos/'.$proyectoId.'/miembros', [
        'user_id' => $miembro->id,
    ])
        ->assertOk();

    $this->assertDatabaseHas('project_members', [
        'project_id' => $proyectoId,
        'user_id' => $miembro->id,
        'role' => 'member',
    ]);
});

it('rechaza agregar miembros que no son contactos aceptados', function () {
    $creador = User::factory()->create();
    $usuarioSinAceptar = User::factory()->create();

    Sanctum::actingAs($creador);

    $proyectoId = $this->postJson('/api/proyectos', [
        'name' => 'Sistema E-Commerce',
    ])->json('proyecto.id');

    $this->postJson('/api/proyectos/'.$proyectoId.'/miembros', [
        'user_id' => $usuarioSinAceptar->id,
    ])->assertForbidden();

    $this->assertDatabaseMissing('project_members', [
        'project_id' => $proyectoId,
        'user_id' => $usuarioSinAceptar->id,
    ]);
});
