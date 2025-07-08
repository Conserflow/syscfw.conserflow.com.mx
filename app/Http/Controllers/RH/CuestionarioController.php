<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\RHModels\CuestionarioInfra;
use Barryvdh\DomPDF\Facade;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CuestionarioController extends Controller
{
    private $PATH = "RH/CuestionariosInfra/";

    /**
     * Genera el formato para cuestionario de infraestructura
     */
    public function DescargarPlantilla()
    {
        try
        {
            $puntos = [
                "¿Cuentas con el equipo de cómputo adecuado para tus actividades?",
                "¿Cuentas con el equipo de oficina necesario para el desarrollo de tus actividades (impresora, teléfono, cámara, etc.)?",
                "¿Cuentas con el mobiliario de oficina (silla, escritorio, estantes, gavetas) adecuado?",
                "El área donde realizas tus actividades ¿cuenta con el espacio, luz y ventilación suficiente?",
                "El área de comedores, ¿Se encuentra en buenas condiciones las instalaciones, cantidad de mobiliario, ordenado y limpio?",
                "Los baños, ¿Se encuentran en condiciones, cantidad y localización adecuada?",
                "Las Instalaciones en General ¿Se encuentran en condiciones óptimas, ordenadas, limpias?",
            ];
            $respuesta_no = "Si tu respuesta es NO, favor de especificar.";
            $pdf = Facade::loadView('pdf.rh.infraestructura', compact("puntos", "respuesta_no"));
            $pdf->setPaper("letter", "portrait");
            $pdf->getDomPDF()->set_option("enable_php", true);
            return $pdf->stream("CUESTIONARIO DE INFRAESTRUCTURA");
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view(("errors.500"));
        }
    }

    /**
     * Registrar
     */
    public function GuardarCuestionarioInfra(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            $datos = $request->all();
            if ($request->id == null)
            {
                $cuestionarioinfra = new CuestionarioInfra($datos);
                $cuestionarioinfra->empleado_registra_id = Auth::user()->empleado_id;
                $cuestionarioinfra->save();
                Auditoria::AuditarCambios($cuestionarioinfra);
            }
            else
            {
                $cuestionarioinfra = CuestionarioInfra::find($request->id);
                $cuestionarioinfra->fill($datos);
                Auditoria::AuditarCambios($cuestionarioinfra);
                $cuestionarioinfra->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar la información");
        }
    }

    /**
     * 
     */
    public function ObtenerCuestionarioInfra()
    {
        try
        {
            $CuestionarioInfra = DB::table("rh_cuestionario_infra as rhi")
                ->join("empleados as e", "e.id", "rhi.empleado_id")
                ->select(
                    "rhi.id",
                    "rhi.fecha",
                    "documento",
                    DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre")
                )
                ->get();
            return Status::Success("cuestionarioinfra", $CuestionarioInfra);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los cuestionarios");
        }
    }

    /**
     * Subir el documento
     */
    public function SubirCuestionarioInfra(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            $cuestionarioinfra = CuestionarioInfra::find($request->cuestionario_id);
            $uid = uniqid() . ".pdf";
            Storage::disk("local")->put(
                $this->PATH . $uid,
                fopen($request->file("evidencia"), 'r+')
            );

            $cuestionarioinfra->documento = $uid; // nombre del documento guardado
            Auditoria::AuditarCambios($cuestionarioinfra);
            $cuestionarioinfra->update();

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
            $cuestionarioinfra = CuestionarioInfra::find($id);
            $nombre = $cuestionarioinfra->documento;
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
