<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactoController;
use App\Http\Controllers\Api\ProyectoController;
use App\Http\Controllers\Api\TareaController;

// Rutas Públicas
Route::post('/registro', [AuthController::class, 'registrar']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas Protegidas por Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/perfil', [AuthController::class, 'perfil']);

    // Participantes / Contactos
    Route::post('/contactos/agregar', [ContactoController::class, 'agregarPorCodigo']);
    Route::patch('/contactos/{id}/responder', [ContactoController::class, 'responderSolicitud']);
    Route::get('/contactos', [ContactoController::class, 'listarParticipantes']);

    // Proyectos
    Route::get('/proyectos', [ProyectoController::class, 'index']);
    Route::post('/proyectos', [ProyectoController::class, 'store']);
    Route::post('/proyectos/{id}/miembros', [ProyectoController::class, 'agregarMiembro']);

    // Tareas
    Route::post('/tareas', [TareaController::class, 'store']);
    Route::patch('/tareas/{id}/estado', [TareaController::class, 'cambiarEstado']);
});