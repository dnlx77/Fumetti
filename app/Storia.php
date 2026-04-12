<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Storia extends Model
{
    //
    protected $table = 'storia';

    /*I local scope ci permettono di definire una serie di vincoli riusabili
    per far ciò si fa precedre la keyword scope al nome della funzione poi la funzione sarà richiamabile soltanto
    con il nome senza scope
    $query contiene instanze della classe Storia il cui titolo contiene la stringa $string
    Il secondo parametro e l'argomento che passiamo alla funzione
    */
    public function scopeSearch($query, $string) {
        if(!empty($string)){
			$query->where('nome', 'LIKE', "%{$string}%");
        }
        return $query;
    }

    public function dateLettura() {
        return $this->hasMany(StoriaLetture::class);
    }

    public function scopeStoriaSearch($query, $cerca_per, $cerca, $tipo_ricerca, $data_let_iniziale, $data_let_finale, $stato_lettura) {

        if(!empty($cerca_per)) {
            
            if ($cerca_per != 'tutto') {
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
        }

        return $query;
    }

    public function albi() {
        return $this->belongsToMany(Albo::class, 'rel_storia_albo');
    }

    public function autori() {
        return $this->belongsToMany(Autore::class, 'rel_storia_autore_ruolo');
    }
}
