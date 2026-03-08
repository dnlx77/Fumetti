<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlboLetture extends Model
{
    protected $table = 'albo_letture';
    protected $guarded = [];

    // Relazione: Questa lettura appartiene a un utente
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relazione: Questa lettura riguarda un albo specifico
    public function albo()
    {
        return $this->belongsTo(Albo::class);
    }
}
