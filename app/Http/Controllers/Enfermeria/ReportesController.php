<?php

namespace App\Http\Controllers\Enfermeria;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{

    /**
     * Obtener los departamentos para los reportes por area
     */
    public function ObtenerDeptos()
    {
        try
        {
            $departamentos = DB::table("enfermeria_atencion_medica as eam")
                ->join("puestos as p", "p.id", "eam.puesto_id")
                ->join("departamentos as d", "d.id", "p.departamento_id")
                ->select(
                    "d.id",
                    "d.nombre"
                )
                ->distinct("puesto->id")
                ->orderBy("nombre")
                ->get();
            return Status::Success("departamentos", $departamentos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los departamentos");
        }
    }

    /**
     * Obtiene los datos por periodo
     */
    public function ObtenerPorFecha($anio, $mes)
    {
        try
        {
            $where = "";
            $bindings = [];
            if ($anio == 0) // General (Todos los años)
            {
                $where = "where ?";
                $bindings = [1];
            }
            else if ($mes == 0) // Por año (Todos los meses del año)
            {
                $where = "where year(eam.fecha)=?";
                $bindings = [$anio];
            }
            else // Mes y año
            {
                $where = "where year(eam.fecha)=? and month(eam.fecha)=?";
                $bindings = [$anio, $mes];
            }

            $tabla = DB::select("SELECT ema.id,ema.nombre  as motivo,ema.tipo,
            COUNT(eam.empleado_id) as n_empleados,
            GROUP_CONCAT(DISTINCT(eam.medicamentos)) as medicamentos
            from enfermeria_atencion_medica eam
            left join enfermeria_motivo_atencion ema on ema.id =eam.motivo_id 
            $where
            group by eam.motivo_id 
            order by ema.id;", $bindings);

            $grafica = DB::select("SELECT ema.nombre  as motivo,
            COUNT(eam.empleado_id) as n_empleados
            from enfermeria_atencion_medica eam
            left join enfermeria_motivo_atencion ema on ema.id =eam.motivo_id 
            $where
            group by eam.motivo_id 
            order by motivo", $bindings);
            return Status::Success("data", ["tabla" => $tabla, "grafica" => $grafica]);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los datos");
        }
    }

    /**
     * Obtiene los datos por periodo
     */
    public function ObtenerPorDepartamento($dp_id)
    {
        try
        {
            $tabla = DB::select("SELECT ema.id,ema.nombre  as motivo,ema.tipo,
            COUNT(eam.empleado_id) as n_empleados,
            GROUP_CONCAT(DISTINCT(eam.medicamentos)) as medicamentos
            from enfermeria_atencion_medica eam
            left join enfermeria_motivo_atencion ema on ema.id =eam.motivo_id
            left join puestos as p on p.id=eam.puesto_id
            left join departamentos as d on d.id=p.departamento_id
            where d.id = ?
            group by eam.motivo_id 
            order by ema.id;", [$dp_id]);

            $grafica = DB::select("SELECT ema.nombre  as motivo,
            COUNT(eam.empleado_id) as n_empleados
            from enfermeria_atencion_medica eam
            join enfermeria_motivo_atencion ema on ema.id =eam.motivo_id 
            left join puestos as p on p.id=eam.puesto_id
            left join departamentos as d on d.id=p.departamento_id
            where d.id = ?
            group by eam.motivo_id 
            order by motivo", [$dp_id]);

            return Status::Success("data", ["tabla" => $tabla, "grafica" => $grafica]);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los datos");
        }
    }
}
