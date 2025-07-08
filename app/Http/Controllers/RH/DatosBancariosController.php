<?php

namespace App\Http\Controllers\RH;

use App\Exports\RH\DatosBacariosExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DatosBancariosController extends Controller
{
    /**
     * Obtener los empleados activos con sus datos bancarios
     */
    public function ObtenerDatos()
    {
        try
        {
            $empledos = DB::table("empleados as e")
                ->join("datos_bancarios_empleados as dbe", "e.id", "dbe.empleado_id")
                ->join("catalogo_bancos as cb", "cb.id", "dbe.banco_id")
                ->select(
                    "e.nombre",
                    "e.ap_paterno",
                    "e.ap_materno",
                    "cb.nombre as banco",
                    "dbe.numero_cuenta as cuenta",
                    "dbe.clabe",
                    "dbe.numero_tarjeta as tarjeta"
                )
                ->where("e.condicion", 1)
                ->where("dbe.condicion", 1)
                ->orderBy("nombre")
                ->orderBy("ap_paterno")
                ->get();
            return Status::Success("empleados", $empledos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los datos bancarios");
        }
    }

    public function Descargar()
    {
        try
        {
            ob_end_clean();
            ob_start();
            return Excel::download(new DatosBacariosExport(), 'Datos Bacarios.xlsx');
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
