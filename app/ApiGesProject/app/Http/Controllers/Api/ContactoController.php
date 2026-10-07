<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;

class ContactoController extends Controller
{
    // Agregar un participante mediante su código único (user_code)
    public function agregarPorCodigo(Request $request)
    {
        $request->validate([
            'user_code' => 'required|string|exists:users,user_code',
        ]);

        $usuarioActual = $request->user();
        $contactoAgregar = User::where('user_code', $request->user_code)->first();

        if ($usuarioActual->id === $contactoAgregar->id) {
            return response()->json(['mensaje' => 'No puedes agregarte a ti mismo'], 422);
        }

        // Verificar si ya existe una solicitud previa
        $existe = Contact::where(function ($query) use ($usuarioActual, $contactoAgregar) {
            $query->where('user_id', $usuarioActual->id)->where('contact_id', $contactoAgregar->id);
        })->orWhere(function ($query) use ($usuarioActual, $contactoAgregar) {
            $query->where('user_id', $contactoAgregar->id)->where('contact_id', $usuarioActual->id);
        })->first();

        if ($existe) {
            return response()->json(['mensaje' => 'Ya existe una solicitud o vínculo con este usuario'], 400);
        }

        $contacto = Contact::create([
            'user_id' => $usuarioActual->id,
            'contact_id' => $contactoAgregar->id,
            'status' => 'pending',
        ]);

        return response()->json(['mensaje' => 'Solicitud enviada con éxito', 'contacto' => $contacto], 201);
    }

    // Responder a una solicitud enviada por otra persona (aceptar / rechazar)
    public function responderSolicitud(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $contacto = Contact::where('id', $id)
            ->where('contact_id', $request->user()->id)
            ->firstOrFail();

        $contacto->update(['status' => $request->status]);

        return response()->json(['mensaje' => 'Solicitud actualizada', 'contacto' => $contacto]);
    }

    // Listar contactos aceptados
    public function listarParticipantes(Request $request)
    {
        $userId = $request->user()->id;

        $contactos = Contact::where('status', 'accepted')
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)->orWhere('contact_id', $userId);
            })
            ->with(['remitente', 'destinatario'])
            ->get();

        return response()->json($contactos);
    }
}
