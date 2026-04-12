<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Storia;
use App\Albo;
use App\RelStoriaAutoreRuolo;
use App\Enums\TipoStoriaEnum;
use Exception;
use Illuminate\Support\Facades\DB;
use BenSampo\Enum\Enum;

class StoriaController extends Controller
{
    //
    public function create(){
        $tipo_storia_list = TipoStoriaEnum::toSelectArray();
        return view('storia.create', [
            'tipo_storia_list' => $tipo_storia_list
        ]);
    } 

    public function store(Request $request) {
        $request->validate([
            'nome' => 'required | string | max:511',
            'stato' => 'required | string | max:511',
        ]);

        $storia = new Storia();
        $storia->nome = $request->get('nome');
        $storia->trama = $request->get('trama');
        $storia->stato = $request->get('stato');
        /*if ($request->get('data_lettura') != 0)
            $storia->data_lettura = \DateTime::createFromFormat('d-m-Y', $request->get('data_lettura'));
        else
            $storia->data_lettura = null;*/
        $storia->save();
        return redirect(route('storia.index'))->with('success', 'La storia è stata salvata.');
    }

    public function storiaEliminaForm($id_storia) {
        return view('storia.elimina_form', [
            'id_storia' => $id_storia
        ]);
    }

    public function storiaEliminaExecute($id_storia) {
        try {
            DB::beginTransaction();
            RelStoriaAutoreRuolo::where('tstoria_id', '=', $id_storia)->delete();
            Storia::where('id', '=', $id_storia)->delete();
            DB::commit();
            return redirect(route('storia.index'))->with('success', 'Storia eliminata');
        }
        catch(Exception $e){
            DB::rollBack();
            return redirect(route('storia.index'))->with('success', 'Si è verificato un problema. L\'operazione non è stata eseguita.');
        }
    }

    public function index(Request $request)
    {
               
        $order_by = 'created_at';
        $sorted = 'desc';
        $per_page = 10;
        $storie = Storia::orderBy($order_by, $sorted)->paginate($per_page);
        
        $tipo_storia_list = TipoStoriaEnum::toSelectArray();
        
        return view('storia.index', 
            [ 'storie' => $storie,
              'tipo_storia_list' => $tipo_storia_list ,
              'cerca_in' => '',
              'cerca_per' => '',
              'ruoli' => '',
              'search' => '',
              'ricerca_esatta' => '',
              'stato_lettura' => '',
              'data_pub_iniziale' => '',
              'data_pub_finale' => ''
            ]);

    }

    public function index_list()
    {
        $sorted = 'asc';
        $order_by ='nome';
        $storie = Storia::orderBy($order_by, $sorted)->get();

        return view('storia.storia_list',
            [ 'storie' => $storie]);
    }

    public function storiaSetReadDate (Request $request, $id_storia) {
        $storia = Storia::find($id_storia);

        if ($request->get('data_lettura') != 0)
            $storia->data_lettura = \DateTime::createFromFormat('d-m-Y', $request->get('data_lettura'));
        else
            $storia->data_lettura = null;
            
        $storia->save();

        return redirect(route('storia.details', $id_storia));
    }

    public function detailsStoria ($id_storia)
    {
        $lista_autori_ruoli = DB::table('rel_storia_autore_ruolo')->where('storia_id', '=', $id_storia)
        ->join('autore', 'autore.id', '=', 'rel_storia_autore_ruolo.autore_id')
        ->join('ruolo', 'ruolo.id', '=', 'rel_storia_autore_ruolo.ruolo_id')
        ->get(['ruolo.id AS ruolo_id', 'ruolo.descrizione', 'autore.id AS autore_id', 'autore.nome', 'autore.cognome', 'autore.pseudonimo'])->sortBy('ruolo_id');

        $lista_ruoli = [];
        
        foreach ($lista_autori_ruoli as $current_ruolo) {
            if (!isset($lista_ruoli[$current_ruolo->ruolo_id])){
                $lista_ruoli[$current_ruolo->ruolo_id] = [];
                $lista_ruoli[$current_ruolo->ruolo_id]['ruolo'] = $current_ruolo->descrizione;
                $lista_ruoli[$current_ruolo->ruolo_id]['nome'] = [];
            }
            if ($current_ruolo->pseudonimo)
                $lista_ruoli[$current_ruolo->ruolo_id]['nome'][$current_ruolo->autore_id] = $current_ruolo->nome.' \''.$current_ruolo->pseudonimo.'\' '.$current_ruolo->cognome;
            else
                $lista_ruoli[$current_ruolo->ruolo_id]['nome'][$current_ruolo->autore_id] = $current_ruolo->nome.' '.$current_ruolo->cognome;
        }

        $storia = Storia::find($id_storia);
        $albi = $storia->albi()->orderBy('numero', 'asc')->paginate(4);

        return view('storia.details',
            [
                'lista_ruoli' => $lista_ruoli,
                'albi' => $albi,
                'storia' => $storia
            ]);
    }

    public function showAlbiFromStoria ($id_storia) {

        $storia = Storia::find ($id_storia);
        $albi = $storia->albi()->paginate(10);

        return view('albo.index', 
            [ 'albi' => $albi,
              'storia' => $storia,
              'cerca_in' => '',
              'cerca_per' => '', 
              'search' => '',
              'ricerca_esatta' => '',
              'stato_lettura' => '',
              'data_pub_iniziale' => '',
              'data_pub_finale' => '' 
            ]);
    }

    public function edit ($id_storia) {
        $storia = Storia::find($id_storia);
        $tipo_storia_list = TipoStoriaEnum::toSelectArray();

        return view('storia.edit',
            [ 
              'storia' => $storia ,
              'tipo_storia_list' => $tipo_storia_list
            ]);
    } 

    public function update (Request $request, $id_storia) {
        $request->validate([
            'nome' => 'required | string | max:511',
            'stato' => 'required | string | max:511',
        ]);

        $storia = Storia::find($id_storia);
        $storia->nome = $request->get('nome');
        $storia->trama = $request->get('trama');
        $storia->stato = $request->get('stato');
        /*if ($request->get('data_lettura') != 0)
            $storia->data_lettura = \DateTime::createFromFormat('d-m-Y', $request->get('data_lettura'));
        else
            $storia->data_lettura = null;*/
        $storia->save ();
        return redirect(route('storia.index'))->with('success', 'La storia è stata aggiornata.');
    }

    public function getTrame($id_storia) {
        $storia = Storia::find($id_storia);

        if (!$storia->trama)
            $trama = "Trama non disponibile";
        else
            $trama = $storia->trama;
        
        $arr = ['titolo' => $storia->nome, 'trama' => $trama];

        return json_encode($arr);
    }
}
