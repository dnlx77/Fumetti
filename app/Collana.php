<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Albo;

class Collana extends Model
{
    //
    protected $table ='collana';

    public function albi($data_pub_iniziale, $data_pub_finale, $stato_lettura) {
        $query = $this->hasMany(Albo::class);

        if (!empty($data_pub_iniziale))
            $query->whereDate('data_pubblicazione', '>=', $data_pub_iniziale);

        if(!empty($data_pub_finale)) 
            $query->whereDate('data_pubblicazione', '<=', $data_pub_finale);

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
