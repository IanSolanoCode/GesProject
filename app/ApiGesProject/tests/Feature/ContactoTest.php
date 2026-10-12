<?php

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('envia solicitudes por user_code y acepta participantes', function () {
    $remitente = User::factory()->create();
    $destinatario = User::factory()->create();

    Sanctum::actingAs($remitente);

    $solicitud = $this->postJson('/api/contactos/agregar', [
        'user_code' => $destinatario->user_code,
    ]);

    $solicitud
        ->assertCreated()
        ->assertJsonPath('contacto.user_id', $remitente->id)
        ->assertJsonPath('contacto.contact_id', $destinatario->id)
        ->assertJsonPath('contacto.status', 'pending');

    Sanctum::actingAs($destinatario);

    $this->patchJson('/api/contactos/'.$solicitud->json('contacto.id').'/responder', [
        'status' => 'accepted',
    ])
        ->assertOk()
        ->assertJsonPath('contacto.status', 'accepted');

    $this->getJson('/api/contactos')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.status', 'accepted');
});

it('rechaza solicitudes duplicadas entre los mismos usuarios', function () {
    $remitente = User::factory()->create();
    $destinatario = User::factory()->create();

    Contact::create([
        'user_id' => $remitente->id,
        'contact_id' => $destinatario->id,
        'status' => 'pending',
    ]);

    Sanctum::actingAs($destinatario);

    $this->postJson('/api/contactos/agregar', [
        'user_code' => $remitente->user_code,
    ])
        ->assertStatus(400);
});
