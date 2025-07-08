<?php

namespace App\Http\Controllers\Compras;

use App\ComprasModels\EvaluacionProveedor;
use App\Exports\Compras\EvaluacionProveedoresExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use Barryvdh\DomPDF\Facade;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class EvaluacionProveedoresController extends Controller
{
    /**
     * Obtiene todos los proveedores activos para Catalogo de Registro y Evaluación
     */
    public function ObtenerProveedores($anio)
    {
        try
        {
            // Obtener proveedores activos, que se les ha comprado
            $proveedores_aux = DB::table("ordenes_compras as oc")
                ->join("proveedores as p", "p.id", "oc.proveedore_id")
                ->whereYear("oc.fecha_orden", "=", $anio)
                ->where("p.condicion", 1)
                ->select(
                    "p.id",
                    "p.nombre",
                    "p.razon_social",
                    "p.rfc",
                    "p.direccion"
                )
                ->orderBy("p.nombre")
                ->distinct()
                ->get();

            $proveedores = [];
            // Obtener la evaluación del proveedor en el año ingresado
            foreach ($proveedores_aux as $p)
            {
                $evaluacion = DB::table("evaluacion_provee as ep")
                    ->where("ep.proveedor_id", $p->id)
                    ->whereRaw("year(fecha)=?", [$anio])
                    ->select(
                        "ep.id as ep_id",
                        DB::raw("(
            ep.uno+ep.dos+ep.tres+ep.cuatro+ep.cinco+ep.seis+ep.siete+
            ep.ocho+ep.nueve+ep.diez+ep.once+ep.doce+ep.trece+ep.catorce+ep.quince+
            ep.diesiseis+ep.diesisiete+ep.diesiocho
          ) as total_evaluacion")
                    )
                    ->first();
                // Unir proveedor y evaluacion
                $aux = array_merge((array)$p, (array)$evaluacion);

                $proveedores[] = $aux;
            }
            return Status::Success("proveedores", $proveedores);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los proveedores");
        }
    }

    /**
     * Obtiene la evaluación ingresada
     */
    public function ObtenerEvaluacion($id)
    {
        try
        {
            // Obtiene la evaluacion sin importar el proveedor ni el año
            $evaluacion = DB::table("evaluacion_provee as ep")
                ->join("empleados as e", "e.id", "ep.evaluador")
                ->select(
                    "ep.*",
                    DB::raw("CONCAT_WS(' ',e.nombre,e.ap_paterno,e.ap_materno) AS empleado")
                )
                ->where("ep.id", $id)->first();
            return Status::Success("evaluacion", $evaluacion);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener la evaluación");
        }
    }

    /**
     * Registra la evaluación del proveedor
     */
    public function GuardarEvaluacion(Request $request)
    {
        try
        {
            if ($request->id == null) // Nuevo
            {
                $evaluacion = new EvaluacionProveedor();
                $evaluacion->fill($request->all());
                Utilidades::auditar($evaluacion, $evaluacion->id);
                $evaluacion->save();
            }
            else // Actu
            {
                $evaluacion = EvaluacionProveedor::find($request->id);
                $evaluacion->fill($request->all());
                $evaluacion->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "registrar la evaluación del proveedor");
        }
    }

    /**
     * Descarga la evaluacion del provedor
     */
    public function DescargarEvaluacion($id)
    {
        try
        {
            $evaluacion = DB::table("evaluacion_provee as ep")
                ->join("empleados as e", "e.id", "ep.evaluador")
                ->select("ep.*", DB::raw("CONCAT_WS(' ',e.nombre,e.ap_paterno,e.ap_materno) as empleado"))
                ->where("ep.id", $id)->first();

            $categoria = "";
            $calificacion = $evaluacion->uno + $evaluacion->dos + $evaluacion->tres + $evaluacion->cuatro + $evaluacion->cinco + $evaluacion->seis + $evaluacion->siete +
                $evaluacion->ocho + $evaluacion->nueve + $evaluacion->diez + $evaluacion->once + $evaluacion->doce + $evaluacion->trece + $evaluacion->catorce + $evaluacion->quince +
                $evaluacion->diesiseis + $evaluacion->diesisiete + $evaluacion->diesiocho;
            if ($calificacion >= 54)
            {
                $categoria = "APROBADO";
            }
            if ($calificacion >= 36 && $calificacion <= 53)
            {
                $categoria = "CONDICIONADO";
            }
            if ($calificacion <= 35)
            {
                $categoria = "NO APTO";
            }
            // Obtener proveedor
            $proveedor = DB::table("proveedores AS p")
                ->where("id", $evaluacion->proveedor_id)->first();

            $pdf = Facade::loadView("pdf.evaluacion", compact("id", "evaluacion", "proveedor", "calificacion", "categoria"));
            $pdf->setPaper("letter", "portrait");

            return $pdf->stream("EVALUACIÓN DE PROVEEDOR.pdf");
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.204");
        }
    }

    /**
     * DEscargar la evaluación del año selecionado
     */
    public function DescargarReporte($anio)
    {
        try
        {
            ob_end_clean(); // this
            ob_start(); // and this
            $nombre = "Evaluación_Proveedores-$anio.xlsx";
            ob_end_clean();
ob_start();
            return Excel::download(new EvaluacionProveedoresExport($anio), $nombre);
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
