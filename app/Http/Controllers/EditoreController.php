<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Editore;
use App\Albo;
use Exception;
use Illuminate\Support\Facades\DB;

class EditoreController extends Controller
{
    //
    public function create(){
        return view('editore.create');
    } 

    public function store(Request $request) {
        $request->validate([
            'nome' => 'required | string | max:511',
        ]);

        $editore = new Editore();
        $editore->nome = $request->get('nome');
        $editore->save();
        return redirect(route('editore.index'))->with('success', 'L\'editore è stato salvato.');
    }

    public function editoreEliminaForm($id_editore) {
        return view('editore.elimina_form', [
            'id_editore' => $id_editore
        ]);
    }

    public function editoreEliminaExecute($id_editore) {
        try {
            DB::beginTransaction();

            //GESTIRE LE RELAZIONI COINVOLTE
            
            Editore::where('id', '=', $id_editore)->delete();
            DB::commit();
            return redirect(route('editore.index'))->with('success', 'Editore eliminato');
        }
        catch(Exception $e){
            DB::rollBack();
            return redirect(route('editore.index'))->with('success', 'Si è verificato un problema. L\'operazione non è stata eseguita.');
        }
    }

    public function index()
    {
               
        $editori = Editore::all();
        $num_albi_per_editore = [];

        foreach ($editori as $editore)
            $num_albi_per_editore[$editore->id] = Albo::NumAlbiPerEditore ($editore->id);

        return view('editore.index', 
            [ 'editori' => $editori,
              'num_albi_per_editore' => $num_albi_per_editore
            ]);
    }

    public function edit ($id_editore) {
        $editori = Editore::find($id_editore);
        return view('editore.edit',
            [ 'editori' => $editori ]);
    } 

    public function update (Request $request, $id_editore) {
        $request->validate([
            'nome' => 'required | string | max:511',
        ]);

        $editori = Editore::find($id_editore);
        $editori->nome = $request->get('nome');
        $editori->save ();
        return redirect(route('editore.index'))->with('success', 'L\'editore è stato aggiornato.');
    }
}
