<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class ExperienciasLaborale extends Model
{
    protected $table = "experiencias_laborales";
    protected $fillable =
    [
        "empresa",
        "fecha_inicio",
        "fecha_termino",
        "jefe_inmediato",
        "referencia",
        "tel_referencia",
        "puesto",
        "actividades",
    ];
}
