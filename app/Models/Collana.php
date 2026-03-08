<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collana extends Model
{
    protected $table = 'collana';
    protected $guarded = [];

    // Relazione standard (pulita per le API)
    public function albi()
    {
        return $this->hasMany(Albo::class);
    }

    // La tua vecchia funzione di ricerca, rinominata per non fare conflitto
    public function albiFiltrati($data_pub_iniziale, $data_pub_finale, $stato_lettura)
    {
        $query = $this->hasMany(Albo::class);

        if (!empty($data_pub_iniziale))
            $query->whereDate('data_pubblicazione', '>=', $data_pub_iniziale);

        if (!empty($data_pub_finale))
            $query->whereDate('data_pubblicazione', '<=', $data_pub_finale);

        switch ($stato_lettura) {
            case 'leggere':
                $query->doesntHave('dateLettura');
                break;
            case 'letti':
                $query->has('dateLettura');
                break;
        }

        return $query;
    }
}
