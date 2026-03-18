<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfiloController extends Controller
{
    /**
     * GET /api/v1/profilo
     * Restituisce nome ed email dell'utente loggato
     */
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'dati' => [
                'name'  => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    /**
     * PUT /api/v1/profilo/password
     * Cambia la password dell'utente loggato
     */
    public function cambiaPassword(Request $request)
    {
        $request->validate([
            'password_attuale' => 'required|string',
            'nuova_password'   => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        // Verifica che la password attuale sia corretta
        if (!Hash::check($request->password_attuale, $user->password)) {
            throw ValidationException::withMessages([
                'password_attuale' => ['La password attuale non è corretta.'],
            ]);
        }

        // Aggiorna la password
        $user->update([
            'password' => Hash::make($request->nuova_password),
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Password aggiornata con successo.'
        ]);
    }
}
