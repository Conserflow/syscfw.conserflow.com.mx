<?php

namespace App\Http\Controllers\TI;

use App\Http\Controllers\Auditoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Otros\Utils;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\TIModels\CotizacionEquipo;
use App\TIModels\PropuestaEquipo;
use Barryvdh\DomPDF\Facade;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PropuestaEquipoController extends Controller
{

    /**
     * Guarda la propuesta de equipo
     */
    public function Guardarpropuesta(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;

            $emp_id = Auth::user()->empleado_id;
            $datos = LimpiarInput::LimpiarCampos(
                $request->all(),
                [
                    "necesidad_especial", "marca", "modelo", "almacenamiento",
                    "procesador", "ram", "comentarios", "accesorios"
                ]
            );

            if ($request->id == null)
            {
                $propuesta = new PropuestaEquipo($datos);
                $propuesta->empleado_registra_id = $emp_id;
                $propuesta->usuario_id = $emp_id;
                $propuesta->save();
                Auditoria::AuditarCambios($propuesta);
            }
            else
            {
                $propuesta = PropuestaEquipo::find($request->id);
                $propuesta->fill($datos);
                $propuesta->usuario_id = $emp_id;
                $propuesta->update();
                Auditoria::AuditarCambios($propuesta);
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar la propuesta de equipo");
        }
    }

    /**
     * Obtener las propuestas de equipo realizadas
     */
    public function Obtenerpropuestas()
    {
        try
        {
            $propuestas = DB::table("ti_propuesta_equipo as tpe")
                ->join("puestos as p", "p.id", "tpe.puesto_id")
                ->select(
                    "tpe.id",
                    "p.nombre as puesto_nombre",
                    "tpe.puesto_id as puesto_id",
                    "tpe.fecha",
                    "tpe.necesidad_especial",
                    "tpe.tipo",
                    "tpe.marca",
                    "tpe.modelo",
                    "tpe.almacenamiento",
                    "tpe.procesador",
                    "tpe.ram",
                    "tpe.comentarios",
                    "tpe.accesorios"
                )
                ->get();
            return Status::Success("propuestas", $propuestas);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener las propuestas");
        }
    }

    /**
     * Registrar
     */
    public function Guardarcotizacion(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            $datos = LimpiarInput::LimpiarCampos(
                $request->all(),
                ["proveedor", "marca", "forma_pago"]
            );
            $cotizaciones_n = DB::table("ti_propuestas_cotizaciones as tpc")
                ->where("tpc.propuesta_id", $request->propuesta_id)->count();
            if ($cotizaciones_n >= 3)
                return Status::Error2("Límite de cotizaciones. Max 3");

            if ($request->id == null)
            {
                $cotizacion = new CotizacionEquipo($datos);
                $cotizacion->empleado_registra_id = Auth::user()->empleado_id;
                $cotizacion->save();
                Auditoria::AuditarCambios($cotizacion);
            }
            else
            {
                $cotizacion = CotizacionEquipo::find($request->id);
                $cotizacion->fill($datos);
                Auditoria::AuditarCambios($cotizacion);
                $cotizacion->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar la cotización");
        }
    }

    /**
     * Obtener las cotizaciones de la propuesta ingresada
     */
    public function Obtenercotizaciones($c_id)
    {
        try
        {
            $cotizacion = DB::table("ti_propuestas_cotizaciones as tpc")
                ->where("tpc.propuesta_id", $c_id)
                ->select(
                    "id",
                    "proveedor",
                    "marca",
                    "costo",
                    "forma_pago"
                )
                ->get();
            return Status::Success("cotizacion", $cotizacion);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener las cotizaciones");
        }
    }

    /**
     * Descargar el formato de la propuesta
     */
    public function Descargar($id)
    {
        try
        {
            // Obtener propuestas
            $propuesta = DB::table("ti_propuesta_equipo as tpe")
                ->join("puestos as p", "p.id", "tpe.puesto_id")
                ->join("empleados as e", "e.id", "tpe.empleado_registra_id")
                ->where("tpe.id", $id)
                ->select(
                    "p.nombre as puesto",
                    "tpe.fecha",
                    "tpe.necesidad_especial",
                    "tpe.tipo",
                    "tpe.marca",
                    "tpe.modelo",
                    "tpe.almacenamiento",
                    "tpe.procesador",
                    "tpe.ram",
                    "tpe.comentarios",
                    "tpe.accesorios",
                    DB::raw("CONCAT_WS(' ',e.nombre,e.ap_paterno,e.ap_materno) as registra"),
                )
                ->first();
            // Fecha
            $propuesta->fecha = Utils::FechaCompleta($propuesta->fecha, "-");

            // Cotizaciones
            $cotizaciones = DB::table("ti_propuestas_cotizaciones as tpc")
                ->where("tpc.propuesta_id", $id)
                ->select(
                    "proveedor",
                    "marca",
                    "costo",
                    "forma_pago"
                )
                ->get();
            $pdf = Facade::loadView('pdf.ti.propuestaequipo', compact("propuesta", "cotizaciones"));
            $pdf->setPaper("letter", "portrait");
            $pdf->getDomPDF()->set_option("enable_php", true);
            return $pdf->stream("CUESTIONARIO DE INFRAESTRUCTURA");
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
