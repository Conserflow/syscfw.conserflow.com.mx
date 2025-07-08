<?php

namespace App\SeguridadModels;

use Illuminate\Database\Eloquent\Model;

class Platica extends Model
{
    protected $table = "seguridad_platicas";
    public $fillable = [
        "ubicacion",
        "fecha",
        "tema",
        "responsable_id",
        "empleado_registra_id"
    ];
    public $timestamps = true;
}
