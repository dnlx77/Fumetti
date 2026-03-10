<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Editore;
use Illuminate\Http\Request;

class EditoreController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'dati' => Editore::orderBy('nome')->paginate(15)]);
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
