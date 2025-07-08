<?php

namespace App\Http\Controllers\TI;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\TIModels\ConsumibleMtto;
use App\TIModels\ReporteMttoPreventivo;
use App\TIModels\TiConsumible;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class TiMantenimientoController extends Controller
{

    /**
     * Obtener todos los mantenimientos registrados
     */
    public function ObtenerMttos()
    {
        try
        {
            $mttos = $this->ObtenerMtto_Aux(); // Todos
            return Status::Success("mantenimientos", $mttos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los reportes de mantenimiento");
        }
    }

    /**
     * Aux para obtener los mttos preventivos
     */
    public function ObtenerMtto_Aux($id = 0)
    {
        try
        {
            // Obtenre los reportes de Mtto preventivo
            $mttos = DB::table("ti_mtto_preventivo as tmp")
                ->join("empleados as e1", "e1.id", "tmp.empleado_asignado")
                ->join("empleados as e2", "e2.id", "tmp.empleado_autoriza")
                ->select(
                    "tmp.*",
                    DB::raw("CONCAT_WS(' ',e1.nombre,e1.ap_paterno,e2.ap_materno) as empleado_sigado"),
                    DB::raw("CONCAT_WS(' ',e2.nombre,e2.ap_paterno,e2.ap_materno) as empelado_revisa")
                );

            if ($id == 0) // Obtener todos
            {
                $mttos = $mttos->get();
            }
            else
            {
                $mttos = $mttos->where("tmp.id", $id)->get()->toArray();
            }

            $tablas_equipos = ["", "ti_computo as tc", "ti_impresoras as ti", "ti_red as tr"];
            $mttos_aux = [];

            // Obtener equipos
            foreach ($mttos as $m)
            {
                // Obtener los datos del eequipo (m->equipo_id) de acuerdo al tipo (m->tipo_equipo)
                $equipo = DB::table($tablas_equipos[$m->tipo_equipo])
                    ->find($m->equipo_id, $this->ObtenerColums($m->tipo_equipo));

                // Refacciones
                $consumible = DB::table("ti_consumible_mtto as tcm")
                    ->join("ti_consumibles as tc", "tc.id", "tcm.consumible_id")
                    ->where("tcm.tipo_mtto", 1) // Preventivo
                    ->where("tcm.mtto_id", $m->id)
                    ->select(
                        "tc.*",
                        DB::raw("1 as checked")
                    )->get();

                $mttos_aux[] = ["mtto" => $m, "equipo" => $equipo, "consumibles" => $consumible];
            }

            return $mttos_aux;
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los reportes de mantenimiento");
        }
    }

    /**
     * Obtiene las columnas de los equipos
     */
    private function ObtenerColums($id)
    {
        $cols = [
            [ // Computo
                "tc.id",
                "tc.no_serie",
                "tc.marca_modelo",
                DB::raw(
                    'CONCAT_WS(" - ",tc.marca_modelo,tc.color,tc.almacenamiento, tc.mac) as descripcion'
                ),
                DB::raw("1 as tipo")
            ],
            [ // Impresoras
                "ti.id",
                "ti.no_serie",
                DB::raw(
                    'CONCAT_WS(" - ",ti.marca,ti.modelo) as marca_modelo'
                ),
                "ti.descripcion",
                DB::raw("2 as tipo")
            ],
            [ // Red
                "tr.id",
                "tr.no_serie",
                "tr.marca as marca_modelo",
                "tr.descripcion",
                DB::raw("3 as tipo")
            ]
        ];
        return $cols[$id - 1];
    }

    /**
     * Obtiene los equipos para los mantenimientos del tipo ingresado
     */
    public function ObtenerEquipos($tipo)
    {
        try
        {
            if ($tipo == 1) // Equipo de cómputo
                $equipos = DB::table("ti_computo as tc")
                    ->select($this->ObtenerColums(1))
                    ->get()->toArray();
            else if ($tipo == 2) // Impresoras
                $equipos = DB::table("ti_impresoras as ti")
                    ->select($this->ObtenerColums(2))
                    ->get()->toArray();
            else // Red
                $equipos = DB::table("ti_red as tr")
                    ->select($this->ObtenerColums(3))
                    ->get()->toArray();

            return Status::Success("equipos", $equipos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los equipos");
        }
    }

    /**
     * Guardar el reporte de mantenimiento
     */
    public function Guardar(Request $request)
    {
        try
        {
            DB::beginTransaction();
            if ($request->id == null) // nuevo
            {
                // reporte
                $mtto = new ReporteMttoPreventivo($request->all());
                Utilidades::auditar($mtto, 0);
                $mtto->save();

                // Refacciones
                $list_consumibles = explode("&", $request->list_consumibles);
                array_pop($list_consumibles);
                foreach ($list_consumibles as $c)
                {
                    $cons = new ConsumibleMtto();
                    $cons->consumible_id = $c;
                    $cons->mtto_id = $mtto->id;
                    $cons->tipo_mtto = 1; // Preventivo
                    $cons->save();
                }
            }
            else // Actualizar
            {
            }
            DB::commit();
            return Status::Success();
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return Status::Error($e, "guardar el mantenimiento");
        }
    }

    /**
     * Obtener los consumibles
     */
    public function ObtenerConsumibles()
    {
        try
        {
            $consumibles = DB::table("ti_consumibles as tc")
                ->orderBy("tc.nombre")
                ->get();
            return Status::Success("consumibles", $consumibles);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los consumibles");
        }
    }

    /**
     * Registra un nuevo consumible
     */
    public function RegistarConsumible(Request $request)
    {
        try
        {
            $consumible = new TiConsumible($request->all());
            $consumible->save();
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "registrar el consumible");
        }
    }

    /**
     * Obtener el personal de TI
     */
    public function ObtenerPersonal()
    {
        try
        {
            $personal = DB::table("empleados as e")
                ->join("puestos as p", "p.id", "e.puesto_id")
                ->join("departamentos as d", "d.id", "p.departamento_id")
                ->where("d.id", 3)
                ->where("e.condicion", 1)
                ->select("e.id", DB::raw("CONCAT_WS(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre"))
                ->get();
            return Status::Success("empleados", $personal);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener el personal de TI");
        }
    }

    /**
     * Cargar mantenimientos
     */
    public function CargarMttosPreven(Request $request)
    {
        DB::beginTransaction();
        try
        {
            if (!($request->token == "#Yolo123dx")) return "Nel, prro";
            $mttos = (new FastExcel)->import($request->file('mantenimientos')->getRealPath());

            foreach ($mttos as $m)
            {
                $nuevo = new ReporteMttoPreventivo();
                $nuevo->fecha = $m["FECHA"];
                $equipo = $this->BuscarIdEquipo($m["NS"]);
                $nuevo->equipo_id = $equipo->id;
                $nuevo->tipo_equipo = $equipo->tipo;
                $nuevo->hora_inicio = $m["HORA_INICIO"];
                $nuevo->hora_final = $m["HORA_FIN"];
                $nuevo->empleado_asignado = $m["RESPONSABLE"];
                $nuevo->empleado_autoriza = $m["VALIDA"];
                $nuevo->actividades = $m["ACTIVIDADES"];
                $nuevo->observaciones = $m["OBSERVACIONES"];
                $nuevo->save();

                // Refacciones/Csoncumibles
                $refacciones = explode(",", $m["REFACCIONES"]);
                foreach ($refacciones as $f)
                {
                    if ($f == "NA") $f = 9;
                    $cons = new ConsumibleMtto();
                    $cons->consumible_id = $f;
                    $cons->tipo_mtto = 1; // Preventivo
                    $cons->mtto_id = $nuevo->id;
                    $cons->save();
                }
            }
            DB::commit();
            echo "ok";
        }
        catch (Exception $e)
        {
            db::rollBack();
        }
    }

    /**
     * Obtiene las columas de los equipos 
     */
    private function BuscarIdEquipo($ns)
    {
        // Buscar en Computo
        $equipo = DB::table("ti_computo as tc")->where("no_serie", $ns)
            ->first(["id", DB::raw("1 as tipo")]);
        if ($equipo == null)
        {
            $equipo = DB::table("ti_impresoras as tc")->where("no_serie")
                ->first(["id", DB::raw("2 as tipo")]);
            if ($equipo == null)
            {
                // Buscar envideo
                $equipo = DB::table("ti_video as tv")->where("no_serie", $ns)
                    ->first(["id", DB::raw("3 as tipo")]);
            }
        }
        if ($equipo == null)
        {
            throw new Exception("NOT FOUND");
        }
        return $equipo;
    }

    /**
     * Genera el reporte en PDF del mtto preventivo
     */
    public function GenerarReporte($id)
    {
        try
        {
            $mtto = $this->ObtenerMtto_Aux($id)[0];
            $pdf = PDF::loadView('pdf.ti.timttopreventivo', compact("mtto"));
            $pdf->setPaper("letter", "portrait");

            error_reporting(E_ALL ^ E_DEPRECATED);
            return $pdf->stream();
        }
        catch (Exception  $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
