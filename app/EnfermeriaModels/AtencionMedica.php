<?php

namespace App\EnfermeriaModels;

use Illuminate\Database\Eloquent\Model;

class AtencionMedica extends Model
{
    protected $fillable = [
        "empleado_id",
        "tipo",
        "fecha",
        "motivo_id",
        "puesto_id",
        "medicamentos"
    ];
    protected $table = "enfermeria_atencion_medica";
}
