<?php

namespace App\Http\Controllers\Enfermeria;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Support\Facades\DB;

class DatosEmpleadosController extends Controller
{

    /**
     * Obtener los datos del seguro de todos los empleado activos
     */
    public function ObtenerEmpleados()
    {
        try
        {
            // Obtener empleados activos
            $empleados = DB::table("empleados as e")
                ->where("e.condicion", 1)
                ->select(
                    "e.id",
                    DB::raw("concat_Ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre"),
                    DB::raw("' ' as puesto"),
                    "e.curp",
                    "e.nss_imss",
                    "e.rfc"
                )
                ->orderBy("nombre")
                ->get();

            // Obtener el puesto (primero activo)
            foreach ($empleados as $e)
            {
                $contrato = DB::table("contratos as c")
                    ->leftJoin("puestos as p", "p.id", "c.puesto_id")
                    ->select(
                        "p.nombre"
                    )
                    ->where("c.empleado_id", $e->id)
                    ->where("c.condicion", 1)
                    ->first();
                $puesto = $contrato == null ? "N/D" : $contrato->nombre;
                $e->puesto = $puesto;
            }
            return Status::Success("empleados", $empleados);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los empleados");
        }
    }
}
