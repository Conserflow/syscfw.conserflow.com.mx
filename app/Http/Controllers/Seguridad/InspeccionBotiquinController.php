<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\SeguridadModels\Botiquin;
use App\SeguridadModels\InspeccionBotiquin;
use Barryvdh\DomPDF\Facade;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InspeccionBotiquinController extends Controller
{
    /**
     * Obtener todas las inspecciones
     */
    public function Obtener()
    {
        try
        {
            $inspecciones = DB::table("seguridad_inspeccion_botiquin as sib")
                ->join("empleados as e", "e.id", "sib.inspector_id")
                ->join("empleados as e2", "e2.id", "sib.responsable_id")
                ->select(
                    "sib.*",
                    DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as inspector"),
                    DB::raw("concat_ws(' ',e2.nombre,e2.ap_paterno,e2.ap_materno) as responsable")
                )->get();
            return Status::Success("inspecciones", $inspecciones);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener las inspecciones");
        }
    }

    /**
     * Registra una nueva inspección
     */
    public function Guardar(Request $request)
    {
        try
        {
            if (!$request->ajax()) redirect("/");
            $datos = LimpiarInput::LimpiarCampos($request->all(), ["area", "recomendaciones"]);
            if ($request->id == null)
            {
                $inspeccion = new InspeccionBotiquin($datos);
                $inspeccion->empleado_registra_id = Auth::user()->empleado_id;
                $inspeccion->save();
            }
            else
            {
                $inspeccion = InspeccionBotiquin::find($request->id);
                $inspeccion->fill($datos);
                $inspeccion->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            dd($e);
            return Status::Error($e, "registrar la inspección");
        }
    }

    /**
     * Obtiene todos los registros de botiuquin de la inspección ingresada
     */
    public function ObtenerBotiquines($inspeccion_id)
    {
        try
        {
            $botiquines = DB::table("seguridad_inspeccion_botiquin_botiquin as b")
                ->where("b.sib_id", $inspeccion_id)
                ->get();
            return Status::Success("botiquines", $botiquines);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los botiquines");
        }
    }

    /**
     * Registra un botiquín
     */
    public function GuardarBotiquin(Request $request)
    {
        try
        {
            if (!$request->ajax()) redirect("/");
            $datos = LimpiarInput::LimpiarCampos($request->all(), ["material", "observacion"]);
            if ($request->id == null)
            {
                $botiquin = new Botiquin($datos);
                $botiquin->empleado_registra_id = Auth::user()->empleado_id;
                $botiquin->save();
            }
            else
            {
                $botiquin = Botiquin::find($request->id);
                $botiquin->fill($datos);
                $botiquin->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            dd($e);
            return Status::Error($e, "guardar el botiquín");
        }
    }

    /**
     * Genera el formato
     */
    public function Descargar($i_id)
    {
        try
        {
            $inspeccion = DB::table("seguridad_inspeccion_botiquin as sib")
                ->join("empleados as e", "e.id", "sib.inspector_id")
                ->join("empleados as e2", "e2.id", "sib.responsable_id")
                ->select(
                    "sib.*",
                    DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as inspector"),
                    DB::raw("concat_ws(' ',e2.nombre,e2.ap_paterno,e2.ap_materno) as responsable")
                )->where("sib.id", $i_id)
                ->first();

            $basicos = DB::table("seguridad_inspeccion_botiquin_botiquin as b")
                ->where("b.sib_id", $i_id)
                ->where("b.apoyo",0)
                ->get();
                
            $apoyo = DB::table("seguridad_inspeccion_botiquin_botiquin as b")
                ->where("b.sib_id", $i_id)
                ->where("b.apoyo",1)
                ->get();

            $pdf = Facade::loadView("pdf.seguridad.botiquines", compact("inspeccion", "basicos","apoyo"));
            $pdf->setPaper('letter', 'portrait');
            $pdf->getDomPDF()->set_option("enable_php", true);

            return $pdf->stream("PSE01_F05_Inspección Y Control De Botiquin De Primeros Auxilios");
        }
        catch (Exception $e)
        {
            dd($e);
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
