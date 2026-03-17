<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Albo;
use App\Models\Storia;
use App\Models\Autore;
use App\Models\Editore;
use App\Models\Collana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticheController extends Controller
{
    /**
     * Restituisce tutte le statistiche statiche (senza heatmap)
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // ==========================================
        // PANORAMICA
        // ==========================================
        $totaleAlbi      = Albo::where('user_id', $userId)->count();
        $totaleStorie    = Storia::count();
        $totaleAutori    = Autore::count();
        $totaleEditori   = Editore::count();
        $totaleCollane   = Collana::count();
        $albiLetti       = Albo::where('user_id', $userId)->has('dateLettura')->count();
        $albidaLeggere   = $totaleAlbi - $albiLetti;
        $percentualeLetti = $totaleAlbi > 0
            ? round(($albiLetti / $totaleAlbi) * 100)
            : 0;

        // ==========================================
        // COLLEZIONE
        // ==========================================
        $perEditore = Albo::where('albo.user_id', $userId)
            ->join('editore', 'albo.editore_id', '=', 'editore.id')
            ->groupBy('editore.id', 'editore.nome')
            ->orderByDesc('totale')
            ->limit(10)
            ->select('editore.nome', DB::raw('COUNT(albo.id) as totale'))
            ->get();

        $perCollana = Albo::where('albo.user_id', $userId)
            ->join('collana', 'albo.collana_id', '=', 'collana.id')
            ->groupBy('collana.id', 'collana.nome')
            ->orderByDesc('totale')
            ->limit(10)
            ->select('collana.nome', DB::raw('COUNT(albo.id) as totale'))
            ->get();

        $perAnnoPubblicazione = Albo::where('user_id', $userId)
            ->whereNotNull('data_pubblicazione')
            ->groupBy('anno')
            ->orderBy('anno')
            ->select(DB::raw('YEAR(data_pubblicazione) as anno'), DB::raw('COUNT(*) as totale'))
            ->get();

        // ==========================================
        // LETTURE
        // ==========================================
        $letturePerMese = DB::table('albo_letture')
            ->where('user_id', $userId)
            ->groupBy('anno', 'mese')
            ->orderBy('anno')
            ->orderBy('mese')
            ->select(
                DB::raw('YEAR(data_lettura) as anno'),
                DB::raw('MONTH(data_lettura) as mese'),
                DB::raw('COUNT(*) as totale')
            )
            ->get();

        $letturePerAnno = DB::table('albo_letture')
            ->where('user_id', $userId)
            ->groupBy('anno')
            ->orderBy('anno')
            ->select(
                DB::raw('YEAR(data_lettura) as anno'),
                DB::raw('COUNT(*) as totale')
            )
            ->get();

        // Streak — calcoliamo giorni consecutivi
        $streak = $this->calcolaStreak($userId);

        // Media mensile letture
        $mediaMensile = $this->calcolaMediaMensile($userId);

        // Prima data di lettura presente nel DB (albi)
        $primaDataLettura = DB::table('albo_letture')
            ->where('user_id', $userId)
            ->min('data_lettura');

        // Se non ci sono letture albi, controlla le storie
        if (!$primaDataLettura) {
            $primaDataLettura = DB::table('storia_letture')
                ->where('user_id', $userId)
                ->min('data_lettura');
        }

        // ==========================================
        // STORIE
        // ==========================================
        $storiePerStato = Storia::groupBy('stato')
            ->select('stato', DB::raw('COUNT(*) as totale'))
            ->get();

        $storieLette = DB::table('storia_letture')
            ->where('user_id', $userId)
            ->distinct('storia_id')
            ->count('storia_id');

        $storieDaLeggere = $totaleStorie - $storieLette;

        // ==========================================
        // TOP AUTORI
        // ==========================================
        $topAutoriStorie = DB::table('rel_storia_autore_ruolo')
            ->join('autore', 'rel_storia_autore_ruolo.autore_id', '=', 'autore.id')
            ->groupBy('autore.id', 'autore.cognome', 'autore.nome', 'autore.pseudonimo')
            ->orderByDesc('totale')
            ->limit(10)
            ->select(
                'autore.id',
                'autore.cognome',
                'autore.nome',
                'autore.pseudonimo',
                DB::raw('COUNT(DISTINCT rel_storia_autore_ruolo.storia_id) as totale')
            )
            ->get();

        $topAutoriAlbi = DB::table('rel_albo_autoricopertina')
            ->join('autore', 'rel_albo_autoricopertina.autore_id', '=', 'autore.id')
            ->join('albo', 'rel_albo_autoricopertina.albo_id', '=', 'albo.id')
            ->where('albo.user_id', $userId)
            ->groupBy('autore.id', 'autore.cognome', 'autore.nome', 'autore.pseudonimo')
            ->orderByDesc('totale')
            ->limit(10)
            ->select(
                'autore.id',
                'autore.cognome',
                'autore.nome',
                'autore.pseudonimo',
                DB::raw('COUNT(rel_albo_autoricopertina.albo_id) as totale')
            )
            ->get();

        // ==========================================
        // CURIOSITÀ
        // ==========================================
        $alboPiuVecchio = Albo::where('user_id', $userId)
            ->whereNotNull('data_pubblicazione')
            ->with(['editore', 'collana'])
            ->orderBy('data_pubblicazione')
            ->first(['id', 'titolo', 'numero', 'filename', 'data_pubblicazione', 'editore_id', 'collana_id']);

        $alboPiuRecente = Albo::where('user_id', $userId)
            ->whereNotNull('data_pubblicazione')
            ->with(['editore', 'collana'])
            ->orderByDesc('data_pubblicazione')
            ->first(['id', 'titolo', 'numero', 'filename', 'data_pubblicazione', 'editore_id', 'collana_id']);

        $prezzoMedioEuro = Albo::where('user_id', $userId)
            ->whereNotNull('prezzo')
            ->avg('prezzo');

        $prezzoMedioLire = Albo::where('user_id', $userId)
            ->whereNotNull('prezzo_lire')
            ->avg('prezzo_lire');

        $collanaPiuGrande = Albo::where('albo.user_id', $userId)
            ->join('collana', 'albo.collana_id', '=', 'collana.id')
            ->groupBy('collana.id', 'collana.nome')
            ->orderByDesc('totale')
            ->select('collana.id', 'collana.nome', DB::raw('COUNT(albo.id) as totale'))
            ->first();

        $mesepiuProduttivo = DB::table('albo_letture')
            ->where('user_id', $userId)
            ->groupBy('anno', 'mese')
            ->orderByDesc('totale')
            ->select(
                DB::raw('YEAR(data_lettura) as anno'),
                DB::raw('MONTH(data_lettura) as mese'),
                DB::raw('COUNT(*) as totale')
            )
            ->first();

        return response()->json([
            'success' => true,
            'dati' => [
                'panoramica' => [
                    'totale_albi'        => $totaleAlbi,
                    'totale_storie'      => $totaleStorie,
                    'totale_autori'      => $totaleAutori,
                    'totale_editori'     => $totaleEditori,
                    'totale_collane'     => $totaleCollane,
                    'albi_letti'         => $albiLetti,
                    'albi_da_leggere'    => $albidaLeggere,
                    'percentuale_letti'  => $percentualeLetti,
                ],
                'collezione' => [
                    'per_editore'           => $perEditore,
                    'per_collana'           => $perCollana,
                    'per_anno_pubblicazione' => $perAnnoPubblicazione,
                ],
                'letture' => [
                    'per_mese'      => $letturePerMese,
                    'per_anno'      => $letturePerAnno,
                    'streak_attuale' => $streak['attuale'],
                    'streak_massima' => $streak['massima'],
                    'media_mensile'  => round($mediaMensile, 1),
                    'prima_data'    => $primaDataLettura,
                ],
                'storie' => [
                    'per_stato'       => $storiePerStato,
                    'storie_lette'    => $storieLette,
                    'storie_da_leggere' => $storieDaLeggere,
                ],
                'top_autori' => [
                    'per_storie'          => $topAutoriStorie,
                    'per_albi_copertina'  => $topAutoriAlbi,
                ],
                'curiosita' => [
                    'albo_piu_vecchio'    => $alboPiuVecchio,
                    'albo_piu_recente'    => $alboPiuRecente,
                    'prezzo_medio_euro'   => $prezzoMedioEuro ? round($prezzoMedioEuro, 2) : null,
                    'prezzo_medio_lire'   => $prezzoMedioLire ? round($prezzoMedioLire) : null,
                    'collana_piu_grande'  => $collanaPiuGrande,
                    'mese_piu_produttivo' => $mesepiuProduttivo,
                ],
            ]
        ]);
    }

    /**
     * Heatmap letture albi per range di date
     */
    public function heatmapAlbi(Request $request)
    {
        $userId = $request->user()->id;
        $da = $request->query('da', now()->startOfYear()->format('Y-m-d'));
        $a  = $request->query('a', now()->format('Y-m-d'));

        $dati = DB::table('albo_letture')
            ->where('user_id', $userId)
            ->whereBetween('data_lettura', [$da, $a])
            ->groupBy('data_lettura')
            ->orderBy('data_lettura')
            ->select(DB::raw('DATE(data_lettura) as data'), DB::raw('COUNT(*) as totale'))
            ->get();

        return response()->json(['success' => true, 'dati' => $dati]);
    }

    /**
     * Heatmap letture storie per range di date
     */
    public function heatmapStorie(Request $request)
    {
        $userId = $request->user()->id;
        $da = $request->query('da', now()->startOfYear()->format('Y-m-d'));
        $a  = $request->query('a', now()->format('Y-m-d'));

        $dati = DB::table('storia_letture')
            ->where('user_id', $userId)
            ->whereBetween('data_lettura', [$da, $a])
            ->groupBy('data_lettura')
            ->orderBy('data_lettura')
            ->select(DB::raw('DATE(data_lettura) as data'), DB::raw('COUNT(*) as totale'))
            ->get();

        return response()->json(['success' => true, 'dati' => $dati]);
    }

    /**
     * Calcola streak attuale e massima
     */
    private function calcolaStreak(int $userId): array
    {
        $date = DB::table('albo_letture')
            ->where('user_id', $userId)
            ->distinct()
            ->orderBy('data_lettura')
            ->pluck('data_lettura')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
            ->unique()
            ->values();

        if ($date->isEmpty()) return ['attuale' => 0, 'massima' => 0];

        $streakAttuale = 0;
        $streakMassima = 0;
        $corrente = 1;
        $oggi = \Carbon\Carbon::today()->toDateString();
        $ieri = \Carbon\Carbon::yesterday()->toDateString();

        for ($i = 1; $i < $date->count(); $i++) {
            $diff = \Carbon\Carbon::parse($date[$i])->diffInDays(\Carbon\Carbon::parse($date[$i - 1]));
            if ($diff === 1) {
                $corrente++;
            } else {
                $streakMassima = max($streakMassima, $corrente);
                $corrente = 1;
            }
        }
        $streakMassima = max($streakMassima, $corrente);

        // Streak attuale: solo se l'ultima lettura è oggi o ieri
        $ultimaData = $date->last();
        if ($ultimaData === $oggi || $ultimaData === $ieri) {
            $streakAttuale = $corrente;
        }

        return ['attuale' => $streakAttuale, 'massima' => $streakMassima];
    }

    /**
     * Calcola media mensile letture
     */
    private function calcolaMediaMensile(int $userId): float
    {
        $mesi = DB::table('albo_letture')
            ->where('user_id', $userId)
            ->groupBy('anno', 'mese')
            ->select(
                DB::raw('YEAR(data_lettura) as anno'),
                DB::raw('MONTH(data_lettura) as mese'),
                DB::raw('COUNT(*) as totale')
            )
            ->get();

        if ($mesi->isEmpty()) return 0;
        return $mesi->avg('totale');
    }
}
