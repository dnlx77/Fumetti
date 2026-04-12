<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Editore extends Model
{
    //
    protected $table = 'editore';

    public function albi() {
        return $this->hasMany(Albo::class);
    }
}
