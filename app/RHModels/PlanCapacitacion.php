<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class PlanCapacitacion extends Model
{
    protected $fillable = [
        "anio",
        "empleado_elabora_id",
        "empleado_autoriza_id",
        "empleado_registra_id",
        "fecha_elabora",
        "condicion",
        "fecha_autoriza"
    ];
    protected $table = 'rh_planes_capacitacion';
    public $timestamps = true;
}
