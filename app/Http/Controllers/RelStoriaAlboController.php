<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Albo;
use App\Storia;
use App\RelStoriaAlbo;
use Exception;
use Illuminate\Support\Facades\DB;

class RelStoriaAlboController extends Controller
{
    //
    public function aggiungiStoria ($id_albo) {
        $lista_storie = Storia::all();
       
        return view('rel_storia_albo.aggiungi_storia',
            [ 'lista_storie' => $lista_storie,
              'id_albo' => $id_albo
            ]);
    } 

    public function storeStoria (Request $request, $id_albo) {
        /*$request->validate([
            'autore' => 'required | max:511',
            'ruolo' => 'required | max:511',
        ]);*/
        //SALVARE UNA RIGA PER OGNI RUOLO
        DB::beginTransaction();
        try {
            $storia = $request->get('storia');
            $storia_albo = new RelStoriaAlbo();
            $storia_albo->albo_id = $id_albo;
            $storia_albo->storia_id = $storia;
            $storia_albo->save ();
            DB::commit();
            return redirect(route('albo.storia', $id_albo))->with('success', 'Storia aggiunta');
        }
        catch(Exception $e){
            DB::rollBack();
            return redirect(route('albo.index', $id_albo))->with('success', 'Si è verificato un problema. L\'operazione non è stata eseguita.');
        }
    } 

    public function index($id_albo)
    {
        $storie = DB::table('rel_storia_albo')->where('albo_id', '=', $id_albo)
        ->join('storia', 'storia.id', '=', 'rel_storia_albo.storia_id')
        ->get(['storia.id AS storia_id', 'storia.nome']);

        $albo = Albo::find($id_albo);

        return view('rel_storia_albo.storia_albo', [ 
            'albo' => $albo,
            'storie' => $storie
            ]);
    }

    public function eliminaStoriaForm($id_albo, $id_storia)
    {
        return view('rel_storia_albo.elimina_storia_form', [
            'id_albo' => $id_albo,
            'id_storia' => $id_storia
        ]);
    }

    public function eliminaStoriaExecute($id_albo, $id_storia)
    {
        try {
            DB::beginTransaction();

            //GESTIRE LE RELAZIONI COINVOLTE
            
            RelStoriaAlbo::where('albo_id', '=', $id_albo)->where('storia_id', '=', $id_storia)->delete();
            DB::commit();
            return redirect(route('albo.storia', $id_albo))->with('success', 'Storia eliminata');
        }
        catch(Exception $e){
            DB::rollBack();
            return redirect(route('albo.storia', $id_albo))->with('success', 'Si è verificato un problema. L\'operazione non è stata eseguita.');
        }
    }

    public function getStorie($id_albo) {

        $storie = DB::table('rel_storia_albo')->where('albo_id', '=', $id_albo)
        ->join('storia', 'storia.id', '=', 'rel_storia_albo.storia_id')
        ->get(['storia.id AS storia_id', 'storia.nome']);

        $albo = Albo::find($id_albo);
        $table_titolo ="Storie dell'albo: ".$albo->titolo;
        $table_storie = "<ul>";
        foreach ($storie as $storia) {
            $table_storie .= "<li>".$storia->nome."</li>";
        }
        $table_storie .= "</ul>";

        $arr = ['titolo' => $table_titolo, 'storie' => $table_storie];
        return json_encode($arr);
    }
}
