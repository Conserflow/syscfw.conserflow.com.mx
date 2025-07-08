<?php

namespace App\Http\Controllers\TI;

use App\Exports\TI\ResguardosExport;
use App\Http\Controllers\Auditoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Otros\Utils;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\TIModels\TiMaterialResguardo;
use App\TIModels\TiAccesorio;
use App\TIModels\TiComputo;
use App\TIModels\TiImpresion;
use App\TIModels\TiVideo;
use Barryvdh\DomPDF\Facade;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ValesResguardoController extends Controller
{
    public function ObtenerVales($emp)
    {
        try
        {
            $resguardos = DB::table("ti_material_resguardo as tmr")
                ->join("empleados as e", "e.id", "tmr.empleado_recibe")
                ->join("empleados as ee", "ee.id", "tmr.empleado_entrega")
                ->select(
                    "tmr.*",
                    DB::raw("CONCAT_WS(' ',e.nombre,e.ap_paterno,e.ap_materno) as empleado_r"),
                    DB::raw("CONCAT_WS(' ',ee.nombre,ee.ap_paterno,ee.ap_materno) as empleado_e"),
                    DB::raw("'-' as descripcion")
                )
                ->where("tmr.empresa", $emp) // Empresa
                ->orderBy("tmr.id", "desc")
                ->get();

            $list_resguardos = [];
            foreach ($resguardos as $r)
            {
                $desc = "";
                if ($r->tipo == 1)
                {
                    $cdata = TiComputo::where("id", $r->caiv)
                        ->select(
                            DB::raw("CONCAT_WS(' ',no_serie,marca_modelo,cpu,ram,almacenamiento,
                            tarjeta_video,tarjeta_red,observaciones,mac) as descripcion")
                        )
                        ->first();
                    $desc = $cdata->descripcion;
                }
                if ($r->tipo == 2)
                {
                    $cdata = TiAccesorio::select(DB::raw("CONCAT_WS(' ',descripcion,modelo,marca,no_serie) as descripcion"))
                        ->where("id", $r->caiv)
                        ->first();
                    $desc = $cdata->descripcion;
                }
                if ($r->tipo == 3)
                {
                    $cdata = TiImpresion::select(DB::raw("CONCAT_WS(' ',descripcion,modelo,marca,no_serie) as descripcion"))
                        ->where("id", $r->caiv)
                        ->first();
                    $desc = $cdata->descripcion;
                }
                if ($r->tipo == 4)
                {
                    $cdata = TiVideo::select(DB::raw("CONCAT_WS(' ',descripcion,modelo,marca,no_serie) as descripcion"))
                        ->where("id", $r->caiv)
                        ->first();
                    $desc = $cdata->descripcion;
                }
                $r->descripcion = $desc;
                $list_resguardos[] = $r;
            }

            return Status::Success("resguardos", $list_resguardos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los vales de resguardo");
        }
    }

    /**
     * Obtener los equipos del tipo ingresado
     */
    public function ObtenerEquipos($tipo)
    {
        try
        {
            if ($tipo == 1) // Equipo de cómputo
            {
                $cdata = TiComputo::where("condicion", ">", 0) // 
                    ->where("eliminado", 1) // No eliminado
                    ->where("cantidad",  1)
                    ->select(
                        "id",
                        "marca_modelo AS marca",
                        "marca_modelo AS modelo",
                        "no_serie",
                        DB::raw("CONCAT_WS(' ',no_serie,marca_modelo,cpu,ram,
                        almacenamiento,tarjeta_video,tarjeta_red,observaciones,mac) 
                        AS descripcion"),
                        "cantidad"
                    )
                    ->get();
            }
            else if ($tipo == 2) //Accesorios
            {
                $cdata = TiAccesorio::where("cantidad", ">", 0)
                    ->where("eliminado", 1)
                    ->select(
                        "id",
                        DB::raw("CONCAT_WS(' ',descripcion,modelo,marca,no_serie) AS descripcion"),
                        "cantidad"
                    )
                    ->get();
            }

            else if ($tipo == 3) //Impresion
            {
                $cdata = TiImpresion::where("cantidad", ">", 0)
                    ->where("condicion", 1)
                    ->where("eliminado", 1)
                    ->select(
                        "id",
                        "marca",
                        "modelo",
                        "no_serie",
                        DB::raw("CONCAT_WS(' ',descripcion,modelo,marca,no_serie) AS descripcion"),
                        "cantidad"
                    )
                    ->get();
            }
            else //Video
            {
                $cdata = TiVideo::where("cantidad", ">", 0)
                    ->where("condicion", 1)
                    ->where("eliminado", 1)
                    ->select(
                        "id",
                        DB::raw("CONCAT_WS(' ',descripcion,no_serie) AS descripcion"),
                        "cantidad"
                    )
                    ->get();
            }
            return Status::Success("list_equipos", $cdata);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los equipos");
        }
    }

    /**
     * Obtener los accesorios
     */
    public function ObtenerAccesorios()
    {
        try
        {
            $accesorios = TiAccesorio::where("cantidad", ">", 0)
                ->where("condicion", 1)
                ->where("eliminado", 1)
                ->select(
                    "id",
                    DB::raw("CONCAT_WS(' ',descripcion,modelo,marca,no_serie) AS descripcion"),
                    "cantidad"
                )
                ->get();
            return Status::Success("accesorios", $accesorios);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los accesorios");
        }
    }

    /**
     * Guardar resguardo
     */
    public function GuardarResguardo(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $resguardo = new TiMaterialResguardo($request->all());
            $resguardo->save();
            Auditoria::AuditarCambios($resguardo);

            if ($request->tipo == 1)
            {
                $cdata = TiComputo::where('id', $request->caiv)->first();
                $total = $cdata->cantidad - $request->cantidad;
                $cdata->cantidad = $total;
                $cdata->condicion = 2;
                if ($total < 0)
                {
                    DB::rollBack();
                    return Status::Error2("No hay stock suficiente del material");
                }
                Auditoria::AuditarCambios($cdata);
                $cdata->update();
            }
            if ($request->tipo == 2)
            {
                $cdata = TiAccesorio::where('id', $request->caiv)->first();
                $total = $cdata->cantidad - $request->cantidad;
                $cdata->cantidad = $total;
                if ($total < 0)
                {
                    DB::rollBack();
                    return Status::Error2("No hay stock suficiente del material");
                }
                Auditoria::AuditarCambios($cdata);
                $cdata->update();
            }
            if ($request->tipo == 3)
            {
                $cdata = TiImpresion::where('id', $request->caiv)->first();
                $total = $cdata->cantidad - $request->cantidad;
                $cdata->cantidad = $total;
                $cdata->condicion = 2;
                if ($total < 0)
                {
                    DB::rollBack();
                    return Status::Error2("No hay stock suficiente del material");
                }
                Auditoria::AuditarCambios($cdata);
                $cdata->update();
            }
            if ($request->tipo == 4)
            {
                $cdata = TiVideo::where('id', $request->caiv)->first();
                $total = $cdata->cantidad - $request->cantidad;
                $cdata->cantidad = $total;
                $cdata->condicion = 2;
                if ($total < 0)
                {
                    DB::rollBack();
                    return Status::Error2("No hay stock suficiente del material");
                }
                Auditoria::AuditarCambios($cdata);
                $cdata->update();
            }

            $aux_accesorios = json_decode($request->accesorios);
            /// SE REGITRA LOS ACCESORIOS
            foreach ($aux_accesorios as $value_a)
            {
                $adata = TiAccesorio::where('id', $value_a->id)->first();
                $total = $adata->cantidad - $value_a->cantidad;
                $adata->cantidad = $total;
                $adata->update();
            }

            $resguardo->accesorios_listado = json_encode($request->accesorios);
            $resguardo->save();

            DB::commit();
            return Status::Success();
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return Status::Error($e, "guardar el resguardo");
        }
    }

    /**
     * Descargar Vale
     */
    public function DescargarValeResguardo($id)
    {
        try
        {
            $data = TiMaterialResguardo::join('empleados AS e', 'e.id', 'ti_material_resguardo.empleado_recibe')
                ->join('empleados AS ea', 'ea.id', 'ti_material_resguardo.empleado_entrega')
                ->select(
                    'ti_material_resguardo.*',
                    DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS empleado_r"),
                    DB::raw("CONCAT(ea.nombre,' ',ea.ap_paterno,' ',ea.ap_materno) AS empleado_e")
                )
                ->where('ti_material_resguardo.id', $id)
                ->first();

            $anio = substr($data->fecha, 0, 4);
            $mes = substr($data->fecha, 5, -3);
            $dia = substr($data->fecha, 8);

            $fecha_fin = $dia . ' / ' . Utils::NombreMesNumero($mes) . ' / ' . $anio;

            if ($data->tipo == 1)
            {
                $cdata = TiComputo::select('ti_computo.*', DB::raw("CONCAT(cpu,' ',ram,' ',almacenamiento,' ',tarjeta_video,' ',tarjeta_red,' ',observaciones,' ',mac) AS caracteristicas"))
                    ->where('id', $data->caiv)->first();
            }
            if ($data->tipo == 2)
            {
                $cdata = TiAccesorio::select('ti_accesorios.*', DB::raw("CONCAT(descripcion,' ',modelo,' ',marca,' ',no_serie) AS caracteristicas"))
                    ->where('id', $data->caiv)->first();
            }
            if ($data->tipo == 3)
            {
                $cdata = TiImpresion::select(
                    'ti_impresoras.*',
                    DB::raw("CONCAT(descripcion,' ',modelo,' ',marca,' ',no_serie) AS caracteristicas")
                )
                    ->where('id', $data->caiv)->first();
            }
            if ($data->tipo == 4)
            {
                $cdata = TiVideo::select('ti_video.*', DB::raw("CONCAT(descripcion,' ',modelo,' ',marca,' ',no_serie) AS caracteristicas"))
                    ->where('id', $data->caiv)->first();
            }

            $emp_aux = "";

            $tipo = json_decode($data->accesorios_listado);
            if (gettype($tipo) == "string")
            {
                $accesorios = json_decode($tipo);
            }
            else
            {
                $accesorios = $tipo;
            }

            $nombre_pdf = $data->empresa == 1 ? "pdf.ti.valeresguardoti" : "pdf.ti.valeresguardoticsct";
            $emp_aux = $data->empresa == 1 ? "CONSERFLOW_PTI-01_F-02" : "CSCT_TI-01_F-04";
            $pdf = Facade::loadView($nombre_pdf, compact('data', 'accesorios', 'fecha_fin', 'cdata'));
            $pdf->setPaper('letter', 'portrait');
            $nombre = $emp_aux . '.pdf';
            return $pdf->stream($nombre);
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }

    /**
     * Actualizar vale de resguardo. (Solo observaciones y empleado entrega)
     */
    public function ActualizarResguardo(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect("/");
            $resguardo = TiMaterialResguardo::find($request->id);
            $datos = LimpiarInput::LimpiarCampos($request->all(), ["observacion_uno", "observacion_dos"]);
            $resguardo->observacion_uno = $datos["observacion_uno"];
            $resguardo->observacion_dos = $datos["observacion_dos"];
            $resguardo->empleado_entrega = $datos["empleado_entrega"];
            Auditoria::AuditarCambios($resguardo);
            $resguardo->update();

            return Status::Success();
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return Status::Error($e, "guardar el resguardo");
        }
    }

    /**
     * Retornar el equipo
     */
    public function RegresarResguardo(Request $request)
    {
        try
        {
            DB::beginTransaction();
            // Liberar resguardo
            $resguardo = TiMaterialResguardo::find($request->id);
            $resguardo->estado = $request->cantidad_defectuoso == 0 ? 2 : 3; // Liberado
            $resguardo->observacion_recepcion = $request->observacion_recepcion; // Observaciones de repepcion
            $resguardo->fecha_retorno = $request->fecha_retorno;
            $resguardo->update();

            // Actualizar inventario
            $tablas = ["ti_computo", "ti_accesorios", "ti_impresoras", "ti_video", "ti_red"];
            $tbl = $tablas[$resguardo->tipo - 1];

            if ($request->cantidad_defectuoso == 0)
            {

                // Buscar equipo en inventario
                $n = DB::table($tbl)->where("id", $resguardo->caiv)->first()->cantidad;

                $equipo = DB::table($tbl)->where("id", $resguardo->caiv)
                    ->update([
                        "cantidad" => ($n + $resguardo->cantidad),
                        "condicion" => 1
                    ]);
            }
            else
            {
                // Desactivamos el articulo
                // Buscar equipo en inventario
                $n = DB::table($tbl)->where("id", $resguardo->caiv)->first()->cantidad;

                $equipo = DB::table($tbl)->where("id", $resguardo->caiv)
                    ->update([
                        "cantidad" => ($n + $resguardo->cantidad),
                        "condicion" => 0
                    ]);
            }
            DB::commit();
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "regresar el equipo");
        }
    }

    /**
     * Autoriza la entrega mediante el QR del empleado asigando
     */
    public function Autorizar(Request $request)
    {
        try
        {
            // Obtener entrega
            $entrega = TiMaterialResguardo::find($request->resguardo_id);

            // Comprobar empleado de entrega/autoriza
            if ($entrega->empleado_recibe == $request->empleado_id)
            {
                // ok
                $entrega->autorizado = 1;
                Utilidades::auditar($entrega, $entrega->id);
                $entrega->update();
                return Status::Success();
            }
            else
            {
                // No coincide
                return response()->json(["status" => false, "mensaje" => "No autorizado"]);
            }
        }
        catch (Exception $e)
        {
            return Status::Error($e, "autorizar le entrega");
        }
    }

    /**
     * Descargar todos los reesguardos pendientes
     */
    public function DescargarTodos()
    {
        try
        {
            ob_end_clean();
            ob_start();
            return Excel::download(new ResguardosExport(), 'Equipos_Resguardos.xlsx');
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
