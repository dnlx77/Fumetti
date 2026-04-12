<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Autore;
use App\Ruolo;
use App\Storia;
use App\RelStoriaAutoreRuolo;
use Exception;
use Illuminate\Support\Facades\DB;

class RelStoriaAutoreRuoloController extends Controller
{
    //
    public function aggiungiAutore ($id_storia, Request $request) {
        $lista_autori = Autore::all();
        $lista_ruoli = Ruolo::all();
        //$storia = Storia::find($id_storia);
        $id_autore_default = (!empty($request->get('autore_id')))?$request->get('autore_id'):"";

        return view('rel_storia_autore_ruolo.aggiungi_autore',
            [ 'lista_autori' => $lista_autori,
            'lista_ruoli' => $lista_ruoli,
            'id_storia' => $id_storia,
            'id_autore_default' => $id_autore_default
            ]);
    } 

    public function storeAutore (Request $request, $id_storia) {
        $request->validate([
            'autore' => 'required | max:511',
            'ruolo' => 'required | max:511',
        ]);
        //SALVARE UNA RIGA PER OGNI RUOLO
        DB::beginTransaction();
        try {
            RelStoriaAutoreRuolo::where('storia_id', '=', $id_storia)->where('autore_id', '=', $request->get('autore'))->delete();
            
            $ruoli = $request->get('ruolo');
            foreach ($ruoli as $ruolo) {
                $storia_autore_ruolo = new RelStoriaAutoreRuolo();
                $storia_autore_ruolo->storia_id = $id_storia;
                $storia_autore_ruolo->autore_id = $request->get('autore');
                $storia_autore_ruolo->ruolo_id = $ruolo;
                $storia_autore_ruolo->save ();
            }
            DB::commit();
            return redirect(route('storia.autore', $id_storia))->with('success', 'Autori e ruoli aggiunti');
        }
        catch(Exception $e){
            DB::rollBack();
            return redirect(route('storia.index'))->with('success', 'Si è verificato un problema. L\'operazione non è stata eseguita.');
        }
    } 

    public function index($id_storia)
    {
        $autori = DB::table('rel_storia_autore_ruolo')->where('storia_id', '=', $id_storia)
        ->join('autore', 'autore.id', '=', 'rel_storia_autore_ruolo.autore_id')
        ->join('ruolo', 'ruolo.id', '=', 'rel_storia_autore_ruolo.ruolo_id')
        ->get(['autore.id AS autore_id', 'autore.nome', 'autore.cognome', 'autore.pseudonimo', 'ruolo.id AS ruolo_id', 'ruolo.descrizione']);
        
        $info_autori = [];

        foreach($autori AS $current_autore){
            if(!isset($info_autori[$current_autore->autore_id])){
                $info_autori[$current_autore->autore_id] = [];
                if($current_autore->pseudonimo)
                    $autore = $current_autore->nome.' \''.$current_autore->pseudonimo.'\' '.$current_autore->cognome;
                else
                    $autore = $current_autore->nome . ' ' . $current_autore->cognome;
                $info_autori[$current_autore->autore_id]['nome'] = $autore;
                $info_autori[$current_autore->autore_id]['ruoli'] = [];
            }
            $info_autori[$current_autore->autore_id]['ruoli'][$current_autore->ruolo_id] = $current_autore->descrizione;
        }

        $storia = Storia::find($id_storia);

        return view('rel_storia_autore_ruolo.storia_autori', [ 
            'storia' => $storia,
            'info_autori' => $info_autori
            ]);
    }

    public function storie_list($id_autore)
    {
        
        $ruoli = DB::table('rel_storia_autore_ruolo')->where('autore_id', '=', $id_autore)
        ->join('storia', 'storia.id', '=', 'rel_storia_autore_ruolo.storia_id')
        ->join('ruolo', 'ruolo.id', '=', 'rel_storia_autore_ruolo.ruolo_id')
        ->get(['ruolo.id AS ruolo_id', 'ruolo.descrizione', 'storia.id AS storia_id', 'storia.nome']);

        $info_ruoli = [];

        foreach($ruoli AS $current_ruolo) {
            if(!isset($info_ruoli[$current_ruolo->ruolo_id])){
                $info_ruoli[$current_ruolo->ruolo_id] = [];
                $info_ruoli[$current_ruolo->ruolo_id]['ruolo'] = $current_ruolo->descrizione;
                $info_ruoli[$current_ruolo->ruolo_id]['titoli'] = [];
            }
            $info_ruoli[$current_ruolo->ruolo_id]['titoli'][$current_ruolo->storia_id] = $current_ruolo->nome;
        }

        $autore = Autore::find($id_autore);
        
        /*$sorted = 'asc';
        $order_by ='nome';
        $autore = Autore::find($id_autore);
        $storie = $autore->storie(null,null,null)->orderBy($order_by, $sorted)->distinct()->get();*/

        return view('autore.lista_storie', [ 
            'info_ruoli' => $info_ruoli,
            'autore' => $autore,
            'ruoli' => $ruoli]);
    }

    public function eliminaAutoreForm ($id_storia, $id_autore) {
        return view('rel_storia_autore_ruolo.elimina_autore_form', [
            'id_storia' => $id_storia,
            'id_autore' => $id_autore
        ]);
    }

    public function eliminaAutoreExecute ($id_storia, $id_autore)
    {
        try {
            DB::beginTransaction();
            RelStoriaAutoreRuolo::where('storia_id', '=', $id_storia)->where('autore_id', '=', $id_autore)->delete(); 
            DB::commit();
            return redirect(route('storia.autore', $id_storia))->with('success', 'Autore eliminato');
        }
        catch(Exception $e){
            DB::rollBack();
            return redirect(route('storia.index'))->with('success', 'Si è verificato un problema. L\'operazione non è stata eseguita.');
        }
    }

    public function getRuoliJson($id_storia, $id_autore) {
        /*
        FAI LA QUERY CHE RESTITUISCE UN ARRAY MONODIMENSIONALE DI RUOLI A
        PARTIRE DA STORIA E AUTORE. POI PASSA L'ARRAY ALLA FUNZIONE JSON_ENCODE.
        VERIFICA CHE IN CONSOLE COMPAIONO I VALORI CORRETTI.
        POI SE PROPRIO TI REGGE GOOGOLA PER VEDERE COME CAMBIARE IL VALORE DI UNA SELECT
        MULTIPLA TRAMITE JSON
        */

        $ruoli = DB::table('rel_storia_autore_ruolo')->where('storia_id', '=', $id_storia)->where('autore_id', '=', $id_autore)
        ->join('autore', 'autore.id', '=', 'rel_storia_autore_ruolo.autore_id')
        ->join('ruolo', 'ruolo.id', '=', 'rel_storia_autore_ruolo.ruolo_id')
        ->get(['ruolo.descrizione', 'ruolo.id AS ruolo_id']);

        $ruoli_array = [];
        foreach($ruoli AS $ruolo)
            $ruoli_array[] = $ruolo->ruolo_id;

        return json_encode($ruoli_array);
    }

    public function getOnlyRuoliJson($id_autore) {
    
        $ruoli = DB::table('rel_storia_autore_ruolo')->where('autore_id', '=', $id_autore)
        ->join('ruolo', 'ruolo.id', '=', 'rel_storia_autore_ruolo.ruolo_id')
        ->distinct()
        ->get(['ruolo.descrizione', 'ruolo.id AS ruolo_id']);

        $ruoli_array = [];
        foreach ($ruoli as $ruolo)
            $ruoli_array[$ruolo->ruolo_id] = $ruolo->descrizione;

        return json_encode($ruoli_array);
    }

}
