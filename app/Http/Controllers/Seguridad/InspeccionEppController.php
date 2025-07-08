<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\SeguridadModels\InspeccionEpp;
use App\SeguridadModels\InspeccionEppParticipante;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class InspeccionEppController extends Controller
{

    /**
     * Registra una nueva inspección
     */
    public function Guardar(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');
            $datos = LimpiarInput::LimpiarCampos($request->all(), ["ubicacion", "observaciones"]);
            $empleado_id = Auth::user()->empleado_id;
            if ($request->id == null)
            {
                $inspeccion = new InspeccionEpp($datos);
                $inspeccion->empleado_registra_id = $empleado_id;
                $inspeccion->save();
            }
            else
            {
                $inspeccion = InspeccionEpp::find($request->id);
                $inspeccion->fill($datos);
                $inspeccion->save();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar la inspección");
        }
    }

    /**
     * Obtener inspecciones
     */
    public function Obtener()
    {
        try
        {
            $inspecciones = DB::table("seguridad_inspeccion_epp as sip")
                ->join("empleados as e1", "e1.id", "sip.empleado_realiza_id")
                ->join("empleados as e2", "e2.id", "sip.empleado_revisa_id")
                ->select(
                    "sip.*",
                    DB::raw("concat_ws(' ',e1.ap_paterno,e1.ap_materno,e1.nombre) as realiza"),
                    DB::raw("concat_ws(' ',e2.ap_paterno,e2.ap_materno,e2.nombre) as revisa")
                )
                ->orderBy("sip.fecha")
                ->get();
            return Status::Success("inspecciones", $inspecciones);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener las inspecciones");
        }
    }

    /**
     * Obtener los participantes de la inspección
     */
    public function ObtenerParticipantes($inspeccion_id)
    {
        try
        {
            $participantes = DB::table("seguridad_inspeccion_epp_participantes as siep")
                ->join("empleados as e", "e.id", "siep.empleado_id")
                ->join("puestos as p", "e.puesto_id", "p.id")
                ->select(
                    "siep.*",
                    "p.nombre as puesto",
                    DB::raw("concat_ws(' ',e.ap_paterno,e.ap_materno,e.nombre) as empleado")
                )->where("siep.sip_id", $inspeccion_id)
                ->orderBy("empleado")
                ->get();
            return Status::Success("participantes", $participantes);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los participantes");
        }
    }

    /**
     * Guarda el participante de la inspección
     */
    public function GuardarParticipante(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');
            $datos = $request->all();
            $empleado_id = Auth::user()->empleado_id;
            if ($request->id == null)
            {
                $n = DB::table("seguridad_inspeccion_epp_participantes as siep")
                    ->where("siep.sip_id", $request->sip_id)
                    ->count();
                if ($n >= 12) return response()->json(
                    ["status" => false, "mensaje" => "Limite alcanzado. Registre otra inspección"]
                );
                $participante = new InspeccionEppParticipante($datos);
                $participante->empleado_registra_id = $empleado_id;
                $participante->save();
            }
            else
            {
                $participante = InspeccionEppParticipante::find($request->id);
                $participante->fill($datos);
                $participante->save();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar la inspección");
        }
    }

    /**
     * Genera el formato de Inspección de EPP
     */
    public function Descargar($i_id)
    {
        try
        {
            // Inspecion
            $inspeccion = DB::table("seguridad_inspeccion_epp as sip")
                ->join("empleados as e1", "e1.id", "sip.empleado_realiza_id")
                ->join("empleados as e2", "e2.id", "sip.empleado_revisa_id")
                ->select(
                    "sip.*",
                    DB::raw("concat_ws(' ',e1.ap_paterno,e1.ap_materno,e1.nombre) as realiza"),
                    DB::raw("concat_ws(' ',e2.ap_paterno,e2.ap_materno,e2.nombre) as revisa")
                )
                ->where("sip.id", $i_id)->first(1);
            // Participantes
            $participantes = DB::table("seguridad_inspeccion_epp_participantes as siep")
                ->join("empleados as e", "e.id", "siep.empleado_id")
                ->join("puestos as p", "e.puesto_id", "p.id")
                ->select(
                    "siep.*",
                    "p.nombre as puesto",
                    DB::raw("concat_ws(' ',e.ap_paterno,e.ap_materno,e.nombre) as empleado")
                )->where("siep.sip_id", $i_id)
                ->orderBy("empleado")
                ->get();
            $pdf = PDF::loadView("pdf.seguridad.inspeccionepp", compact("inspeccion", "participantes"));
            $pdf->setPaper('legder', 'landscape');
            $pdf->getDomPDF()->set_option("enable_php", true);

            return $pdf->stream("Platica de Seguridad");
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return  view("errors.500");
        }
    }
}
