<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AlboLetture;
use Illuminate\Http\Request;

class AlboLettureController extends Controller
{
    /**
     * Lista tutte le letture di un albo per l'utente loggato
     */
    public function index(Request $request, $alboId)
    {
        $letture = AlboLetture::where('albo_id', $alboId)
            ->where('user_id', $request->user()->id)
            ->orderBy('data_lettura', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'dati' => $letture
        ]);
    }

    /**
     * Aggiunge una nuova lettura per un albo
     */
    public function store(Request $request, $alboId)
    {
        $dati = $request->validate([
            'data_lettura' => 'required|date',
        ]);

        $lettura = AlboLetture::create([
            'albo_id'      => $alboId,
            'user_id'      => $request->user()->id,
            'data_lettura' => $dati['data_lettura'],
        ]);

        return response()->json([
            'success' => true,
            'dati'    => $lettura
        ], 201);
    }

    /**
     * Elimina una singola lettura (solo se appartiene all'utente loggato)
     */
    public function destroy(Request $request, $alboId, $letturaId)
    {
        $lettura = AlboLetture::where('id', $letturaId)
            ->where('albo_id', $alboId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $lettura->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lettura eliminata con successo!'
        ]);
    }
}
