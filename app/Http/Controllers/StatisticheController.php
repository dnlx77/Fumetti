<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Albo;
use App\Autore;
use App\Storia;
use App\Editore;
use App\Collana;
use Illuminate\Support\Facades\DB;

class StatisticheController extends Controller
{

    public function index() {
        return view('statistiche.index');
    }

    public function generali() {

        $num_albi = Albo::all()->count();
        $num_autori = Autore::all()->count();
        $num_storie = Storia::all()->count();
        $num_editori = Editore::all()->count();
        $num_collane = Collana::all()->count();
        $num_storie_lette = DB::table('storia_letture')->select('storia_id')->distinct()->count();
        $num_albi_letti = DB::table('albo_letture')->select('albo_id')->distinct()->count();
        
        $statistiche = ['albi' => $num_albi,
        'albi letti' => $num_albi_letti, 
        'storie' => $num_storie,
        'storie lette' => $num_storie_lette,
        'autori' => $num_autori,
        'editori' => $num_editori, 
        'collane' => $num_collane,
        ];
        
        return view('statistiche.generali', [
            'statistiche' => $statistiche,
        ]);
    }

    /* Dato l'anno ricevuto per parametro calcola il numero di albi pubblicati in ogni mese dell'anno */

    public function albiPerMese($anno) {
        $primo_anno = date("Y", strtotime(Albo::min('data_pubblicazione')));
        $ultimo_anno = date("Y", strtotime(Albo::max('data_pubblicazione')));
        $num_albi_anno = Albo::AlbiPubblicatiAnno($anno)->count();

        $num_albi_per_mese = [];
        for ($i=1; $i<13; $i++) 
            $num_albi_per_mese[date('F', mktime(0, 0, 0, $i, 1))] = Albo::AlbiPubblicatiMeseAnno($i,$anno)->count();

        $sort_by = 'data_pubblicazione';
        $order = 'asc';
        $per_page = 10;
        
        $albi = Albo::AlbiPubblicatiAnno($anno)->orderBy($sort_by, $order)->paginate($per_page);
            
        return view('statistiche.albi_per_mese', [
            'albi' => $albi,
            'num_albi_per_mese' => $num_albi_per_mese,
            'primo_anno' => $primo_anno,
            'ultimo_anno' => $ultimo_anno,
            'num_albi_anno' => $num_albi_anno,
        ]);
    }

    public function albiPerAnno() {
        $primo_anno = date("Y", strtotime(Albo::min('data_pubblicazione')));
        $ultimo_anno = date("Y", strtotime(Albo::max('data_pubblicazione')));

        $num_albi_per_anno = [];
        for($i=$primo_anno; $i<=$ultimo_anno; $i++) 
           $num_albi_per_anno[$i] = Albo::AlbiPubblicatiAnno($i)->count();

            return view('statistiche.albi_per_anno', [
                'primo_anno' => $primo_anno,
                'ultimo_anno' => $ultimo_anno,
                'num_albi_per_anno' => $num_albi_per_anno
            ]);
    }

    public function getAnni() {
        $primo_anno = date("Y", strtotime(Albo::min('data_pubblicazione')));
        $ultimo_anno = date("Y", strtotime(Albo::max('data_pubblicazione')));

        for($i=$primo_anno; $i<=$ultimo_anno; $i++) {
            $num_albi_per_anno[$i] = Albo::AlbiPubblicatiAnno($i)->count();
        }
        return json_encode($num_albi_per_anno);
    }

    public function listaAlbiMeseAnno($mese, $anno) {
        $sort_by = 'data_pubblicazione';
        $order = 'asc';
        $per_page = 10;

        $albi = Albo::AlbiPubblicatiMeseAnno($mese, $anno)->orderBy($sort_by, $order)->paginate($per_page);

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
}
