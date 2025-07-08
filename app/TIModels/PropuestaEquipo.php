<?php

namespace App\TIModels;

use Illuminate\Database\Eloquent\Model;

class PropuestaEquipo extends Model
{
    protected $fillable = [
        "fecha",
        "necesidad_especial",
        "tipo",
        "marca",
        "modelo",
        "almacenamiento",
        "procesador",
        "ram",
        "puesto_id",
        "comentarios",
        "accesorios",
        "empleado_registra_id"
    ];
    protected $table = "ti_propuesta_equipo";
    public $timestamps = true;
}
