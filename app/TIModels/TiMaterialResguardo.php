<?php

namespace App\TIModels;

use Illuminate\Database\Eloquent\Model;

class TiMaterialResguardo extends Model
{
    protected $table = 'ti_material_resguardo';
    protected $fillable = [
        "fecha",
        "caiv",
        "tipo",
        "observacion_uno",
        "observacion_dos",
        "empleado_entrega",
        "empleado_recibe",
        "cantidad",
        "empresa"
    ];
}
