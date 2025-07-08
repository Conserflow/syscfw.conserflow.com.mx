<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class FactorRiesgo extends Model
{
    protected $fillable = [
        "empleado_id",
        "puesto_id",
		"fecha"
    ];
    protected $table = "rh_factores_riesgo";
    public $timestamps = true;
}

    