<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Controlliamo che Angular ci abbia inviato email e password
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Proviamo a fare il login con i dati ricevuti
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            // 3. Se i dati sono corretti, recuperiamo l'utente dal database
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // 4. Creiamo il "Braccialetto VIP" (Token)
            // Il nome 'AngularApp' è solo un'etichetta per ricordarci chi usa questo token
            $token = $user->createToken('AngularApp')->plainTextToken;

            // 5. Restituiamo il token e i dati dell'utente ad Angular in formato JSON
            return response()->json([
                'success' => true,
                'message' => 'Login effettuato con successo',
                'user' => $user,
                'token' => $token
            ], 200);
        }

        // 6. Se i dati sono sbagliati, diamo errore
        return response()->json([
            'success' => false,
            'message' => 'Email o password non validi'
        ], 401); // 401 significa "Non autorizzato"
    }
}
