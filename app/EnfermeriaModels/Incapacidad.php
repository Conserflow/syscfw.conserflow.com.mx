<?php

namespace App\EnfermeriaModels;

use Illuminate\Database\Eloquent\Model;

class Incapacidad extends Model
{
    protected $fillable = [
        "empleado_id",
        "puesto_id",
		"total_dias",
		"fecha_inicio",
		"fecha_termino",
		"subsecuente",
		"tipo",
		"causa",
		"dias_incapacidad",
		"estado"
    ];
    protected $table = "enfermeria_incapacidades";
    public $timestamps = true;
}   