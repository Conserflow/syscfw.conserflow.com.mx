<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class CuestionarioInfra extends Model
{
    protected $fillable = [
        "empleado_id",
		"fecha"
    ];
    protected $table = "rh_cuestionario_infra";
    public $timestamps = true;
}

    