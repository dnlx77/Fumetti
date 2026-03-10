<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ruolo;
use Illuminate\Http\Request;

class RuoloController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'dati' => Ruolo::orderBy('descrizione')->paginate(15)]);
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
}
