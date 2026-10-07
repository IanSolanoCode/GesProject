<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $tarea = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'project_id' => $request->project_id,
            'assigned_to' => $request->assigned_to,
            'created_by' => $request->user()->id,
            'due_date' => $request->due_date,
            'status' => 'pending',
        ]);

        return response()->json(['mensaje' => 'Tarea creada con éxito', 'tarea' => $tarea], 201);
    }

    // Cambiar estado por el usuario responsable o creador
    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,review_pending,completed',
            'rejection_reason' => 'nullable|string',
        ]);

        $tarea = Task::findOrFail($id);
        $usuario = $request->user();

        // Regla: Solo el creador puede pasarla a 'completed' o devolverla
        if ($request->status === 'completed' && $tarea->created_by !== $usuario->id) {
            return response()->json(['mensaje' => 'Solo el creador del proyecto o tarea puede aprobarla'], 403);
        }

        // Si el creador no la aprueba (se devuelve)
        if ($request->status === 'in_progress' && $tarea->status === 'review_pending' && $tarea->created_by === $usuario->id) {
            $tarea->rejection_reason = $request->rejection_reason;
        }

        $tarea->status = $request->status;
        $tarea->save();

        return response()->json(['mensaje' => 'Estado de la tarea actualizado', 'tarea' => $tarea]);
    }
}
