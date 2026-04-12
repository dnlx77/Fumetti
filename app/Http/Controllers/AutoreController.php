<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Autore;
use App\RelStoriaAutoreRuolo;
use Exception;
use Illuminate\Support\Facades\DB;

class AutoreController extends Controller
{
    //
    public function create(){
        return view('autore.create');
    } 

    public function autoreEliminaForm($id_autore) {
        return view('autore.elimina_form', [
            'id_autore' => $id_autore
        ]);
    }

    public function autoreEliminaExecute($id_autore) {
        try {
            DB::beginTransaction();
            RelStoriaAutoreRuolo::where('autore_id', '=', $id_autore)->delete();
            Autore::where('id', '=', $id_autore)->delete();
            DB::commit();
            return redirect(route('autore.index'))->with('success', 'Autore eliminato');
        }
        catch(Exception $e){
            DB::rollBack();
            return redirect(route('autore.index'))->with('success', 'Si è verificato un problema. L\'operazione non è stata eseguita.');
        }
    }

    public function store(Request $request) {
        $request->validate([
            'cognome' => 'required | string | max:511',
        ]);

        $autore = new Autore();
        $autore->cognome = $request->get('cognome');
        $autore->nome = $request->get('nome');
        $autore->pseudonimo = $request->get('pseudonimo');
        $autore->save();
        
        return redirect(route('autore.index'))->with('success', 'L\'autore è stato salvato.');
    }

    public function index()
    {
        $order_by = 'created_at';
        $sorted = 'desc';
        $per_page = 10;
        $autore = Autore::orderBy($order_by, $sorted)->paginate($per_page);
        

        return view('autore.index', 
        [ 'autore' => $autore,
        'cerca_in' => '',
        'cerca_per' => '',
        'search' => '',
        'tipo_ricerca' => ''
        ]);
    }

    public function edit ($id_autore) {
        $autore = Autore::find($id_autore);
        return view('autore.edit',
            [ 'autore' => $autore ]);
    } 

    public function update (Request $request, $id_autore) {
        $request->validate([
            'cognome' => 'required | string | max:511',
        ]);

        $autore = Autore::find($id_autore);
        $autore->cognome = $request->get('cognome');
        $autore->nome = $request->get('nome');
        $autore->pseudonimo = $request->get('pseudonimo');
        $autore->save ();
        return redirect(route('autore.index'))->with('success', 'L\'autore è stato aggiornato.');
    }
}
