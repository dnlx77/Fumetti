<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Collana;
use App\Albo;
use Exception;
use Illuminate\Support\Facades\DB;

class CollanaController extends Controller
{
    //
    public function create(){
        return view('collana.create');
    } 

    public function store(Request $request) {
        $request->validate([
            'nome' => 'required | string | max:511',
        ]);

        $collana = new Collana();
        $collana->nome = $request->get('nome');
        $collana->save();
        return redirect(route('collana.index'))->with('success', 'La collana è stata salvata.');
    }

    public function collanaEliminaForm($id_collana) {
        return view('collana.elimina_form', [
            'id_collana' => $id_collana
        ]);
    }

    public function collanaEliminaExecute($id_collana) {
        try {
            DB::beginTransaction();
            Collana::where('id', '=', $id_collana)->delete();
            DB::commit();
            return redirect(route('collana.index'))->with('success', 'Collana eliminata');
        }
        catch(Exception $e){
            DB::rollBack();
            return redirect(route('collana.index'))->with('success', 'Si è verificato un problema. L\'operazione non è stata eseguita.');
        }
    }

    public function index() {
        $collane = Collana::all();
        $num_albi_in_collana = [];

        foreach ($collane as $collana)
            $num_albi_in_collana[$collana->id] = Albo::NumAlbiInCollana($collana->id);

        return view('collana.index', 
            [ 
                'collane' => $collane,
                'num_albi_in_collana' => $num_albi_in_collana
            ]);
    }

    public function edit ($id_collana) {
        $collane = Collana::find($id_collana);
        return view('collana.edit',
            [ 
                'collane' => $collane,
            ]
        );
    } 

    public function update (Request $request, $id_collana) {
        $request->validate([
            'nome' => 'required | string | max:511',
        ]);

        $collane = Collana::find($id_collana);
        $collane->nome = $request->get('nome');
        $collane->save ();
        return redirect(route('collana.index'))->with('success', 'La collana è stato aggiornata.');
    }
}
