<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    protected $fillable = [
        "empleado_id",
        "nombre",
        "curp",
        "nss",
        "fecha_alta",
        "fecha_baja",
        "puesto_id",
        "salario_neto",
        "proyecto_id",
        "empleado_registra_id"
    ];
    protected $table = "rh_historial_contratos";
    public $timestamps = true;
}
