<?php

namespace App\RequisicionModels;

use App\RHModels\Empleado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PersonaAprueba extends Model
{
    protected $table = "requis_personal_aprueba";

    /******************************
     * RELACIONES
     *******************************/
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, "empleado_id", "id");
    }

    /******************************
     * SCOPES
     *******************************/
    public function scopeActivos($query)
    {
        $query->from("requis_personal_aprueba as rpa")
            ->join("empleados as e", "e.id", "rpa.empleado_id")
            ->where("e.condicion", 1)
            ->select(
                DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre"),
                "e.id"
            )
            ->orderBy("nombre");
    }
}
