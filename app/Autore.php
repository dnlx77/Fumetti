<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Autore extends Model
{
    //
    protected $table = 'autore';

    public function scopeAutoreSearch($query, $cerca_per, $cerca, $tipo_ricerca) {
        if(!empty($cerca_per) && $cerca_per != 'tutto') {
            switch ($tipo_ricerca) {
                case 'iniziaPer':
                    $query->where($cerca_per, 'LIKE', "{$cerca}%");
                    break;
                case 'contiene':
                    $query->where($cerca_per, 'LIKE', "%{$cerca}%");
                    break;
                case 'esatta':
                    $query->where($cerca_per, '=', $cerca);
                    break;
            }
        }
        return $query;
    }

    public function storie($data_let_iniziale, $data_let_finale, $stato_lettura) {
        $query = $this->belongsToMany(Storia::class, 'rel_storia_autore_ruolo');

        if (!empty($data_let_iniziale)) {
            $query->join('storia_letture', 'storia.id', '=', 'storia_letture.storia_id');
            $query->whereDate('data_lettura', '>=', $data_let_iniziale);
        }

        if(!empty($data_let_finale)) {
            if(empty($data_let_iniziale)) {
                $query->join('storia_letture', 'storia.id', '=', 'storia_letture.storia_id');
                $query->whereDate('data_lettura', '<=', $data_let_finale);
            }
            else 
                $query->whereDate('data_lettura', '<=', $data_let_finale);
        }
        switch ($stato_lettura) {
            case 'leggere':
                $query->doesntHave('dateLettura');
                break;
            case 'letti':
                $query->has('dateLettura');
                break;
            default:
                break;
        }

        return $query;
    }

    public function storie_autori_ruoli($ruoli, $search, $data_let_iniziale, $data_let_finale, $stato_lettura) {
        $query = $this->belongsToMany(Storia::class, 'rel_storia_autore_ruolo')->where('autore_id', '=', $search)->where('ruolo_id', '=', $ruoli);

        if (!empty($data_let_iniziale)) {
            $query->join('storia_letture', 'storia.id', '=', 'storia_letture.storia_id');
            $query->whereDate('data_lettura', '>=', $data_let_iniziale);
        }

        if(!empty($data_let_finale)) {
            if(empty($data_let_iniziale)) {
                $query->join('storia_letture', 'storia.id', '=', 'storia_letture.storia_id');
                $query->whereDate('data_lettura', '<=', $data_let_finale);
            }
            else 
                $query->whereDate('data_lettura', '<=', $data_let_finale);
        }

        switch ($stato_lettura) {
            case 'leggere':
                $query->doesntHave('dateLettura');
                break;
            case 'letti':
                $query->has('dateLettura');
                break;
            default:
                break;
        }

        return $query;
    }
}
