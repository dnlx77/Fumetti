<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Albo extends Model
{
    //
    protected $table = 'albo';

    public function collana() {
        return $this->belongsTo(Collana::class, 'collana_id');
    }

    public function editore() {
        return $this->belongsTo(Editore::class, 'editore_id');
    }

    public function storie() {
        return $this->belongsToMany(Storia::class, 'rel_storia_albo');
    }

    public function autoriCopertina() {
        return $this->belongsToMany(Autore::class, 'rel_albo_autoricopertina');
    }

    public function dateLettura() {
        return $this->hasMany(AlboLetture::class);
    }

    public function scopeGetAlbo ($query, $albo_id) {
        return $query->where('id',$albo_id);
    }

    public function scopeNumAlbiInCollana ($query, $collana_id) {
        return $query->where('collana_id', '=', $collana_id)->count();
    }

    public function scopeNumAlbiPerEditore ($query, $editore_id) {
        return $query->where('editore_id', '=', $editore_id)->count();
    }

    public function scopeNumAlbi ($query) {
        return $query->where('id', '=', '*')->count();
    }


    public function scopeAlboSearch($query, $cerca_per, $cerca, $tipo_ricerca, $data_pub_iniziale, $data_pub_finale, $stato_lettura) {
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

            if (!empty($data_pub_iniziale))
                $query->whereDate('data_pubblicazione', '>=', $data_pub_iniziale);

            if(!empty($data_pub_finale)) 
                $query->whereDate('data_pubblicazione', '<=', $data_pub_finale);
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

    public function scopeAlbiPubblicatiAnno($query, $anno_pub) {
        return $query->whereYear('data_pubblicazione',$anno_pub);
    }

    public function scopeAlbiPubblicatiMeseAnno($query, $mese_pub, $anno_pub) {
        $query->whereYear('data_pubblicazione', $anno_pub);
        return $query->whereMonth('data_pubblicazione', $mese_pub);
    }
}
