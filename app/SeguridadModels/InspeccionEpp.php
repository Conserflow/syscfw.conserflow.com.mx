<?php

namespace App\SeguridadModels;

use Illuminate\Database\Eloquent\Model;

class InspeccionEpp extends Model
{
    protected $table = "seguridad_inspeccion_epp";
    public $fillable = [
        "ubicacion",
        "fecha",
        "observaciones",
        "empleado_realiza_id",
        "empleado_revisa_id",
        "empleado_registra_id"
    ];
    public $timestamps = true;
}
