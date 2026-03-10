<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Autore;
use Illuminate\Http\Request;

class AutoreController extends Controller
{
    public function index()
    {
        // Ordiniamo per cognome e poi per nome
        return response()->json([
            'success' => true,
            'dati' => Autore::orderBy('cognome')->orderBy('nome')->paginate(15)
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
