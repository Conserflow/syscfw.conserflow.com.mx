<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class VacacionesEmpleados extends Model
{
    protected $fillable = [
        "empleado_id",
        "contrato_id",
        "fecha_inicio",
        "fecha_fin",
        "dias_a_tomar",
        "empleado_registra_id",
        "condicion"
    ];
    protected $table = 'rh_vacaciones_empleados';
    public $timestamps = true;
}
