<?php

namespace App\Http\Controllers\Almacen;

use App\AlmaceModels\Inventario;
use App\Exports\AlmacenInventarioExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Registro de inventario de almacén
 */
class InventarioController  extends Controller
{

    /**
     * Obtiene todos los registros del almacén
     */
    public function Obtener($p_id)
    {
        try
        {
            $com = "=";
            if ($p_id == 0)
            {
                $com = ">=";
                $p_id = 1;
            }
            $existencias = DB::table("alm_inventario as ai")
                ->join("articulos as a", "a.id", "ai.articulo_id")
                ->join("proyectos as p", "p.id", "ai.proyecto_id")
                ->where("p.id", $com, $p_id)
                ->select(
                    "ai.existencia_real as existencia_registrada",
                    "a.descripcion",
                    "p.nombre_corto"
                )->get();
            return Status::Success("existencias", $existencias);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener el inventario");
        }
    }
    /**
     * Guarda un articulo para inventario
     */
    public function Guardar(Request $request)
    {
        try
        {
            $inv = new Inventario($request->all());
            $inv->empleado_id = Auth::user()->empleado_id; //Empleado que registra
            $inv->fecha = date("y-m-d"); // Fecha actual
            $inv->save();
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el inventario");
        }
    }

    /**
     * Buscar artículos mediante descripción
     */
    public function BuscarDesc($desc)
    {
        try
        {
            $existencias = DB::table("lote_almacen as la")
                ->join("articulos as a", "a.id", "la.articulo_id")
                ->join("stock_articulos as sa", "sa.articulo_id", "la.articulo_id")
                ->join("stocks as s", "sa.stocke_id", "s.id")
                ->join("proyectos as p", "p.id", "s.proyecto_id")
                ->where("a.descripcion", "like", "%$desc%")
                ->select(
                    "la.articulo_id",
                    "p.id as proyecto_id",
                    "a.descripcion as articulo",
                    "sa.cantidad as existencia_sistema",
                    "p.nombre_corto as nombre_proyecto"
                )->distinct()->get();
            return Status::Success("existencias", $existencias);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener las existencias");
        }
    }

    /**
     * Busca el artículo con el Código ingresado
     */
    public function BuscarCodigo($codigo)
    {
        try
        {
            // Artículo
            $articulo = DB::table("lote_almacen as la")
                ->where("la.codigo_barras", $codigo) // Codigo
                ->select(
                    "la.codigo_barras",
                    "la.articulo_id"
                )->first();

            // Buscar las existencias del artículo  en todos los proyectos
            $existencia = DB::table("stocks as s")
                ->join("stock_articulos as sa", "sa.stocke_id", "s.id")
                ->join("proyectos as p", "p.id", "s.proyecto_id")
                ->join("articulos as a", "a.id", "sa.articulo_id")
                ->where("sa.articulo_id", $articulo->articulo_id) // articulo
                ->select(
                    "p.nombre_corto as nombre_proyecto",
                    "p.id as proyecto_id",
                    "sa.articulo_id",
                    "a.descripcion as articulo",
                    "sa.cantidad as existencia_sistema"
                )->get();
            return Status::Success("existencias", $existencia);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener el artículo");
        }
    }

    /**
     * Genera el reporte en Excel del inventario de almacén
     */
    public function Reporte($p_id)
    {
        try
        {
            ob_end_clean();
ob_start();
            return Excel::download(new AlmacenInventarioExport(0), 'Invertario Almacén.xlsx');
        }
        catch (Exception $e)
        {
            Status::Error($e, "generar el reporte");
            return view("errors.500");
        }
    }
}
