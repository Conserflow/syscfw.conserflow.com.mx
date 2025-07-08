<?php

namespace App\CalidadModels;

use Illuminate\Database\Eloquent\Model;

class Calibraciones extends Model
{
    protected $fillable = [
        "fecha_servicio",
        "proxima_fecha",
    ];
    protected $table = "calidad_calibraciones";
}
