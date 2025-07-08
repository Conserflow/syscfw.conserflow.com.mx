<?php

namespace App\Automaticos;

use Illuminate\Database\Eloquent\Model;

class Correos extends Model
{
    public $timestamps = false;
    protected $table = "correos_automaticos";

    public function scopeByTipo($query, $tipo)
    {
        return $query->where("tipo", $tipo);
    }
}
