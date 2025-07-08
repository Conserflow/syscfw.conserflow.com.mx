<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class PeriodoVacaciones extends Model
{
    protected $table = "rh_vacaciones_periodos";
    public $timestamps = false;

    public function scopeByEmpleado($query, $empleado_id)
    {
        return $query->where("empleado_id", $empleado_id);
    }

    public function scopeByEmpleadoPeriodo($query, $empleado_id, $periodo)
    {
        return $query->where("empleado_id", $empleado_id)
            ->where("periodo", $periodo);
    }
}
