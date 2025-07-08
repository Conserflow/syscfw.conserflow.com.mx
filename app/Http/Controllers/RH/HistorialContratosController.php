<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Support\Facades\DB;

class HistorialContratosController extends Controller
{
    /**
     * Obtiene el historial del emplado seleccionado
     */
    public function ObtenerHistorial($emp_id)
    {
        try
        {
            $historial = DB::table("rh_historial_contratos as rhc")
                ->where("rhc.empleado_id", $emp_id)
                ->get();
            return  Status::Success("historial", $historial);
        }
        catch (Exception $e)
        {
            dd($e);
            return Status::Error($e, "obtener el historial del empleado");
        }
    }
}
