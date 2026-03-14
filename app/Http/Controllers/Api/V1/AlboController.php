<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Albo;

class AlboController extends Controller
{
    /**
     * Restituisce la lista di tutti gli albi dell'utente loggato
     */
    public function index(Request $request)
    {
        // 1. Capiamo chi ci sta facendo la richiesta grazie al Token
        $user = $request->user();

        // 2. Cerchiamo nel database SOLO gli albi che hanno il suo user_id.
        // Usiamo "with" per includere comodamente le informazioni su collana ed editore.
        $albi = Albo::where('user_id', $user->id)
            ->with(['collana', 'editore', 'autoriCopertina', 'storie'])
            ->withCount(['dateLettura'])
            ->paginate(50);

        // 3. Restituiamo il risultato formattato in JSON
        return response()->json([
            'success' => true,
            'dati' => $albi
        ], 200);
    }

    /**
     * Salva un nuovo albo nel database (con upload immagine e relazioni pivot)
     */
    public function store(Request $request)
    {
        // 1. Validazione (aggiunti i controlli per gli array di Autori e Storie)
        $datiRicevuti = $request->validate([
            // Dati base
            'editore_id' => 'required|integer|exists:editore,id',
            'titolo' => 'nullable|string|max:511',
            'collana_id' => 'nullable|integer|exists:collana,id',
            'numero' => 'nullable|integer',
            'num_pagine' => 'nullable|integer',
            'prezzo' => 'nullable|numeric',
            'prezzo_lire' => 'nullable|numeric',
            'barcode' => 'nullable|string|max:511',
            'data_pubblicazione' => 'nullable|date',

            // File immagine
            'file_copertina' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',

            // Relazioni Pivot (devono essere array di numeri esistenti)
            'autori_copertina' => 'nullable|array',
            'autori_copertina.*' => 'integer|exists:autore,id', // Controlla che ogni ID esista
            'storie' => 'nullable|array',
            'storie.*' => 'integer|exists:storia,id',

            'date_lettura' => 'nullable|array',
            'date_lettura.*' => 'date',
        ]);

        // 2. Gestione dell'Upload del File
        if ($request->hasFile('file_copertina')) {
            $file = $request->file('file_copertina');
            $path = $file->store('copertine', 'public');
            $datiRicevuti['filename'] = $path;
            $datiRicevuti['mime'] = $file->getClientMimeType();
            $datiRicevuti['original_filename'] = $file->getClientOriginalName();
        }

        // Estraiamo gli array dalle variabili prima di salvare l'albo, per pulire $datiRicevuti
        $autoriCopertina = $request->input('autori_copertina', []);
        $storie = $request->input('storie', []);

        // Rimuoviamo i campi che non vanno direttamente nella tabella "albo"
        unset($datiRicevuti['file_copertina'], $datiRicevuti['autori_copertina'], $datiRicevuti['storie']);

        // 3. Aggiungiamo l'utente loggato e creiamo l'Albo!
        $datiRicevuti['user_id'] = $request->user()->id;
        $nuovoAlbo = Albo::create($datiRicevuti);

        // ==========================================
        // 4. MAGIA DELLE RELAZIONI PIVOT
        // ==========================================
        if (!empty($autoriCopertina)) {
            // Usa il nome esatto della relazione definita nel Model Albo.php
            $nuovoAlbo->autoriCopertina()->sync($autoriCopertina);
        }

        if (!empty($storie)) {
            // Usa il nome esatto della relazione definita nel Model Albo.php
            $nuovoAlbo->storie()->sync($storie);
        }

        // Gestione delle date di lettura dell'Albo collegate all'utente
        if ($request->has('date_lettura')) {

            $nuoveLetture = [];
            foreach ($request->input('date_lettura', []) as $data) {
                $nuoveLetture[] = [
                    'user_id' => $request->user()->id, // <-- AGGIUNTO!
                    'albo_id' => $nuovoAlbo->id,       // N.B: usa $albo->id nell'update!
                    'data_lettura' => $data,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($nuoveLetture)) {
                \Illuminate\Support\Facades\DB::table('albo_letture')->insertOrIgnore($nuoveLetture);
            }
        }

        // Opzionale: Ricarichiamo l'albo appena creato includendo anche i dati delle relazioni
        // per far vedere ad Angular che è andato tutto a buon fine
        $nuovoAlbo->load(['autoriCopertina', 'storie']);


        return response()->json([
            'success' => true,
            'message' => 'Nuovo albo creato con successo, inclusi autori e storie!',
            'dati' => $nuovoAlbo
        ], 201);
    }

    /**
     * Aggiorna un albo esistente
     */
    public function update(Request $request, $id)
    {
        // 1. Troviamo l'albo, ma SOLO se appartiene all'utente loggato! (Sicurezza prima di tutto)
        $albo = Albo::where('user_id', $request->user()->id)->findOrFail($id);

        // 2. Validazione (uguale allo store)
        $datiRicevuti = $request->validate([
            'editore_id' => 'required|integer|exists:editore,id',
            'titolo' => 'nullable|string|max:511',
            'collana_id' => 'nullable|integer|exists:collana,id',
            'numero' => 'nullable|integer',
            'num_pagine' => 'nullable|integer',
            'prezzo' => 'nullable|numeric',
            'prezzo_lire' => 'nullable|numeric',
            'barcode' => 'nullable|string|max:511',
            'data_pubblicazione' => 'nullable|date',
            'file_copertina' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'autori_copertina' => 'nullable|array',
            'autori_copertina.*' => 'integer|exists:autore,id',
            'storie' => 'nullable|array',
            'storie.*' => 'integer|exists:storia,id',
            'date_lettura' => 'nullable|array',
            'date_lettura.*' => 'date',
        ]);

        // 3. Gestione della NUOVA immagine (se l'utente ne ha caricata una nuova)
        if ($request->hasFile('file_copertina')) {
            // Se c'era una vecchia immagine, la cancelliamo dal disco per non occupare spazio!
            if ($albo->filename) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($albo->filename);
            }

            // Salviamo la nuova
            $file = $request->file('file_copertina');
            $path = $file->store('copertine', 'public');
            $datiRicevuti['filename'] = $path;
            $datiRicevuti['mime'] = $file->getClientMimeType();
            $datiRicevuti['original_filename'] = $file->getClientOriginalName();
        }
        unset($datiRicevuti['file_copertina']); // Puliamo l'array

        // 4. Estraiamo gli array pivot e puliamo i dati
        $autoriCopertina = $request->input('autori_copertina', []);
        $storie = $request->input('storie', []);
        unset($datiRicevuti['autori_copertina'], $datiRicevuti['storie']);

        // 5. Aggiorniamo i dati testuali dell'Albo
        $albo->update($datiRicevuti);

        // 6. Aggiorniamo le relazioni pivot (sync aggiunge i nuovi e toglie quelli vecchi in automatico!)
        if ($request->has('autori_copertina')) {
            $albo->autoriCopertina()->sync($autoriCopertina);
        }
        if ($request->has('storie')) {
            $albo->storie()->sync($storie);
        }

        // Gestione delle date di lettura dell'Albo collegate all'utente
        if ($request->has('date_lettura')) {
            // Cancelliamo solo le letture di QUESTO utente per QUESTO albo
            \Illuminate\Support\Facades\DB::table('albo_letture')
                ->where('albo_id', $albo->id) // N.B: usa $albo->id nell'update!
                ->where('user_id', $request->user()->id)
                ->delete();

            $nuoveLetture = [];
            foreach ($request->input('date_lettura', []) as $data) {
                $nuoveLetture[] = [
                    'user_id' => $request->user()->id, // <-- AGGIUNTO!
                    'albo_id' => $albo->id,       // N.B: usa $albo->id nell'update!
                    'data_lettura' => $data,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($nuoveLetture)) {
                \Illuminate\Support\Facades\DB::table('albo_letture')->insert($nuoveLetture);
            }
        }

        $albo->load(['autoriCopertina', 'storie']); // Ricarichiamo i dati aggiornati

        return response()->json([
            'success' => true,
            'message' => 'Albo aggiornato con successo!',
            'dati' => $albo
        ], 200);
    }

    /**
     * Elimina un albo
     */
    public function destroy(Request $request, $id)
    {
        // 1. Troviamo l'albo (sempre controllando che sia dell'utente loggato)
        $albo = Albo::where('user_id', $request->user()->id)->findOrFail($id);

        // 2. Cancelliamo l'immagine dal disco (se esiste)
        if ($albo->filename) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($albo->filename);
        }

        // 3. Scolleghiamo le relazioni pivot (opzionale se hai il cascade nel DB, ma è una buona pratica)
        $albo->autoriCopertina()->detach();
        $albo->storie()->detach();

        // 4. Eliminiamo l'albo dal database
        $albo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Albo eliminato con successo!'
        ], 200);
    }
}
