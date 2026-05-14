<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Storia;
use Illuminate\Http\Request;

class StoriaController extends Controller
{
    public function index(Request $request)
    {
        $ordinaPer = $request->query('ordina_per', 'nome');
        $direzione = $request->query('direzione', 'asc');

        $colonneConsentite = ['id', 'nome', 'stato', 'date_lettura_count'];
        if (!in_array($ordinaPer, $colonneConsentite)) {
            $ordinaPer = 'nome';
        }
        $direzione = $direzione === 'desc' ? 'desc' : 'asc';

        return response()->json([
            'success' => true,
            'dati' => Storia::orderBy($ordinaPer, $direzione)
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
        $storia = Storia::findOrFail($id);

        // Elimina prima i record nelle tabelle collegate
        \Illuminate\Support\Facades\DB::table('rel_storia_autore_ruolo')
            ->where('storia_id', $id)->delete();

        \Illuminate\Support\Facades\DB::table('rel_storia_albo')
            ->where('storia_id', $id)->delete();

        \Illuminate\Support\Facades\DB::table('storia_letture')
            ->where('storia_id', $id)->delete();

        $storia->delete();

        return response()->json(['success' => true, 'message' => 'Storia eliminata con successo!']);
    }

    public function show(Request $request, $id)
    {
        $storia = Storia::with([
            'autori' => function ($query) {
                $query->withPivot('ruolo_id');
            },
            'albi' => function ($query) use ($request) {
                $query->where('albo.user_id', $request->user()->id)
                    ->with(['editore', 'collana'])
                    ->orderBy('albo.numero');
            },
            'dateLettura' => function ($query) use ($request) {
                $query->where('user_id', $request->user()->id)
                    ->orderBy('data_lettura', 'desc');
            }
        ])->findOrFail($id);

        $ruoli = \App\Models\Ruolo::pluck('descrizione', 'id');
        $storia->autori->each(function ($autore) use ($ruoli) {
            $autore->pivot->ruolo_descrizione = $ruoli[$autore->pivot->ruolo_id] ?? null;
        });

        // ================================
        // DATI GRAFICI
        // ================================
        $userId = $request->user()->id;

        // Albi pubblicati per anno/mese (raggruppa data_pubblicazione)
        $albbiPubblicati = \Illuminate\Support\Facades\DB::table('rel_storia_albo')
            ->join('albo', 'rel_storia_albo.albo_id', '=', 'albo.id')
            ->where('rel_storia_albo.storia_id', $id)
            ->where('albo.user_id', $userId)
            ->whereNotNull('albo.data_pubblicazione')
            ->groupBy('anno', 'mese')
            ->orderBy('anno')
            ->orderBy('mese')
            ->select(
                \Illuminate\Support\Facades\DB::raw('YEAR(albo.data_pubblicazione) as anno'),
                \Illuminate\Support\Facades\DB::raw('MONTH(albo.data_pubblicazione) as mese'),
                \Illuminate\Support\Facades\DB::raw('COUNT(*) as totale')
            )
            ->get();

        // Albi letti per anno/mese (raggruppa data_lettura)
        $albiLetti = \Illuminate\Support\Facades\DB::table('rel_storia_albo')
            ->join('albo', 'rel_storia_albo.albo_id', '=', 'albo.id')
            ->join('albo_letture', function ($join) use ($userId) {
                $join->on('albo_letture.albo_id', '=', 'albo.id')
                    ->where('albo_letture.user_id', $userId);
            })
            ->where('rel_storia_albo.storia_id', $id)
            ->where('albo.user_id', $userId)
            ->groupBy('anno', 'mese')
            ->orderBy('anno')
            ->orderBy('mese')
            ->select(
                \Illuminate\Support\Facades\DB::raw('YEAR(albo_letture.data_lettura) as anno'),
                \Illuminate\Support\Facades\DB::raw('MONTH(albo_letture.data_lettura) as mese'),
                \Illuminate\Support\Facades\DB::raw('COUNT(*) as totale')
            )
            ->get();

        return response()->json([
            'success' => true,
            'dati'    => $storia,
            'grafici' => [
                'albi_pubblicati' => $albbiPubblicati,
                'albi_letti'      => $albiLetti,
            ]
        ]);
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
