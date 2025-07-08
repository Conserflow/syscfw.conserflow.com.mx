<?php

namespace App\CalidadModels;

use Illuminate\Database\Eloquent\Model;

class Equipos extends Model
{
    protected $fillable = [
        "equipo",
		"marca",
		"modelo",
		"ns",
		"tipo",
		"rango_medicion",
		"resguardo",
		"frecuencia",
		"empleado_revisa_id",
		"observaciones",
		"tipo"
    ];
    protected $table = "calidad_equipos_calibracion";
}
    