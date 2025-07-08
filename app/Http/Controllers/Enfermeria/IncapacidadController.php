<?php

namespace App\Http\Controllers\Enfermeria;

use App\EnfermeriaModels\Incapacidad;
use App\Exports\Enfermeria\IncapacidadExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class IncapacidadController extends Controller
{

    /**
     * Registrar
     */
    public function GuardarIncapacidad(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            $datos = LimpiarInput::LimpiarCampos($request->all(), ["causa", "estado"]);
            if ($request->id == null)
            {
                $incapacidad = new Incapacidad($datos);
                $incapacidad->estado = date("Y-m-d") . ": " . $datos["estado"];
                $incapacidad->empleado_registra_id = Auth::user()->empleado_id;
                $incapacidad->save();
            }
            else
            {
                $incapacidad = Incapacidad::find($request->id);
                $estado_anterior = $incapacidad->estado;
                $incapacidad->fill($datos);
                $incapacidad->estado = $datos["estado"] . " - " . date("Y-m-d") . ": " . $estado_anterior;
                $incapacidad->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar la incapacidad");
        }
    }

    /**
     * Obtener todos los registros de incapacidad
     */
    public function ObtenerIncapacidad()
    {
        try
        {
            $Incapacidad = DB::table("enfermeria_incapacidades as ei")
                ->join("empleados as e", "e.id", "ei.empleado_id")
                ->join("puestos as p", "p.id", "ei.puesto_id")
                ->select(
                    "ei.*",
                    DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as empleado"),
                    "p.nombre as puesto"
                )
                ->orderBy("empleado")
                ->get();
            return Status::Success("incapacidad", $Incapacidad);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los registros de incapacidad");
        }
    }

    /**
     * Descargar reporte de accidentabilidad
     */
    public function Descargar()
    {
        try
        {
            ob_end_clean();
    ob_start();
            return Excel::download(new IncapacidadExport(), "Incapacidad.xlsx");
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
