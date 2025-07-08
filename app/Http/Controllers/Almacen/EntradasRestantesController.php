<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Support\Facades\DB;

class EntradasRestantesController extends Controller
{
    /**
     * Obtiene las OC pendientes por cerrar
     */
    public function OCPendientes()
    {
        try
        {
            $ocs = DB::table("entradas as e")
                ->join(
                    "requisicion_has_ordencompras as rho",
                    "rho.orden_compra_id",
                    "e.orden_compra_id"
                )
                ->join("ordenes_compras as oc", "oc.id", "e.orden_compra_id")
                ->select("oc.folio as oc")
                ->where("rho.cantidad_entrada", ">", 0)
                ->whereRaw("rho.articulo_id is not null")
                ->orderBy("oc")
                ->distinct()
                ->get();
            foreach ($ocs as $oc)
            {
                $n = DB::table("entradas as e")
                    ->join(
                        "requisicion_has_ordencompras as rho",
                        "rho.orden_compra_id",
                        "e.orden_compra_id"
                    )
                    ->join("ordenes_compras as oc", "oc.id", "e.orden_compra_id")
                    ->selectRaw("count(*) as n")
                    ->where("rho.cantidad_entrada", ">", 0)
                    ->where("oc.folio", $oc->oc)
                    ->first();
                $oc->n = $n->n;
            }
            return Status::Success("ocs", $ocs);
        }
        catch (Exception $e)
        {
            dd($e);
            return Status::Error($e, "obtener las entradas pendientes");
        }
    }
}
