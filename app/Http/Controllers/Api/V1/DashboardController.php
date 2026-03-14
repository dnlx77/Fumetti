<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Albo;
use App\Models\Storia;
use App\Models\Autore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // ==========================================
        // 1. CARDS — Totali generali
        // ==========================================
        $totaleAlbi      = Albo::where('user_id', $userId)->count();
        $totaleStorie    = Storia::count();
        $totaleAutori    = Autore::count();
        $totaleAlbiLetti = Albo::where('user_id', $userId)
            ->has('dateLettura')
            ->count();
        $totaleDaLeggere = $totaleAlbi - $totaleAlbiLetti;

        // ==========================================
        // 2. GRAFICO — Albi per editore (top 8)
        // ==========================================
        $albiPerEditore = Albo::where('albo.user_id', $userId)
            ->join('editore', 'albo.editore_id', '=', 'editore.id')
            ->groupBy('editore.id', 'editore.nome')
            ->orderByDesc('totale')
            ->limit(8)
            ->select('editore.nome', DB::raw('COUNT(albo.id) as totale'))
            ->get();

        // ==========================================
        // 3. GRAFICO — Albi per anno di pubblicazione
        // ==========================================
        $albiPerAnno = Albo::where('user_id', $userId)
            ->whereNotNull('data_pubblicazione')
            ->groupBy('anno')
            ->orderBy('anno')
            ->select(DB::raw('YEAR(data_pubblicazione) as anno'), DB::raw('COUNT(*) as totale'))
            ->get();

        // ==========================================
        // 4. GRAFICO — Storie per stato
        // ==========================================
        $storiePerStato = Storia::groupBy('stato')
            ->select('stato', DB::raw('COUNT(*) as totale'))
            ->get();

        // ==========================================
        // 5. CAROSELLO — Ultimi 20 albi aggiunti
        // ==========================================
        $ultimiAlbi = Albo::where('user_id', $userId)
            ->with(['editore', 'collana'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get(['id', 'titolo', 'numero', 'filename', 'editore_id', 'collana_id', 'created_at']);

        // ==========================================
        // 6. LISTA — Ultimi 10 albi letti
        // ==========================================
        $ultimiLetti = Albo::where('albo.user_id', $userId)
            ->join('albo_letture', function ($join) use ($userId) {
                $join->on('albo.id', '=', 'albo_letture.albo_id')
                     ->where('albo_letture.user_id', '=', $userId);
            })
            ->orderByDesc('albo_letture.data_lettura')
            ->limit(10)
            ->select('albo.id', 'albo.titolo', 'albo.numero', 'albo.filename', 'albo_letture.data_lettura')
            ->get();

        // ==========================================
        // 7. LISTA — 10 albi da leggere (senza letture)
        // ==========================================
        $daLeggere = Albo::where('user_id', $userId)
            ->doesntHave('dateLettura')
            ->with(['editore', 'collana'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['id', 'titolo', 'numero', 'filename', 'editore_id', 'collana_id']);

        // ==========================================
        // RISPOSTA FINALE
        // ==========================================
        return response()->json([
            'success' => true,
            'dati' => [
                'totali' => [
                    'albi'           => $totaleAlbi,
                    'storie'         => $totaleStorie,
                    'autori'         => $totaleAutori,
                    'albi_letti'     => $totaleAlbiLetti,
                    'albi_da_leggere' => $totaleDaLeggere,
                ],
                'albi_per_editore' => $albiPerEditore,
                'albi_per_anno'    => $albiPerAnno,
                'storie_per_stato' => $storiePerStato,
                'ultimi_albi'      => $ultimiAlbi,
                'ultimi_letti'     => $ultimiLetti,
                'da_leggere'       => $daLeggere,
            ]
        ]);
    }
}
