<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\Proyecto;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GeneralController extends Controller
{

    /**
     * Obtiene los empleados actvivos
     */
    public function ObtenerEmpleados()
    {
        try
        {
            $empleados = DB::table("empleados as e")
                ->select(
                    "e.id",
                    DB::raw("CONCAT_WS(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre")
                )
                ->where("e.condicion", 1)
                ->orderBy("nombre")
                ->get();
            return Status::Success("empleados", $empleados);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los empleados");
        }
    }

    /**
     * Obtner proyectos (1. Activos |X. Todos)
     */
    public function ObtenerProyectos($condicion = 1)
    {
        try
        {
            $op = "=";
            if ($condicion != 1)
            {
                $op = "!=";
                $condicion = 0;
            }
            $proyectos = Proyecto::where("condicion", $op, $condicion)
                ->select("id", "nombre_corto")
                ->orderBy("nombre_corto")
                ->get();
            return Status::Success("proyectos", $proyectos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los proyectos");
        }
    }

    /**
     * Obtiene todos los puestos
     */
    public function ObtenerPuestos()
    {
        try
        {
            $puestos = DB::table("puestos")
                ->select("id", "nombre")
                ->orderBy("nombre")
                ->get();
            return Status::Success("puestos", $puestos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los puestos");
        }
    }

    /**
     * Obtener los empleados activos y su puesto
     */
    public function ObtenerEmpleadosPuestos()
    {
        try
        {
            $empleados_aux = DB::table("empleados as e")
                ->select(
                    "e.id",
                    DB::raw("CONCAT_WS(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre")
                )
                ->where("e.condicion", 1)
                ->orderBy("nombre")
                ->get();
            // Obtener el puesto (primero activo)
            $empleados = [];
            foreach ($empleados_aux as $e)
            {
                $contrato = DB::table("contratos as c")
                    ->join("puestos as p", "p.id", "c.puesto_id")
                    ->select(
                        "p.id as puesto_id",
                        "p.nombre as puesto"
                    )
                    ->where("c.empleado_id", $e->id)
                    ->where("c.condicion", 1)
                    ->first();
                if ($contrato == null) continue; // Sin contrato
                $empleados[] = array_merge((array)$e, (array)$contrato);
            }
            return Status::Success("empleados", $empleados);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los empleados");
        }
    }

    /**
     * Obtener todos los empleados existentes y el puesto. N/A en caso de no tener
     */
    public function ObtenerTodosEmpleados()
    {
        try
        {
            // Empleados
            $empleados = DB::table("empleados as e")
                ->select(
                    "e.id",
                    "e.id_checador",
                    DB::raw("CONCAT_WS(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre"),
                    DB::raw("0 as puesto_id"),
                    DB::raw("if(id_checador =0,'N/D',if(id_checador <=2,'CONSERFLOW', 'CSCT'))as empresa"),
                    DB::raw("'puesto' as puesto")
                )
                ->orderBy("nombre")
                ->get();
            // Obtener contratos
            foreach ($empleados as $e)
            {
                $puesto = DB::table("contratos as c")
                    ->join("puestos as p", "p.id", "c.puesto_id")
                    ->where("c.empleado_id", $e->id)
                    ->select("p.id", "p.nombre")
                    ->orderBy("id", "desc")->first();
                // Sin puesto, crear vacio
                if ($puesto == null)
                {
                    $e->puesto_id = -1;
                    $e->puesto = "N/D";
                }
                else
                {
                    $e->puesto_id = $puesto->id;
                    $e->puesto = $puesto->nombre;
                }
            }
            return Status::Success("empleados", $empleados);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los empleados");
        }
    }

    /**
     * Obtener el empleado actual
     */
    public function EmpleadoActual()
    {
        try
        {
            $user = Auth::user();
            $empleado = DB::table("empleados as e")
                ->where("id", $user->empleado_id)
                ->select(
                    "e.id",
                    DB::raw('concat_ws(" ", e.nombre,e.ap_paterno,e.ap_materno) as nombre')
                )
                ->get();
            return Status::Success("empleados", $empleado);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los datos del usuario");
        }
    }
}
