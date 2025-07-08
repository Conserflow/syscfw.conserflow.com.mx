<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Support\Facades\DB;

class AniversarioController extends Controller
{
    /**
     * Otbener los empleados con aniversario en el mes actual
     */
    public function ObtenerEmpleados()
    {
        try
        {
            $empleados = DB::table("empleados as e")
                ->orderBy("nombre")
                ->select(
                    "e.id",
                    "e.nombre",
                    "e.ap_paterno",
                    "e.ap_materno"
                )
                ->where("e.condicion", 1)
                ->get();
            $mes = date("m");
            $anio = date("Y");
            $list_empleados = [];
            foreach ($empleados as $e)
            {
                $contrato = DB::table("contratos as c")
                    ->select(
                        "c.fecha_ingreso",
                        "c.fecha_fin",
                        "p.nombre as puesto",
                        DB::raw("year(curdate())-year(c.fecha_ingreso) as anios")
                    )
                    ->leftJoin("puestos as p", "p.id", "c.puesto_id")
                    ->where("c.empleado_id", $e->id)
                    ->whereRaw("month(c.fecha_ingreso) = ?", [$mes])
                    ->whereRaw("year(c.fecha_ingreso) < ?", [$anio])
                    ->where("c.condicion", 1)
                    ->first();
                if ($contrato == null) continue;
                $list_empleados[] = [
                    "nombre" => $e->nombre,
                    "ap_paterno" => $e->ap_paterno,
                    "ap_materno" => $e->ap_materno,
                    "fecha_ingreso" => $contrato->fecha_ingreso,
                    "anios" => $contrato->anios,
                    "puesto" => $contrato->puesto
                ];
            }
            return Status::Success("empleados", $list_empleados);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los empleados");
        }
    }
}
