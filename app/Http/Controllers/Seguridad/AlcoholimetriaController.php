<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\SeguridadModels\PruebaAlcohol;
use App\SeguridadModels\PruebaAlcoholParticipante;
use Barryvdh\DomPDF\Facade;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AlcoholimetriaController extends Controller
{
    private $PATH = "Seguridad/PruebasAlcohol/";

    /**
     * Obtener las pruebas de alcohol
     */
    public function ObtenerPruebas()
    {
        try
        {
            $pruebas = DB::table("seguridad_pruebas_alcohol as spa")
                ->join("empleados as e", "e.id", "spa.responsable_id")
                ->select(
                    "spa.*",
                    DB::raw("concat_ws(' ',e.ap_paterno,e.ap_materno,e.nombre) as responsable")
                )
                ->get();
            return Status::Success("pruebas", $pruebas);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener las pruebas");
        }
    }

    /**
     * Guardar Prueba
     */
    public function Guardar(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');
            $datos = LimpiarInput::LimpiarCampos($request->all(), ["ubicacion", "observaciones"]);
            if ($request->id == null)
            {
                $prueba = new PruebaAlcohol($datos);
                $prueba->empleado_registra_id = Auth::user()->empleado_id;
                $prueba->save();
            }
            else
            {
                $prueba = PruebaAlcohol::find($request->id);
                $prueba->fill($datos);
                $prueba->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar la prueba");
        }
    }

    /**
     * Obtener los particpantes de la prueba
     */
    public function ObtenerParticipantes($prueba_id)
    {
        try
        {
            $p = DB::table("seguridad_pruebaalcohol_participantes as spp")
                ->join("empleados as e", "e.id", "spp.empleado_id")
                ->join("puestos as p", "p.id", "e.puesto_id")
                ->select(
                    "spp.*",
                    DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre"),
                    "p.nombre as puesto"
                )
                ->where("spp.prueba_id", $prueba_id)
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
    public function GuardarParticipantes(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');
            DB::beginTransaction();
            $emp_id = Auth::user()->empleado_id;
            foreach ($request->participantes as $p)
            {
                $particip = new PruebaAlcoholParticipante();
                $particip->prueba_id = $request->prueba_id;
                $particip->empleado_id = $p["id"];
                $particip->empleado_registra_id = $emp_id;
                $particip->resultado = $request->resultado;
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
     * Descargar plantilla
     */
    public function DescargarPlantilla($prueba_id)
    {
        try
        {
            // Obtener pruebas
            $prueba = DB::table("seguridad_pruebas_alcohol as spa")
                ->join("empleados as e", "e.id", "spa.responsable_id")
                ->select(
                    "spa.*",
                    DB::raw("concat_ws(' ',e.ap_paterno,e.ap_materno,e.nombre) as responsable")
                )
                ->where("spa.id",$prueba_id)
                ->first();

            $pdf = Facade::loadView("pdf.seguridad.pruebaalcohol", compact("prueba"));
            $pdf->setPaper('letter', 'portrait');
            $pdf->getDomPDF()->set_option("enable_php", true);

            return $pdf->stream("PSE01_F06_Registro De Pruebas De Alcoholimetría.pdf");
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
            $prueba = PruebaAlcohol::find($request->prueba_id);
            $uid = uniqid() . ".pdf";
            Storage::disk("local")->put(
                $this->PATH . $uid,
                fopen($request->file("evidencia"), 'r+')
            );

            $prueba->documento = $uid; // nombre del documento guardado
            $prueba->update();

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
            $prueba = PruebaAlcohol::find($id);
            $nombre = $prueba->documento;
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
}
