<?php

namespace App\Http\Controllers\Almacen;

use App\AlmaceModels\ArticulosGrupo;
use App\AlmaceModels\GrupoInventario;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Agrupar artículos de almacén para inventario
 */
class GrupoInventarioController extends Controller
{
    /**
     * Obtiene todos los grupos del almacnén para inventario
     */
    public function Obtener()
    {
        try
        {
            $grupos = GrupoInventario::get();
            return Status::Success("grupos", $grupos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los grupos");
        }
    }

    /**
     * Guarda un grupo de inventario
     */
    public function Guardar(Request $request)
    {
        try
        {
            if ($request->id == null)
            {
                $grupo = new GrupoInventario($request->all());
                $grupo->emp_registra_id = Auth::user()->empleado_id;
                $grupo->save();
            }
            else
            {
                $grupo = GrupoInventario::find($request->id);
                $grupo->fill($request->all());
                $grupo->save();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el grupo de inventario");
        }
    }

    /**
     *  Obtiene los artículos pendientes por agrupar
     */
    public function ObtenerPendientes()
    {
        try
        {
            // Obtener ya agrupados
            $agrupados = DB::table("alm_articulos_grupos as aag")
                ->join("alm_inventario as ai", "ai.id", "aag.art_inv_id")
                ->select("ai.id")->get()->toArray();
            $ids_agrupados = [];
            foreach ($agrupados as $id)
            {
                array_push($ids_agrupados, $id->id);
            }


            $articulos = DB::table("alm_inventario as ai")
                ->join("articulos as a", "a.id", "ai.articulo_id")
                ->select(
                    "ai.id as ai_id",
                    "a.descripcion"
                )
                ->whereNotIn("ai.id", $ids_agrupados)
                ->get();
            return Status::Success("articulos", $articulos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los artículos pendientes");
        }
    }


    /**
     * Obtien los artículos del grupo seleccionado
     */
    public function ObtenerPorGrupo($g_id)
    {
        $articulos = DB::table("alm_articulos_grupos as aag")
            ->join("alm_inventario as ai", "ai.id", "aag.art_inv_id")
            ->join("articulos as a", "a.id", "ai.articulo_id")
            ->where("aag.grupo_id", $g_id)
            ->orderBy("a.descripcion")
            ->select(
                "a.descripcion as nombre",
                "ai.existecia_sistema",
                "ai.existencia_real"
            )->get();
        return Status::Success("articulos", $articulos);
    }

    /**
     * Guardar articulos en el grupo
     */
    public function Agrupar(Request $request)
    {
        try
        {
            $empleado_id = Auth::user()->empleado_id;
            $articulos = explode(",", $request->list_articulos);
            foreach ($articulos as $articulo_id)
            {
                $grupo = new ArticulosGrupo();
                $grupo->grupo_id = $request->grupo_id;
                $grupo->art_inv_id = $articulo_id;
                $grupo->empleado_registra_id = $empleado_id;
                $grupo->save();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "agrupar los artículos");
        }
    }
}
