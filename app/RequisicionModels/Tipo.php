<?php

namespace App\RequisicionModels;

use App\RHModels\Empleado;
use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    public static $ALIMENTOS = "alimentos";
    public static $COMBUSTIBLE = "combustible";
    public static $DC3 = "dc3";
    public static $FLETES = "fletes";
    public static $HOSPEDAJE = "hospedaje";
    public static $REEMBOLSOS = "reembolsos";
    public static $MATERIALES = "requisiciones-materiales";

    protected $table = "requisiciones_tipos";

    /************* RELACIONES *************/

    public function aprueba()
    {
        return $this->belongsTo(Empleado::class, 'empleado_aprueba_id');
    }

    /************* SCOPES *************/
}
