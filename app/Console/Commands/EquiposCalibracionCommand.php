<?php

namespace App\Console\Commands;

use App\Automaticos\Correos as AutomaticosCorreos;
use App\Http\Controllers\Otros\Correos;
use App\Http\Helpers\Utilidades;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EquiposCalibracionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "calidad:equiposcalibracion";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Notificación de los equipos de calibración vencidos";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try
        {
            $aux_equipos = DB::table("calidad_equipos_calibracion as cec")
                ->join("empleados as e", "e.id", "cec.empleado_revisa_id")
                ->select(
                    "cec.id",
                    "cec.equipo",
                    "cec.marca",
                    "cec.modelo",
                    "cec.ns",
                    "cec.rango_medicion",
                    "cec.resguardo",
                    "cec.frecuencia",
                    "cec.empleado_revisa_id",
                    "cec.observaciones",
                    "cec.tipo",
                    DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as revisa"),
                    DB::raw("0 as proxima_fecha"),
                    DB::raw("-1 as condicion")
                )
                ->get();

            $equipos_vencidos = [];
            $equipos_por_vencer = [];
            foreach ($aux_equipos as $e)
            {
                $calibracion = DB::table("calidad_calibraciones as cc")
                    ->where("cc.equipo_id", $e->id)
                    ->select(
                        "cc.proxima_fecha"
                    )
                    ->orderBy("id", "desc")
                    ->first();

                // Sin registro
                if ($calibracion == null)
                {
                    $equipos_vencidos[] = $e;
                }
                else
                {
                    // Vencidos
                    $hoy = date("Y-m-d");
                    if ($calibracion->proxima_fecha < $hoy)
                    {
                        $e->proxima_fecha = $calibracion->proxima_fecha;
                        $e->condicion = 0; // Vencido
                        $equipos_vencidos[] = $e;
                    }

                    // Por vencer
                    $proxima_aux = new DateTime($hoy);
                    $proxima_aux->modify("+30 day");
                    $proxima = $proxima_aux->format("Y-m-d");
                    if ($calibracion->proxima_fecha >= $hoy & $calibracion->proxima_fecha <= $proxima)
                    {
                        $e->proxima_fecha = $calibracion->proxima_fecha;
                        $e->condicion = 2; // Por vencer
                        $equipos_por_vencer[] = $e;
                    }
                }
            }

            $correos = AutomaticosCorreos::byTipo("calidad:equiposcalibracion")->get()
                ->pluck("correo")->toArray();
            if (config("app.debug"))
            {
                $correos = explode(",", config("app.mail_errores"));
            }
            $data = [
                "emails" => $correos,
                "equipos_vencidos" => $equipos_vencidos,
                "equipos_por_vencer" => $equipos_por_vencer,
                "asunto" => "REPORTE DE EQUIPOS DE CALIBRACIÓN - " . date("Y-m-d")
            ];
            // Nada por enviar
            if (count($equipos_vencidos) == 0 && count($equipos_por_vencer) == 0) return;
            Mail::send("emails.calidad.notificacionequipos", $data, function ($message) use ($data)
            {
                $message->to($data["emails"], "Nombre")
                    ->subject($data["asunto"]);
                $message->from("webmaster@conserflow.com", "Conserflow");
            });
        }
        catch (Exception $e)
        {
            Utilidades::errors($e, 2);
        }
    }
}
