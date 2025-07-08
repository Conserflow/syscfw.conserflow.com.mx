<?php

namespace App\Jobs;

use App\Mail\AvisoEmail;
use App\RHModels\PeriodoVacaciones;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ProcessPeriodoVacaciones implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $ids_generados;
    private $titulo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->titulo = "ProcessPeriodoVacaciones";
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->GenerarPeriodos();
        $this->enviarCompletado();
    }

    /**
     * Generar los periodos de vacaciones de los empleados
     */
    public function GenerarPeriodos()
    {
        $actual = Carbon::now();
        $actual2 = clone $actual;

        // Obtener todos los activos
        $empleados = DB::table("empleados as e")
            ->orderBy("e.id")
            ->where("condicion", 1)
            ->whereYear("e.fech_ing", "<=", $actual->year)
            ->select(
                "e.id",
                "e.fech_ing as fecha_ingreso",
            )
            ->get();
        $generados = [];
        foreach ($empleados as $e)
        {
            $contrato = DB::table("contratos as c")
                ->select(
                    "c.fecha_ingreso",
                )
                ->where("c.empleado_id", $e->id)
                ->where("c.condicion", 1)
                ->first();

            if ($contrato == null) continue;

            // Comprobar si ya puede tener vacaciones
            $fecha_ingreso = Carbon::parse($e->fecha_ingreso);

            $diferencia = $actual->diff($fecha_ingreso);

            $periodo = PeriodoVacaciones::byEmpleadoPeriodo($e->id, $actual->year)
                ->first();

            if ($periodo) continue; // Ya registrado

            $anios = $diferencia->y;
            if ($anios == 0) continue; // Aun no tiene

            $dias_totales = $this->ObtenerDias($anios, $actual2->year);

            $periodo = new PeriodoVacaciones();
            $periodo->empleado_id = $e->id;
            $periodo->periodo  = $actual2->year;
            $periodo->anios  = $anios;
            $periodo->dias_ganados  = $dias_totales;
            $periodo->dias_disponibles  = $dias_totales;
            $periodo->save();
            $generados[] = $periodo->id;
        }
        $this->ids_generados = $generados;
    }

    private function enviarCompletado()
    {
        $asunto = "Periodo de vacaciones registrado";
        $ids = "";

        foreach ($this->ids_generados as $id)
        {
            $ids .= "$id-";
        }
        $total = count($this->ids_generados);

        if ($total == 0) $ids = "NINGUNO";

        $detalles = [
            [
                "mensaje" => "Registros totales: $total <br>
                ID's: $ids"
            ]
        ];
        Mail::to(config("app.mail_errores"))
            ->send(new AvisoEmail($this->titulo, false, $detalles, $asunto));
    }

    public function failed(Exception $e = null)
    {
        $detalles = [
            [
                "mensaje" => $e->getMessage()
            ]
        ];

        $asunto = "Error al crear el periodo";
        if ($e->getMessage() != "")
        {
            Mail::to(config("app.mail_errores"))
                ->send(new AvisoEmail($this->titulo, true, $detalles, $asunto));
        }
    }

    /**
     * Calcula los días de vacaciones en base a los años trabajados
     * @param int $anios Años trabajados
     */
    private function ObtenerDias($anios, $anio)
    {
        $dias = 0;
        if ($anios < 1) $dias = 0;
        else if ($anios == 1) $dias = 12;
        else if ($anios == 2) $dias = 14;
        else if ($anios == 3) $dias = 16;
        else if ($anios == 4) $dias = 18;
        else if ($anios == 5) $dias = 20;
        else if ($anios >= 5 & $anios <= 9) $dias = 14;
        else if ($anios >= 6 & $anios <= 10) $dias = 22;
        else if ($anios >= 11 & $anios <= 15) $dias = 24;
        else if ($anios >= 16 & $anios <= 20) $dias = 26;
        else if ($anios >= 21 & $anios <= 25) $dias = 28;
        else if ($anios >= 26 & $anios <= 30) $dias = 30;
        else if ($anios >= 31 & $anios <= 35) $dias = 32;
        else $dias = -1;
        if ($anio < 2023 & $dias > 0) $dias -= 6;
        return $dias;
    }
}
