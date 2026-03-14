<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\StoriaLetture;
use Illuminate\Http\Request;

class StoriaLettureController extends Controller
{
    /**
     * Lista tutte le letture di una storia per l'utente loggato
     */
    public function index(Request $request, $storiaId)
    {
        $letture = StoriaLetture::where('storia_id', $storiaId)
            ->where('user_id', $request->user()->id)
            ->orderBy('data_lettura', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'dati' => $letture
        ]);
    }

    /**
     * Aggiunge una nuova lettura per una storia
     */
    public function store(Request $request, $storiaId)
    {
        $dati = $request->validate([
            'data_lettura' => 'required|date',
        ]);

        // Controlliamo se la data esiste già per questo utente/albo
        $esisteGia = \Illuminate\Support\Facades\DB::table('storia_letture')
            ->where('storia_id', $storiaId)
            ->where('user_id', $request->user()->id)
            ->where('data_lettura', $dati['data_lettura'])
            ->exists();

        if ($esisteGia) {
            return response()->json([
                'success' => false,
                'message' => 'Hai già registrato una lettura per questa data.'
            ], 422);
        }

        $lettura = StoriaLetture::create([
            'storia_id'      => $storiaId,
            'user_id'      => $request->user()->id,
            'data_lettura' => $dati['data_lettura'],
        ]);

        return response()->json(['success' => true, 'dati' => $lettura], 201);
    }
    
    /**
     * Elimina una singola lettura (solo se appartiene all'utente loggato)
     */
    public function destroy(Request $request, $storiaId, $letturaId)
    {
        \Illuminate\Support\Facades\DB::table('storia_letture')
            ->where('storia_id', $storiaId)
            ->where('user_id', $request->user()->id)
            ->where('data_lettura', $letturaId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lettura eliminata con successo!'
        ]);
    }
}
