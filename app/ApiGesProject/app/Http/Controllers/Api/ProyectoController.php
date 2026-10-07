<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();

        // Proyectos donde es creador o miembro
        $proyectos = Project::where('owner_id', $usuario->id)
            ->orWhereHas('miembros', function ($q) use ($usuario) {
                $q->where('users.id', $usuario->id);
            })
            ->with(['creador', 'miembros'])
            ->get();

        return response()->json($proyectos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $proyecto = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'owner_id' => $request->user()->id,
        ]);

        return response()->json(['mensaje' => 'Proyecto creado exitosamente', 'proyecto' => $proyecto], 201);
    }

    // Agregar un participante aceptado al proyecto
    public function agregarMiembro(Request $request, $proyectoId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $proyecto = Project::where('id', $proyectoId)
            ->where('owner_id', $request->user()->id)
            ->firstOrFail();

        $proyecto->miembros()->syncWithoutDetaching([$request->user_id => ['role' => 'member']]);

        return response()->json(['mensaje' => 'Miembro agregado al proyecto con éxito']);
    }
}