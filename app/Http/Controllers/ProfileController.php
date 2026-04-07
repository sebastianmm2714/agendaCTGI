<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function updateSignature(Request $request)
    {
        $request->validate([
            'firma' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

        if ($request->hasFile('firma')) {
            $path = $request->file('firma')->store('firmas', 'public');
            $user->update(['firma' => $path]);

            // Sincronizar con la tabla de funcionarios
            // Buscamos por número de documento o email para ser precisos
            \App\Models\Funcionario::where('email', $user->email)
                ->orWhere('numero_documento', $user->numero_documento)
                ->update(['firma' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Firma actualizada correctamente.',
                'path' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No se pudo cargar la imagen.'], 400);
    }
}
