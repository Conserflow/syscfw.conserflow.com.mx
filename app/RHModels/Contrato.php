<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $fillable = [
        "tipo_ubicacion_id",
        "fecha_ingreso",
        "fecha_fin",
        "tipo_nomina_id",
        "empleado_id",
        "horario_id",
        "empresa_id",
        "tipo_contrato_id",
        "proyecto_id",
        "puesto_id",
        "testigo1_id",
        "testigo2_id"
    ];
    protected $table = "contratos";
    public $timestamps = true;
}
