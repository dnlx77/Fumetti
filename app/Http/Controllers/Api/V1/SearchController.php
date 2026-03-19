<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Albo;
use App\Models\Storia;
use App\Models\Autore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Ricerca albi
     * Parametri: q, campo (titolo|editore|collana|autore|anno|barcode), tipo (contiene|inizia|esatta), letto (0|1|tutti), page
     */
    public function albi(Request $request)
    {
        $userId = $request->user()->id;
        $q      = $request->query('q', '');
        $campo  = $request->query('campo', 'titolo');
        $tipo   = $request->query('tipo', 'contiene');
        $letto  = $request->query('letto', 'tutti');
        $page   = $request->query('page', 1);
        $dal    = $request->query('dal');
        $al     = $request->query('al');

        $query = Albo::where('albo.user_id', $userId)
            ->with(['editore', 'collana', 'autoriCopertina', 'storie']);

        // Filtro lettura
        if ($letto === '1') {
            $query->has('dateLettura');
        } elseif ($letto === '0') {
            $query->doesntHave('dateLettura');
        }

        // Filtro ricerca testo
        if (!empty($q)) {
            $pattern = $this->buildPattern($q, $tipo);

            switch ($campo) {
                case 'titolo':
                    $query->where('albo.titolo', 'LIKE', $pattern);
                    break;
                case 'editore':
                    $query->join('editore', 'albo.editore_id', '=', 'editore.id')
                          ->where('editore.nome', 'LIKE', $pattern);
                    break;
                case 'collana':
                    $query->join('collana', 'albo.collana_id', '=', 'collana.id')
                          ->where('collana.nome', 'LIKE', $pattern);
                    break;
                case 'autore':
                    $query->whereHas('autoriCopertina', function ($q) use ($pattern) {
                        $q->where(DB::raw("CONCAT(autore.cognome, ' ', autore.nome)"), 'LIKE', $pattern)
                          ->orWhere('autore.cognome', 'LIKE', $pattern)
                          ->orWhere('autore.nome', 'LIKE', $pattern)
                          ->orWhere('autore.pseudonimo', 'LIKE', $pattern);
                    });
                    break;
                case 'anno':
                    $query->whereYear('data_pubblicazione', $q);
                    break;
                case 'barcode':
                    $query->where('albo.barcode', 'LIKE', $pattern);
                    break;
            }
        }

        if ($dal) {
            $query->whereDate('data_pubblicazione', '>=', $dal);
        }
        if ($al) {
            $query->whereDate('data_pubblicazione', '<=', $al);
        }

        $risultati = $query->select('albo.*')
            ->orderBy('albo.data_pubblicazione', 'desc')
            ->paginate(20, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'dati'    => $risultati
        ]);
    }

    /**
     * Ricerca storie
     * Parametri: q, campo (nome|trama|autore|stato), tipo, letto, page
     */
    public function storie(Request $request)
    {
        $userId = $request->user()->id;
        $q      = $request->query('q', '');
        $campo  = $request->query('campo', 'nome');
        $tipo   = $request->query('tipo', 'contiene');
        $letto  = $request->query('letto', 'tutti');
        $page   = $request->query('page', 1);

        $query = Storia::with(['autori']);

        // Filtro lettura
        if ($letto === '1') {
            $query->whereHas('dateLettura', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        } elseif ($letto === '0') {
            $query->whereDoesntHave('dateLettura', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        // Filtro ricerca testo
        if (!empty($q)) {
            $pattern = $this->buildPattern($q, $tipo);

            switch ($campo) {
                case 'nome':
                    $query->where('storia.nome', 'LIKE', $pattern);
                    break;
                case 'trama':
                    $query->where('storia.trama', 'LIKE', $pattern);
                    break;
                case 'autore':
                    $query->whereHas('autori', function ($q) use ($pattern) {
                        $q->where(DB::raw("CONCAT(autore.cognome, ' ', autore.nome)"), 'LIKE', $pattern)
                          ->orWhere('autore.cognome', 'LIKE', $pattern)
                          ->orWhere('autore.nome', 'LIKE', $pattern)
                          ->orWhere('autore.pseudonimo', 'LIKE', $pattern);
                    });
                    break;
                case 'stato':
                    $query->where('storia.stato', $q);
                    break;
            }
        }

        $risultati = $query->orderBy('nome')->paginate(20, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'dati'    => $risultati
        ]);
    }

    /**
     * Ricerca autori
     * Parametri: q, campo (cognome|nome|pseudonimo|tutti), tipo, page
     */
    public function autori(Request $request)
    {
        $q     = $request->query('q', '');
        $campo = $request->query('campo', 'cognome');
        $tipo  = $request->query('tipo', 'contiene');
        $page  = $request->query('page', 1);

        $query = Autore::query();

        if (!empty($q)) {
            $pattern = $this->buildPattern($q, $tipo);

            if ($campo === 'tutti') {
                $query->where(function ($q) use ($pattern) {
                    $q->where('cognome', 'LIKE', $pattern)
                      ->orWhere('nome', 'LIKE', $pattern)
                      ->orWhere('pseudonimo', 'LIKE', $pattern);
                });
            } else {
                $query->where($campo, 'LIKE', $pattern);
            }
        }

        $risultati = $query->orderBy('cognome')->paginate(20, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'dati'    => $risultati
        ]);
    }

    /**
     * Costruisce il pattern LIKE in base al tipo di ricerca
     */
    private function buildPattern(string $q, string $tipo): string
    {
        return match($tipo) {
            'inizia'  => "{$q}%",
            'esatta'  => $q,
            default   => "%{$q}%",  // contiene
        };
    }
}
