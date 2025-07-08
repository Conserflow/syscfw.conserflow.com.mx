<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\SeguridadModels\ParticipantePlatica;
use App\SeguridadModels\Platica;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;

class PlaticasController extends Controller
{
    private $PATH = "Seguridad/Platicas/";
    /**
     * Obtener todas las platicas de seguridad
     */
    public function ObtenerPlaticas()
    {
        try
        {
            $platicas = DB::table("seguridad_platicas as sp")
                ->join("empleados as e", "e.id", "sp.responsable_id")
                ->select(
                    "sp.id",
                    "sp.ubicacion",
                    "sp.fecha",
                    "sp.tema",
                    "sp.responsable_id",
                    "sp.documento",
                    DB::raw("concat_ws(' ',e.ap_paterno,e.ap_materno,e.nombre) as nombre")
                )
                ->orderBy("sp.fecha","desc")
                ->get();
            return Status::Success("platicas", $platicas);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtner las platicas");
        }
    }

    /**
     * Guardar o actualizar platica
     */
    public function Guardar(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');
            $datos = LimpiarInput::LimpiarCampos($request->all(), ["ubicacion", "tema"]);
            if ($request->id == null) // Nuevo
            {
                $platica = new Platica($datos);
                $platica->empleado_registra_id = Auth::user()->empleado_id;
                $platica->save();
            }
            else //Actu
            {
                $platica = Platica::find($request->id);
                $platica->fill($datos);
                $platica->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar la plática");
        }
    }

    /**
     * Descarga la plantilla de las platicas
     */
    public function DescargarPlantilla($p_id)
    {
        try
        {
            // Obtener platica
            $platica = DB::table("seguridad_platicas as sp")
                ->join("empleados as e", "e.id", "sp.responsable_id")
                ->select(
                    "sp.*",
                    DB::raw("concat_ws(' ',e.ap_paterno,e.ap_materno,e.nombre) as nombre")
                )
                ->where("sp.id", $p_id)
                ->first();

            $pdf = PDF::loadView("pdf.seguridad.platica", compact("platica"));
            $pdf->setPaper('letter', 'portrait');
            $pdf->getDomPDF()->set_option("enable_php", true);

            return $pdf->stream("Platica de Seguridad.pdf");
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }

    /**
     * Subir el documento de la platica
     */
    public function SubirEvidencia(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            $platica = Platica::find($request->platica_id);
            $uid = uniqid() . ".pdf";
            Storage::disk("local")->put(
                $this->PATH . $uid,
                fopen($request->file("evidencia"), 'r+')
            );

            $platica->documento = $uid; // nombre del documento guardado
            $platica->update();

            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el documento");
        }
    }

    /**
     * Descargar evidencia
     */
    public function DescargarEvidencia($id)
    {
        try
        {
            // buscar platica
            $platica = Platica::find($id);
            $nombre = $platica->documento;
            $path = "app/" . $this->PATH . $nombre;
            $doc = storage_path($path);
            if (file_exists($doc))
            {
                return response()->download($doc, $nombre, [
                    'Content-Type' => "application/pdf",
                    'Content-Disposition' => 'inline; filename="' . $nombre . '"'
                ]);
            }
            return view("errors.404");
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }

    /**
     * Obtener los participantes
     */
    public function ObtenerParticipantes($platica_id)
    {
        try
        {
            $p = DB::table("seguridad_platicas_participantes as spp")
                ->join("empleados as e", "e.id", "spp.empleado_id")
                ->join("puestos as p", "p.id", "e.puesto_id")
                ->select(
                    "spp.*",
                    DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre"),
                    "p.nombre as puesto"
                )
                ->where("spp.platica_id", $platica_id)
                ->orderBy("nombre")
                ->get();
            return Status::Success("participantes", $p);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los participantes");
        }
    }
    /**
     * Guadar participante
     */

    public function GuardarParticipanres(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');
            DB::beginTransaction();
            $emp_id = Auth::user()->empleado_id;
            foreach ($request->participantes as $p)
            {
                $particip = new ParticipantePlatica();
                $particip->platica_id = $request->platica_id;
                $particip->empleado_id = $p["id"];
                $particip->empleado_registra_id = $emp_id;
                $particip->save();
            }
            DB::commit();
            return Status::Success();
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return Status::Error($e, "guadar los participantes");
        }
    }

    /**
     * Generar reporte
     */
    public function Descargar($p_id)
    {
        try
        {
            // Obtener platica
            $platica = DB::table("seguridad_platicas as sp")
                ->join("empleados as e", "e.id", "sp.responsable_id")
                ->select(
                    "sp.*",
                    DB::raw("concat_ws(' ',e.ap_paterno,e.ap_materno,e.nombre) as nombre")
                )
                ->where("sp.id", $p_id)
                ->first();
            // Participantes
            $participantes = DB::table("seguridad_platicas_participantes as spp")
                ->join("empleados as e", "e.id", "spp.empleado_id")
                ->join("puestos as p", "p.id", "e.puesto_id")
                ->select(
                    "spp.*",
                    DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre"),
                    "p.nombre as puesto"
                )
                ->where("spp.platica_id", $p_id)
                ->orderBy("nombre")
                ->get();
            $pdf = PDF::loadView("pdf.seguridad.platica", compact("platica", "participantes"));
            $pdf->setPaper('letter', 'portrait');
            $pdf->getDomPDF()->set_option("enable_php", true);

            return $pdf->stream("Platica de Seguridad");
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
