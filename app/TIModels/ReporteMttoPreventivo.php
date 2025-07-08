<?php

namespace App\TiModels;

use Illuminate\Database\Eloquent\Model;

class ReporteMttoPreventivo extends Model
{
    protected $fillable = [
        "fecha",
        "equipo_id",
        "tipo_equipo",
        "hora_inicio",
        "hora_final",
        "empleado_asignado",
        "empleado_autoriza",
        "actividades",
        "observaciones"
    ];
    protected $table = 'ti_mtto_preventivo';
    public $timestamps = true;
}
