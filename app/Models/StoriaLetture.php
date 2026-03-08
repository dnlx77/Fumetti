<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoriaLetture extends Model
{
    protected $table = 'storia_letture';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function storia()
    {
        return $this->belongsTo(Storia::class);
    }
}
