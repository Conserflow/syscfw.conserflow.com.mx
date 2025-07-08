<?php

namespace App\EnfermeriaModels;

use Illuminate\Database\Eloquent\Model;

class RegistroCovid extends Model
{
    protected $fillable = [
        "empleado_id",
		"puesto_id",
		"diagnostico",
		"inicio_sintomas",
		"fecha_deteccion",
		"inicio_incapacidad",
		"dias_incapacidad",
		"termino_incapacidad",
		"prueba"
    ];
    protected $table = "enfermeria_registros_covid";
    public $timestamps = true;
}
    