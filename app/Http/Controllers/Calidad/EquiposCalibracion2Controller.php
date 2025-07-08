<?php

namespace App\Http\Controllers\Calidad;

use App\CalidadModels\Calibraciones;
use App\CalidadModels\Equipos;
use App\Exports\Calidad\EquiposCalibracionExport;
use App\Http\Controllers\Auditoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class EquiposCalibracion2Controller extends Controller
{

    /**
     * Registrar el equipo de calibración
     */
    public function GuardarEquipos(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            $datos = LimpiarInput::LimpiarCampos(
                $request->all(),
                ["equipo", "marca", "modelo", "ns", "rango_medicion", "resguardo", "observaciones"]
            );
            if ($request->id == null)
            {
                $equipos = new Equipos($datos);
                $equipos->empleado_registra_id = Auth::user()->empleado_id;
                $equipos->save();
                Auditoria::AuditarCambios($equipos);
            }
            else
            {
                $equipos = Equipos::find($request->id);
                $equipos->fill($datos);
                Auditoria::AuditarCambios($equipos);
                $equipos->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar los equipos de calbiración");
        }
    }

    /**
     * Obtener todos los equipos de calibración con su ultima certificacion
     */
    public function ObtenerEquipos()
    {
        try
        {
            $equipos = DB::table("calidad_equipos_calibracion as cec")
                ->join("empleados as e", "e.id", "cec.empleado_revisa_id")
                ->select(
                    "cec.id",
                    "cec.equipo",
                    "cec.marca",
                    "cec.modelo",
                    "cec.ns",
                    "cec.tipo",
                    "cec.rango_medicion",
                    "cec.resguardo",
                    "cec.frecuencia",
                    "cec.empleado_revisa_id",
                    "cec.observaciones",
                    "cec.tipo",
                    DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as revisa"),
                    DB::raw("0 as fecha_servicio"),
                    DB::raw("0 as proxima_fecha"),
                    DB::raw("0 as condicion"),
                    DB::raw("0 as certificado")
                )
                ->get();
            foreach ($equipos as $e)
            {
                $calibracion = DB::table("calidad_calibraciones as cc")
                    ->where("cc.equipo_id", $e->id)
                    ->select(
                        "cc.proxima_fecha",
                        "cc.fecha_servicio",
                        "cc.certificado"
                    )
                    ->orderBy("id", "desc")
                    ->first();
                if ($calibracion == null) continue;
                $hoy = date("Y-m-d");
                $e->proxima_fecha = $calibracion->proxima_fecha;
                $e->fecha_servicio = $calibracion->fecha_servicio;
                $e->condicion = $calibracion->proxima_fecha <= $hoy ? 0 : 1;
                $e->certificado = $calibracion->certificado;
            }
            return Status::Success("equipos", $equipos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los equipos de calbiración");
        }
    }

    /**
     * Registrar calibración al equipo seleccinado
     */
    public function GuardarCalibraciones(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            DB::beginTransaction();
            $calibracion = new Calibraciones($request->all());
            $calibracion->equipo_id = $request->equipo_id;
            $calibracion->empleado_registra_id = Auth::user()->empleado_id;
            $calibracion->save();
            Auditoria::AuditarCambios($calibracion);

            // Guardar certificado
            if ($request->aux_certificado == 1)
            {
                $doc = $request->file("certificado");
                $nombre_archivo = uniqid() . ".pdf";
                Storage::disk("local")->put("Calidad/certificados_calib/" . $nombre_archivo, fopen($doc, "r+"));
                $calibracion->certificado = $nombre_archivo;
                Auditoria::AuditarCambios($calibracion);
                $calibracion->update();
            }
            DB::commit();
            return Status::Success();
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return Status::Error($e, "guardar la calibración");
        }
    }

    /**
     * Descargar el certificado seleccionado
     */
    function DescargarCertificado($nombre)
    {
        try
        {
            $ruta_documento = "Calidad/certificados_calib/" . $nombre;

            $path = storage_path("app/" . $ruta_documento);
            if (file_exists($path))
            {
                return response()->download($path, $nombre, [
                    "Content-Type" => "application/pdf",
                    "Content-Disposition' => 'inline; filename='" . $nombre . "'"
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
     * Generar reporte
     */
    function Reporte($tipos)
    {
        try
        {
            $tipo_equipos = [
                "",
                "CONFIGURADORES TREX",
                "EQUIPOS DE MEDICIÓN DE SOLDADURA",
                "VARIALE: VOLTAJE, CORRIENTE, RESISTENCIA Y ASLAMIENTO",
                "EQUIPOS DE TORQUE",
                "EQUIPOS POR ASME",
                "MAQUINAS DE COMBUSTION",
                "MAQUINAS DE SOLDAR",
                "VARIABLE: PRESIÓN",
                "VARIABLE: RUGOSIDAD",
                "VARIABLE: SALES SULUBLES, POROSIDAD, ESPESORES (PINTURA)",
                "VARIABLE: TEMPERATURA",
            ];
            $array_tipos = explode(",", $tipos);

            if (in_array(12, $array_tipos))
            {
                $tipos_descargar = $tipo_equipos;
            }
            else
            {
                $tipos_descargar = array_map(function ($indice) use ($tipo_equipos)
                {
                    if ($indice >= 0 && $indice < count($tipo_equipos))
                    {
                        return $tipo_equipos[$indice];
                    }
                }, $array_tipos);
            }

            ob_end_clean();
            ob_start();
            return Excel::download(new EquiposCalibracionExport($tipos_descargar), "PCC-03_F-01 Control de Calibración de Equipos NR01.xlsx");
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }


    public function Dashboard()
    {
        $equipos = DB::table("calidad_equipos_calibracion as cec")
            ->get();
        $vencidos = [];
        foreach ($equipos as $e)
        {
            $servicio = DB::table("calidad_calibraciones as cc")
                ->where("cc.equipo_id", $e->id)
                ->select(
                    "cc.proxima_fecha",
                    "cc.fecha_servicio",
                    "cc.certificado"
                )
                ->orderBy("id", "desc")
                ->first();

            // Sin registro
            if ($servicio == null)
            {
                $e->estado = 1;
                $e->fecha_servicio = "N/D";
                $e->proxima_fecha = "N/D";
                $vencidos[] = $e;
            }
            else
            {
                // Vencido
                $proxima_fecha = $servicio->proxima_fecha;
                $hoy = date("Y-m-d");
                if ($proxima_fecha < $hoy)
                {
                    $e->estado = 2;
                    $e->fecha_servicio = $servicio->fecha_servicio;
                    $e->proxima_fecha = $proxima_fecha;
                    $vencidos[] = $e;
                }
                if ($proxima_fecha >= $hoy & $proxima_fecha <= date("Y-m-d", strtotime($hoy . "+ 90 days")))
                {
                    $e->estado = 3;
                    $e->fecha_servicio = $servicio->fecha_servicio;
                    $e->proxima_fecha = $servicio->proxima_fecha;
                    $vencidos[] = $e;
                }
            }
        }
        return response()->json(["status" => true, "equipos" => $vencidos]);
    }
}
