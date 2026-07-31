<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ruolo;
use Illuminate\Http\Request;

class RuoloController extends Controller
{
    public function index(Request $request)
    {
        $ordinaPer = $request->query('ordina_per', 'descrizione'); // 'descrizione' per Ruolo
        $direzione = $request->query('direzione', 'asc') === 'desc' ? 'desc' : 'asc';

        $colonneConsentite = ['id', 'descrizione']; // ['id', 'descrizione'] per Ruolo
        if (!in_array($ordinaPer, $colonneConsentite)) {
            $ordinaPer = 'descrizione';
        }

        return response()->json([
            'success' => true,
            'dati' => Ruolo::orderBy($ordinaPer, $direzione)->paginate(15)
        ]);
    }

    public function lista()
    {
        // Estraiamo solo ID e Nome, ordinati alfabeticamente. Leggerissimo!
        $dati = \App\Models\Ruolo::select('id', 'descrizione')->orderBy('descrizione')->get();
        return response()->json(['dati' => $dati]);
    }

    public function store(Request $request)
    {
        $dati = $request->validate(['descrizione' => 'required|string|max:511']);
        return response()->json(['success' => true, 'dati' => Ruolo::create($dati)], 201);
    }

    public function update(Request $request, $id)
    {
        $ruolo = Ruolo::findOrFail($id);
        $dati = $request->validate(['descrizione' => 'required|string|max:511']);
        $ruolo->update($dati);
        return response()->json(['success' => true, 'dati' => $ruolo]);
    }

    public function destroy($id)
    {
        Ruolo::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Ruolo eliminato con successo!']);
    }

    public function show(Request $request, $id)
    {
        $ruolo = \App\Models\Ruolo::findOrFail($id);

        // Classifica autori per numero di storie con questo ruolo
        $classifica = \Illuminate\Support\Facades\DB::table('rel_storia_autore_ruolo')
            ->join('autore', 'rel_storia_autore_ruolo.autore_id', '=', 'autore.id')
            ->where('rel_storia_autore_ruolo.ruolo_id', $id)
            ->groupBy('autore.id', 'autore.cognome', 'autore.nome', 'autore.pseudonimo')
            ->orderByDesc('totale')
            ->select(
                'autore.id',
                'autore.cognome',
                'autore.nome',
                'autore.pseudonimo',
                \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT rel_storia_autore_ruolo.storia_id) as totale')
            )
            ->get();

        return response()->json([
            'success' => true,
            'dati' => [
                'ruolo'      => $ruolo,
                'classifica' => $classifica,
            ]
        ]);
    }
}
