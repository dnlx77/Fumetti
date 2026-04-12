<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Albo;
use App\Collana;
use App\Editore;
use App\Storia;
use App\Autore;
use App\RelStoriaAlbo;
use App\RelAlboAUtoricopertina;
use App\AlboLetture;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Support\Facades\DB;

class AlboController extends Controller
{
    //
    public function create(){
        $lista_collane = Collana::all();
        $lista_editori = Editore::all();
        $lista_storie = Storia::all();
        $lista_autori = Autore::all();

        return view('albo.create',
            ['lista_collane' => $lista_collane,
             'lista_editori' => $lista_editori,
             'lista_storie' => $lista_storie,
             'lista_autori' => $lista_autori
            ]);
    } 

    public function store(Request $request){
        $request->validate([
            'editore' => 'required | numeric',
        ]);
        
        /* Memorizzazione copertina su disco */
        $copertina = $request->file('copertina');
        $estensione = $copertina->getClientOriginalExtension();
        Storage::disk('public')->put($copertina->getFilename().'.'.$estensione, File::get($copertina));

        $albo = new Albo();
        $albo->num_pagine = $request->get('num_pagine');
        $albo->prezzo = $request->get('prezzo');
        $albo->prezzo_lire = $request->get('prezzo_lire');
        $albo->barcode = $request->get('barcode');
        $albo->mime = $copertina->getClientMimeType();
        $albo->original_filename = $copertina->getClientOriginalName();
        $albo->filename = $copertina->getFilename().'.'.$estensione;
        $albo->numero = $request->get('numero');
        $albo->titolo = $request->get('titolo');
        if ($request->get('data_pubblicazione') != 0)
            $albo->data_pubblicazione = \DateTime::createFromFormat('d-m-Y', $request->get('data_pubblicazione'));
        else
            $albo->data_pubblicazione = null;
        /*if ($request->get('data_lettura') != 0)
            $albo->data_lettura = \DateTime::createFromFormat('d-m-Y', $request->get('data_lettura'));
        else
            $albo->data_lettura = null;*/
        $albo->collana_id = $request->get('collana');
        $albo->editore_id = $request->get('editore');
        $albo->save();

        foreach ($request->get('storie') as $storie) {
            $storiaAlbo = new RelStoriaAlbo();
            $storiaAlbo->albo_id = $albo->id;
            $storiaAlbo->storia_id = $storie;
            $storiaAlbo->save();
        }

        if($request->get('autori_copertina')) {
            foreach ($request->get('autori_copertina') as $autore_copertina) {
                $autoreCopertinaalbo = new RelAlboAUtoricopertina();
                $autoreCopertinaalbo->albo_id = $albo->id;
                $autoreCopertinaalbo->autore_id = $autore_copertina;
                $autoreCopertinaalbo->save();
            }
        }      
        
        return redirect(route('albo.index'))->with('success', 'L\'albo è stata salvato.');
    }

    public function index ()
    {
            $sort_by = 'created_at';
            $order = 'desc';
            $per_page = 10;
            $albi = Albo::orderBy($sort_by, $order)->paginate($per_page);
            
        return view('albo.index', 
            [ 'albi' => $albi,
            'cerca_in' => '',
            'cerca_per' => '', 
            'search' => '',
            'ricerca_esatta' => '',
            'stato_lettura' => '',
            'data_pub_iniziale' => '',
            'data_pub_finale' => ''
            ]);
    }

    public function showAlbo ($id_albo)
    {

        //$albi = Albo::find([$id]);
        //$albi = Albo::where('id', $id)->get();
        //$albi = Albo::whereId($id)->get();
        $albi = Albo::GetAlbo($id_albo)->paginate(1);
           
        return view('albo.index', 
            [ 'albi' => $albi,
            'cerca_in' => '',
            'cerca_per' => '', 
            'search' => '',
            'ricerca_esatta' => '',
            'stato_lettura' => '',
            'data_pub_iniziale' => '',
            'data_pub_finale' => ''
            ]);
    }

    public function detailsAlbo ($id_albo) {
        $albo = Albo::find($id_albo);
       
        return view('albo.details',
            [ 'albo' => $albo ]);
    }

    public function edit ($id_albo) {
        $albo = Albo::find($id_albo);
        $lista_collane = Collana::all();
        $lista_editori = Editore::all();
        $lista_storie = Storia::all();
        $lista_autori = Autore::all();

        $lista_storie_albo = DB::table('rel_storia_albo')->where('albo_id', '=', $id_albo)
        ->join('storia', 'storia.id', '=', 'rel_storia_albo.storia_id')
        ->get(['storia.id AS storia_id', 'storia.nome']);

        $lista_autori_copertinaalbo = DB::table('rel_albo_autoricopertina')->where('albo_id', '=', $id_albo)
        ->join('autore', 'autore.id', '=', 'rel_albo_autoricopertina.autore_id')
        ->get(['autore.id AS autore_id', 'autore.cognome', 'autore.nome']);

        $storie_array = [];
        foreach($lista_storie_albo as $storia)
            $storie_array[] = $storia->storia_id;

        $autoricopertina_array = [];
        foreach($lista_autori_copertinaalbo as $autore)
            $autoricopertina_array[] = $autore->autore_id;

        return view('albo.edit',
            [ 'albo' => $albo,
              'lista_collane' => $lista_collane,
              'lista_editori' => $lista_editori,
              'lista_storie' => $lista_storie,
              'lista_autori' => $lista_autori,
              'storie_array' => $storie_array,
              'autoricopertina_array' => $autoricopertina_array
            ]);
    } 

    /*public function alboSetReadDate (Request $request, $id_albo) {
        $albo = Albo::find($id_albo);

        if ($request->get('data_lettura') != 0)
            $albo->data_lettura = \DateTime::createFromFormat('d-m-Y', $request->get('data_lettura'));
        else
            $albo->data_lettura = null;
            
        $albo->save();

        return redirect(route('albo.details', $id_albo));
    }*/

    public function update (Request $request, $id_albo) {
        $request->validate([
            'editore' => 'required | numeric',
        ]);
        

        $copertina = $request->file('copertina');
        if ($copertina) {
            $estensione = $copertina->getClientOriginalExtension();
            Storage::disk('public')->put($copertina->getFilename().'.'.$estensione, File::get($copertina));
        }

        $albo = Albo::find($id_albo);
        $albo->num_pagine = $request->get('num_pagine');
        $albo->prezzo = $request->get('prezzo');
        $albo->prezzo_lire = $request->get('prezzo_lire');
        $albo->barcode = $request->get('barcode');
        $albo->numero = $request->get('numero');
        $albo->titolo = $request->get('titolo');
        if ($request->get('data_pubblicazione') != 0)
            $albo->data_pubblicazione = \DateTime::createFromFormat('d-m-Y', $request->get('data_pubblicazione'));
        else
            $albo->data_pubblicazione = null;
        /*if ($request->get('data_lettura') != 0)
            $albo->data_lettura = \DateTime::createFromFormat('d-m-Y', $request->get('data_lettura'));
        else
            $albo->data_lettura = null;*/
        $albo->collana_id = $request->get('collana');
        $albo->editore_id = $request->get('editore');
        
        if ($copertina) {
            $albo->mime = $copertina->getClientMimeType();
            $albo->original_filename = $copertina->getClientOriginalName();
            Storage::disk('public')->delete($albo->filename);
            $albo->filename = $copertina->getFilename().'.'.$estensione;
        }
        
        $albo->storie()->sync($request->get('storie'));
        $albo->autoriCopertina()->sync($request->get('autori_copertina'));
        $albo->save();

        return redirect(route('albo.index', $id_albo))->with('success', 'L\'albo è stato aggiornato.');
    }

    public function alboEliminaForm($id_albo) {
        return view('albo.elimina_form', [
            'id_albo' => $id_albo
        ]);
    }

    public function alboEliminaExecute($id_albo) {

        $copertina = Albo::find($id_albo)->filename;
        
        try {
            DB::beginTransaction();
            Albo::where('id', '=', $id_albo)->delete();
            DB::commit();
            Storage::disk('public')->delete($copertina);
            return redirect(route('albo.index', 'all'))->with('success', 'Albo eliminato');
        }
        catch(Exception $e){
            DB::rollBack();
            return redirect(route('albo.index', 'all'))->with('success', 'Si è verificato un problema. L\'operazione non è stata eseguita.');
        }
    }

}
