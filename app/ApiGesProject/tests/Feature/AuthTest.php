<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('registra un usuario, genera user_code e inicia sesion con Sanctum', function () {
    $registro = $this->postJson('/api/registro', [
        'name' => 'Ian Karlhos',
        'email' => 'ianks@example.com',
        'password' => '12345678',
    ]);

    $registro
        ->assertCreated()
        ->assertJsonStructure([
            'mensaje',
            'usuario' => ['id', 'name', 'email', 'user_code'],
            'token',
        ]);

    expect($registro->json('usuario.user_code'))
        ->toStartWith('USR-')
        ->toHaveLength(10);

    $login = $this->postJson('/api/login', [
        'email' => 'ianks@example.com',
        'password' => '12345678',
    ]);

    $login
        ->assertOk()
        ->assertJsonStructure([
            'mensaje',
            'usuario' => ['id', 'name', 'email', 'user_code'],
            'token',
        ]);
});

it('permite consultar el perfil autenticado con Sanctum', function () {
    $usuario = User::factory()->create();

    Sanctum::actingAs($usuario);

    $this->getJson('/api/perfil')
        ->assertOk()
        ->assertJsonPath('id', $usuario->id)
        ->assertJsonPath('email', $usuario->email);
});
