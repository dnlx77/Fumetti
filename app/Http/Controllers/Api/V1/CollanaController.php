<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Collana;
use Illuminate\Http\Request;

class CollanaController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'dati' => Collana::orderBy('nome')->paginate(15)]);
    }

    public function lista()
    {
        // Estraiamo solo ID e Nome, ordinati alfabeticamente. Leggerissimo!
        $dati = \App\Models\Collana::select('id', 'nome')->orderBy('nome')->get();
        return response()->json(['dati' => $dati]);
    }

    public function store(Request $request)
    {
        $dati = $request->validate([
            'nome' => 'required|string|max:511',
            'num_albi' => 'required|integer',
            'stato' => 'required|string|max:511'
        ]);
        return response()->json(['success' => true, 'dati' => Collana::create($dati)], 201);
    }

    public function update(Request $request, $id)
    {
        $collana = Collana::findOrFail($id);
        $dati = $request->validate([
            'nome' => 'required|string|max:511',
            'num_albi' => 'required|integer',
            'stato' => 'required|string|max:511'
        ]);
        $collana->update($dati);
        return response()->json(['success' => true, 'dati' => $collana]);
    }

    public function destroy($id)
    {
        Collana::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Collana eliminata con successo!']);
    }
}
