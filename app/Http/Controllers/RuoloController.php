<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ruolo;
use App\RelStoriaAutoreRuolo;
use Exception;
use Illuminate\Support\Facades\DB;

class RuoloController extends Controller
{
    //
    public function create(){
        return view('ruolo.create');
    } 

    public function store(Request $request) {
        $request->validate([
            'descrizione' => 'required | string | max:511',
        ]);

        $ruolo = new Ruolo();
        $ruolo->descrizione = $request->get('descrizione');
        $ruolo->save();
        return redirect(route('ruolo.index'))->with('success', 'Il ruolo è stato salvato.');
    }

    public function ruoloEliminaForm($id_ruolo) {
        return view('ruolo.elimina_form', [
            'id_ruolo' => $id_ruolo
        ]);
    }

    public function ruoloEliminaExecute($id_ruolo) {
        try {
            DB::beginTransaction();
            RelStoriaAutoreRuolo::where('ruolo_id', '=', $id_ruolo)->delete();
            Ruolo::where('id', '=', $id_ruolo)->delete();
            DB::commit();
            return redirect(route('ruolo.index'))->with('success', 'Ruolo eliminato');
        }
        catch(Exception $e){
            DB::rollBack();
            return redirect(route('ruolo.index'))->with('success', 'Si è verificato un problema. L\'operazione non è stata eseguita.');
        }
    }

    public function index()
    {
               
        $ruoli = Ruolo::all();

        return view('ruolo.index', 
            [ 'ruoli' => $ruoli ] 
            );
    }

    public function edit ($id_ruolo) {
        $ruoli = Ruolo::find($id_ruolo);
        return view('ruolo.edit',
            [ 'ruoli' => $ruoli ]);
    } 

    public function update (Request $request, $id_ruolo) {
        $request->validate([
            'descrizione' => 'required | string | max:511',
        ]);

        $ruoli = Ruolo::find($id_ruolo);
        $ruoli->descrizione = $request->get('descrizione');
        $ruoli->save ();
        return redirect(route('ruolo.index'))->with('success', 'Il ruolo è stato aggiornato.');
    }
}
