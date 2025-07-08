<?php

namespace App\Http\Controllers\Enfermeria;

use App\EnfermeriaModels\RegistroCovid;
use App\Exports\Enfermeria\RegistrosCovidExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RegistroCovidController extends Controller
{

    /**
     * Registrar
     */
    public function GuardarRegistroCovid(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            $datos = LimpiarInput::LimpiarCampos($request->all(), [
                "diagnostico", "dias_incapacidad", "prueba"
            ]);
            if ($request->id == null)
            {
                $registrocovid = new RegistroCovid($datos);
                $registrocovid->empleado_registra_id = Auth::user()->empleado_id;
                $registrocovid->save();
            }
            else
            {
                $registrocovid = RegistroCovid::find($request->id);
                $registrocovid->fill($datos);
                $registrocovid->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el registro");
        }
    }

    /**
     * 
     */
    public function ObtenerRegistroCovid()
    {
        try
        {
            $RegistroCovid = DB::table("enfermeria_registros_covid as erc")
                ->join("empleados as e", "e.id", "erc.empleado_id")
                ->join("puestos as p", "p.id", "erc.puesto_id")
                ->select(
                    "erc.id",
                    "erc.empleado_id",
                    "erc.puesto_id",
                    "erc.diagnostico",
                    "erc.inicio_sintomas",
                    "erc.fecha_deteccion",
                    "erc.inicio_incapacidad",
                    "erc.dias_incapacidad",
                    "erc.termino_incapacidad",
                    "erc.prueba",
                    DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as empleado"),
                    "p.nombre as puesto"
                )
                ->orderBy("empleado")
                ->get();
            return Status::Success("registrocovid", $RegistroCovid);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los registros");
        }
    }

    /**
     * Generar reporte
     */
    public function GenerarReporte()
    {
        try
        {
            ob_end_clean();
    ob_start();
            return Excel::download(new RegistrosCovidExport(),"PERSONAL_COVID.xlsx");
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
