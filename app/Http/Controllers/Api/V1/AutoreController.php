<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Autore;
use Illuminate\Http\Request;

class AutoreController extends Controller
{
    public function index(Request $request)
{
    $ordinaPer = $request->query('ordina_per', 'cognome');
    $direzione = $request->query('direzione', 'asc');

    $colonneConsentite = ['id', 'cognome', 'nome', 'pseudonimo'];
    if (!in_array($ordinaPer, $colonneConsentite)) {
        $ordinaPer = 'cognome';
    }
    $direzione = $direzione === 'desc' ? 'desc' : 'asc';

    return response()->json([
        'success' => true,
        'dati' => Autore::orderBy($ordinaPer, $direzione)
            ->orderBy('nome', $direzione) // secondo criterio
            ->paginate(15)
    ]);
}

    public function lista()
    {
        // Estraiamo gli autori e li formattiamo uno per uno
        $autori = \App\Models\Autore::all()->map(function ($autore) {
            $nomeFormattato = $autore->nome;

            if (!empty($autore->pseudonimo)) {
                $nomeFormattato .= " '" . $autore->pseudonimo . "'";
            }

            if (!empty($autore->cognome)) {
                $nomeFormattato .= " " . $autore->cognome;
            }

            return [
                'id' => $autore->id,
                'nome' => trim($nomeFormattato)
            ];
        })->sortBy('nome')->values();

        return response()->json(['dati' => $autori]);
    }

    public function show(Request $request, $id)
    {
        $autore = \App\Models\Autore::with([
            'storie' => function ($query) {
                $query->withPivot('ruolo_id');
            },
            'albiCopertina' => function ($query) use ($request) {
                $query->where('albo.user_id', $request->user()->id)
                    ->with(['editore', 'collana'])
                    ->orderBy('albo.numero');
            },
        ])->findOrFail($id);

        // Carichiamo le descrizioni dei ruoli in una sola query
        $ruoli = \App\Models\Ruolo::pluck('descrizione', 'id');

        // Arricchiamo ogni storia con la descrizione del ruolo
        $autore->storie->each(function ($storia) use ($ruoli) {
            $storia->pivot->ruolo_descrizione = $ruoli[$storia->pivot->ruolo_id] ?? null;
        });

        // Conteggio storie per ruolo (storie uniche per ogni ruolo)
        $storiePerRuolo = $autore->storie
            ->groupBy('pivot.ruolo_id')
            ->map(function ($storie, $ruoloId) use ($ruoli) {
                return [
                    'ruolo'  => $ruoli[$ruoloId] ?? 'N/D',
                    'totale' => $storie->unique('id')->count()
                ];
            })
            ->values()
            ->sortByDesc('totale')
            ->values();

        // Aggiungiamo i conteggi
        $autore->totale_storie    = $autore->storie->unique('id')->count();
        $autore->totale_albi      = $autore->albiCopertina->count();
        $autore->storie_per_ruolo = $storiePerRuolo;

        return response()->json([
            'success' => true,
            'dati'    => $autore
        ]);
    }

    public function store(Request $request)
    {
        $dati = $request->validate([
            'cognome' => 'required|string|max:511',
            'nome' => 'nullable|string|max:511',
            'pseudonimo' => 'nullable|string|max:511'
        ]);

        return response()->json(['success' => true, 'dati' => Autore::create($dati)], 201);
    }

    public function update(Request $request, $id)
    {
        $autore = Autore::findOrFail($id);

        $dati = $request->validate([
            'cognome' => 'required|string|max:511',
            'nome' => 'nullable|string|max:511',
            'pseudonimo' => 'nullable|string|max:511'
        ]);

        $autore->update($dati);

        return response()->json(['success' => true, 'dati' => $autore]);
    }

    public function destroy($id)
    {
        Autore::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Autore eliminato con successo!']);
    }
}
