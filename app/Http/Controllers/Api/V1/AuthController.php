<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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

    public function register(Request $request)
    {
        // 1. Controlliamo che i dati in arrivo siano corretti
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // unique verifica che non esista già!
            // La regola "confirmed" in Laravel cerca automaticamente un campo chiamato "password_confirmation". 
            // Magia! È esattamente quello che gli spediamo da Angular.
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Se la validazione fallisce, restituiamo un errore 422 con i dettagli
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Creiamo l'utente nel Database (criptando la password!)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Creiamo il Token Sanctum per questo nuovo utente
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Rispondiamo ad Angular dando il benvenuto e fornendo il Token
        return response()->json([
            'message' => 'Utente registrato con successo!',
            'user' => $user,
            'token' => $token // Angular si aspetta esattamente questa chiave!
        ], 201); // 201 significa "Creato con successo"
    }

    public function logout(Request $request)
    {
        // Prende l'utente autenticato e distrugge il token esatto che sta usando ora
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout effettuato con successo. Token distrutto!'
        ]);
    }
}
