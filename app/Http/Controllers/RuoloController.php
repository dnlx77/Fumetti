<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RuoloController extends Controller
{
    //
    public function create(){
        return view('ruolo.create');
    } //

    public function store(Request $request) {
        $ruolo = new Ruolo();
        $ruolo->descrizione = $request->get('descrizione');
        $ruolo->save();
        return redirect(route('ruolo.index'))->with('success', 'Il ruolo è stato salvato.');
    }

    public function index(Request $request)
    {
        /* 
        se l'index è stato chiamato dal form di ricerca ed è stata inserita una stringa nel form 
        la recuperiamo dalla request e la inseriamo nella variabile $scope_search, altrimenti se la form è vuota
        o se l'index non è stato chiamato dal form di ricerca $scope_search contiene la stringa vuota
        Quando chiamiamo il metodo search su fumetti viene richiamato in automatico il metodo scopeSearch definito nel model
        */
        
        $ruoli = Ruolo::all();

        return view('ruolo.index', 
            [ 'ruolo' => $ruoli ], 
            );
    }
}