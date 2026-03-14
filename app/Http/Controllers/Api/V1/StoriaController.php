<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Storia;
use Illuminate\Http\Request;

class StoriaController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'dati' => Storia::orderBy('nome')
                ->with(['autori'])
                ->withCount(['dateLettura'])
                ->paginate(50)
        ]);
    }

    public function lista()
    {
        // Estraiamo solo ID e Nome, ordinati alfabeticamente. Leggerissimo!
        $dati = \App\Models\Storia::select('id', 'nome')->orderBy('nome')->get();
        return response()->json(['dati' => $dati]);
    }

    public function store(Request $request)
    {
        $dati = $request->validate([
            'nome' => 'required|string|max:511',
            'trama' => 'nullable|string|max:5000',
            'stato' => 'required|string|max:511',
            // Array delle date di lettura
            'date_lettura' => 'nullable|array',
            'date_lettura.*' => 'date',
            // Array della tabella a 3 vie (Autore + Ruolo)
            'autori_ruoli' => 'nullable|array',
            'autori_ruoli.*.autore_id' => 'required|integer|exists:autore,id',
            'autori_ruoli.*.ruolo_id' => 'required|integer|exists:ruolo,id',
        ]);

        $storia = Storia::create([
            'nome' => $dati['nome'],
            'trama' => $dati['trama'] ?? null,
            'stato' => $dati['stato'],
        ]);

        $this->sincronizzaRelazioniAvanzate($storia->id, $request);

        return response()->json(['success' => true, 'dati' => $storia], 201);
    }

    public function update(Request $request, $id)
    {
        $storia = Storia::findOrFail($id);
        $dati = $request->validate([
            'nome' => 'required|string|max:511',
            'trama' => 'nullable|string|max:5000',
            'stato' => 'required|string|max:511',
            'date_lettura' => 'nullable|array',
            'date_lettura.*' => 'date',
            'autori_ruoli' => 'nullable|array',
            'autori_ruoli.*.autore_id' => 'required|integer|exists:autore,id',
            'autori_ruoli.*.ruolo_id' => 'required|integer|exists:ruolo,id',
        ]);

        $storia->update([
            'nome' => $dati['nome'],
            'trama' => $dati['trama'] ?? null,
            'stato' => $dati['stato'],
        ]);

        $this->sincronizzaRelazioniAvanzate($storia->id, $request);

        return response()->json(['success' => true, 'dati' => $storia]);
    }

    public function destroy($id)
    {
        Storia::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Storia eliminata con successo!']);
    }

    /**
     * Funzione di supporto per gestire le chiavi composte
     */
    private function sincronizzaRelazioniAvanzate($storiaId, $request)
    {
        // 1. Gestione Letture (date) collegate all'utente
        if ($request->has('date_lettura')) {
    
            $nuoveLetture = [];
            foreach ($request->input('date_lettura', []) as $data) {
                $nuoveLetture[] = [
                    'user_id' => $request->user()->id, // <-- AGGIUNTO!
                    'storia_id' => $storiaId,
                    'data_lettura' => $data,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($nuoveLetture)) {
                \Illuminate\Support\Facades\DB::table('storia_letture')->insertOrIgnore($nuoveLetture);
            }
        }

        // 2. Gestione Pivot a 3 vie (Autore + Ruolo)
        if ($request->has('autori_ruoli')) {
            \Illuminate\Support\Facades\DB::table('rel_storia_autore_ruolo')->where('storia_id', $storiaId)->delete();
            $nuoviLegami = [];
            foreach ($request->input('autori_ruoli', []) as $item) {
                $nuoviLegami[] = [
                    'storia_id' => $storiaId,
                    'autore_id' => $item['autore_id'],
                    'ruolo_id'  => $item['ruolo_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($nuoviLegami)) \Illuminate\Support\Facades\DB::table('rel_storia_autore_ruolo')->insert($nuoviLegami);
        }
    }
}
