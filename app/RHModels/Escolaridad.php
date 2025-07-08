<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class Escolaridad extends Model
{
    protected $table = "escolaridades";
    protected $fillable = [
        "grado_id",
        "fecha_inicio",
        "fecha_termino",
        "titulo",
        "cedula_prof"
    ];
}
