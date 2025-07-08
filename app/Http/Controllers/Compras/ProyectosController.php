<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Support\Facades\DB;

class ProyectosController extends Controller
{
    /**
     * Obtener los proyectos activos con OC
     */
    public function GetProyectOC()
    {
        try
        {
            $proyectos = DB::table("ordenes_compras as oc")
                ->join("proyectos as p", "p.id", "oc.proyecto_id")
                ->select("oc.proyecto_id as id","p.nombre_corto")
                ->where("p.condicion", 1)
                ->orderBy("p.nombre_corto")
                ->distinct()
                ->get();
            return Status::Success("proyectos", $proyectos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los proyectos");
        }
    }
}
