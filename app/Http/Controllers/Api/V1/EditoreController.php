<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Editore;
use Illuminate\Http\Request;

class EditoreController extends Controller
{
    public function index(Request $request)
    {
        $ordinaPer = $request->query('ordina_per', 'nome'); // 'descrizione' per Ruolo
        $direzione = $request->query('direzione', 'asc') === 'desc' ? 'desc' : 'asc';

        $colonneConsentite = ['id', 'nome']; // ['id', 'descrizione'] per Ruolo
        if (!in_array($ordinaPer, $colonneConsentite)) {
            $ordinaPer = 'nome';
        }

        return response()->json([
            'success' => true,
            'dati' => Editore::orderBy($ordinaPer, $direzione)->paginate(15)
        ]);
    }

    public function lista()
    {
        // Estraiamo solo ID e Nome, ordinati alfabeticamente. Leggerissimo!
        $dati = \App\Models\Editore::select('id', 'nome')->orderBy('nome')->get();
        return response()->json(['dati' => $dati]);
    }

    public function store(Request $request)
    {
        $dati = $request->validate(['nome' => 'required|string|max:511']);
        return response()->json(['success' => true, 'dati' => Editore::create($dati)], 201);
    }

    public function update(Request $request, $id)
    {
        $editore = Editore::findOrFail($id);
        $dati = $request->validate(['nome' => 'required|string|max:511']);
        $editore->update($dati);
        return response()->json(['success' => true, 'dati' => $editore]);
    }

    public function destroy($id)
    {
        Editore::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Editore eliminato con successo!']);
    }
}
