<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\Exports\VacacionesExport;
use App\Http\Controllers\Auditoria;
use App\Jobs\ProcessPeriodoVacaciones;
use App\RHModels\PeriodoVacaciones;
use App\RHModels\VacacionesEmpleados;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class VacacionesEmpleadoController extends Controller
{

    /**
     * Obtener todos los empleados y vacaciones disponibles
     */
    public function ObtenerVacacionesEmpleado($anio)
    {
        try
        {
            // Empleados activos
            $aux_empleados = DB::table("empleados as e")
                ->where("e.condicion", 1)
                ->select(
                    "e.id",
                    "e.id as empleado_id",
                    DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as empleado")
                )
                ->orderBy("empleado")
                ->get();

            $empleados = [];
            // Obtener contratos
            foreach ($aux_empleados as $e)
            {
                $contrato = DB::table("contratos as c")
                    ->leftJoin("puestos as p", "p.id", "c.puesto_id")
                    ->select("c.id", "c.fecha_ingreso", "p.nombre as puesto")
                    ->where("c.empleado_id", $e->id)
                    ->whereRaw("year(c.fecha_ingreso)<=?", [$anio]) // Año ingresado
                    ->where("c.condicion", 1)
                    ->orderBy("c.fecha_ingreso", "desc")
                    ->first();

                if ($contrato == null) continue; // No tiene contrato:no se sabe fechas

                $ganados = 0;
                $tomados = 0;
                $dias = 0;

                $periodo = PeriodoVacaciones::byEmpleadoPeriodo($e->id, $anio)->first();
                if ($periodo) // Sacar los tomados del periodo 
                {
                    $ganados = $periodo->dias_ganados;
                    $dias = $periodo->dias_disponibles;
                    $tomados = $ganados - $dias;
                }
                // Resto 
                $dias = $ganados - $tomados;

                // Fecha de ingreso - fecha actual
                $e = (array) $e;
                $e["puesto"] = $contrato->puesto;
                $e["fecha_ingreso"] = $contrato->fecha_ingreso;
                $e["contrato_id"] = $contrato->id;
                $e["dias_ganados"] = $ganados;
                $e["dias_tomados"] = $tomados;
                $e["dias_disponibles"] = $dias;
                $empleados[] = $e;
            }

            return Status::Success("empleados", $empleados);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los empleados");
        }
    }

    /**
     * Crea un registro de vacaciones del empleado seleccionado
     */
    public function GuardarVacaciones(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');

            // Validar los días ingresados con los disponibles
            // Dias totales
            $inicio = strtotime($request->fecha_inicio);
            $fin = strtotime($request->fecha_fin);

            $fechas = [];
            $domingos = 0;
            for ($i = $inicio; $i <= $fin; $i += 86400) // 86400 = 1 dia
            {
                $dia_n = date("N", $i);
                if ($dia_n == 7) // Eliminar los Domingos
                {
                    $domingos++;
                    continue;
                }
                $fechas[] = date("Y-m-d", $i);
            }

            // Descontar dias festivos
            $festivos = DB::table("rh_dias_festivos as rdf")
                ->whereBetween("dia", [$request->fecha_inicio, $request->fecha_fin])
                ->count();

            // Días totales
            $dias = count($fechas) - $festivos;

            if ($request->dias_a_tomar != $dias)
            {
                $mensaje = "Los días solicitados y los días calculados no coindicen
                <br><br>
                Días solicitados: $request->dias_a_tomar <br>
                Días festivos: $festivos <br>
                Días calculados: $dias <br>";
                return response()->json(["status" => false, "tipo" => 2, "mensaje" => $mensaje]);
            }

            $periodo = PeriodoVacaciones::byEmpleadoPeriodo($request->empleado_id, $request->periodo)
                ->first();
            if (!$periodo)
            {
                return Status::Error2("Periodo no encontrado");
            }

            DB::beginTransaction();

            // Registrar
            $vacaciones = new VacacionesEmpleados($request->all());
            $vacaciones->empleado_registra_id = Auth::user()->empleado_id;
            $vacaciones->periodo = $request->periodo;
            $vacaciones->save();
            Auditoria::AuditarCambios($vacaciones);

            // Actualizar periodo
            $periodo->dias_disponibles = ($periodo->dias_disponibles - $dias);
            Auditoria::AuditarCambios($periodo);
            $periodo->update();

            DB::commit();
            return Status::Success();
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return Status::Error($e, "guardar las vacaciones");
        }
    }

    /**
     * Muestra el historial de las vacaciones del empleado ingreado
     */
    public function VerHistorial($id)
    {
        try
        {
            // Obtener años
            $anios = DB::select("SELECT DISTINCT (periodo) as anio 
            from rh_vacaciones_empleados rve where empleado_id =?", [$id]);

            $historial = [];
            foreach ($anios as $a)
            {
                $vacaciones = DB::table("rh_vacaciones_empleados as rve")
                    ->select("rve.fecha_inicio", "rve.fecha_fin", "rve.dias_a_tomar as dias_tomados")
                    ->where("rve.empleado_id", $id)
                    ->whereRaw("rve.periodo", [$a->anio])
                    ->get();
                $total_dias = DB::table("rh_vacaciones_empleados as rve")
                    ->selectRaw("sum(dias_a_tomar) as dias_tomados")
                    ->where("rve.empleado_id", $id)
                    ->whereRaw("rve.periodo", [$a->anio])
                    ->get();

                $historial[] = [
                    "anio" => $a->anio,
                    "total_dias" => $total_dias[0]->dias_tomados,
                    "fechas" => $vacaciones
                ];
            }
            return Status::Success("historial", $historial);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener el historial de vacaciones");
        }
    }

    /**
     * Generar reporte
     */
    public function Reporte($data)
    {
        try
        {
            ob_end_clean();
            ob_start();
            return Excel::download(new VacacionesExport($data), "Vacaciones.xlsx");
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return  view("errors.500");
        }
    }

    /**
     * Obtener los periodos de vacaciones del empleado
     */
    public function ObtenerPeriodos(Request $request)
    {
        try
        {
            $periodos = PeriodoVacaciones::byEmpleado($request->empleado_id)->get();
            return Status::Success("periodos", $periodos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los periodos");
        }
    }
}
