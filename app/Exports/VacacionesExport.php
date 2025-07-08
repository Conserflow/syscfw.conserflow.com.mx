<?php

namespace App\Exports;

use App\Http\Controllers\Otros\Styles;
use App\Http\Helpers\Utilidades;
use DateTime;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class VacacionesExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {


        return [
            //todos los estilos los cargas aqui
            AfterSheet::class => function (AfterSheet $event)
            {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');
                $drawing->setPath(public_path('img/conserflow.png'));
                $drawing->setCoordinates('A2');
                $drawing->setHeight(50);
                $drawing->setWorksheet($event->sheet->getDelegate());

                $event->sheet->getStyle('B2:K2')->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle('B2:K2')->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle('B2:K2')->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle('A5:K5')->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle('A5:K5')->applyFromArray(Styles::$HORIZONTAL_CENTER);

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(35);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(24);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(16);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(16);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(18);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(8);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(30);

                $event->sheet->getDelegate()->getRowDimension('2')->setRowHeight(15);
                $event->sheet->getDelegate()->getRowDimension('5')->setRowHeight(20);
            },
        ];
    }

    public function view(): View
    {
        $dts = explode("&", $this->data);
        $inicio = $dts[0];
        $fin = $dts[1];
        $empresa_id = $dts[2];

        $aux_checador = $empresa_id == 1 ? [1, 2] : [3, 4];

        // Empleados con vacaciones en esa fecha
        $aux_empleados = DB::table("rh_vacaciones_empleados as rve")
            ->join("empleados as e", "e.id", "rve.empleado_id")
            ->select(
                "rve.*",
                DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as empleado")
            )
            ->whereBetween("rve.fecha_inicio", [$inicio, $fin])
            ->whereBetween("e.id_checador", [$aux_checador])
            ->orderBy("empleado")
            ->orderBy("fecha_inicio")
            ->get();

        $empleados = [];
        // Obtener contrato registrado y su sueldo
        foreach ($aux_empleados as $e)
        {
            // Último salario del contrato actual
            $contrato = DB::table("contratos as c")
                ->leftJoin("sueldos as s", "s.contrato_id", "c.id")
                ->leftJoin("puestos as p", "p.id", "c.puesto_id")
                ->where("c.id", $e->contrato_id)
                ->select("c.id","c.fecha_ingreso", "p.nombre as puesto", "s.sueldo_diario_integral")
                ->orderBy("s.fecha_act")
                ->first();

            // Días ganados
            // Obtener años trabajados y dias de vacaciones
            $inicio = new DateTime($contrato->fecha_ingreso);
            $hoy = new DateTime(date("Y-m-d"));
            $dif = $inicio->diff($hoy);
            $anios = $dif->y;


            //Total de días en base a los años
            $ganados = $this->ObtenerDias($anios);

            $tomados = $this->ObtenerDiasTomados($contrato->id, $e->fecha_inicio);
            $disponibles = $ganados - $tomados;

            $emp = (array)$e;
            $contrato = (array)$contrato;
            $emp["dias_ganados"] = $ganados;
            $emp["dias_disponibles"] = $disponibles;
            $emp["monto_vacaciones"] = $contrato["sueldo_diario_integral"] * $emp["dias_a_tomar"];
            $emp = array_merge($emp, $contrato);

            $emp = (object) $emp;
            $empleados[] = $emp;
        }
        return view("excel.RH.vacaciones", compact("empleados"));
    }

    /**
     * Calcula los días de vacaciones en base a los años trabajados
     * @param int $anios Años trabajados
     */
    private function ObtenerDias($anios)
    {
        $dias = 0;
        if ($anios < 1) $dias = 0; // TODO: Medio año??
        else if ($anios == 1) $dias = 6;
        else if ($anios == 2) $dias = 8;
        else if ($anios == 3) $dias = 10;
        else if ($anios == 4) $dias = 12;
        else if ($anios >= 5 & $anios <= 9) $dias = 14;
        else if ($anios >= 10 & $anios <= 14) $dias = 16;
        else if ($anios >= 15 & $anios <= 19) $dias = 18;
        else if ($anios >= 20 & $anios <= 24) $dias = 20;
        else if ($anios >= 25 & $anios <= 29) $dias = 22;
        else if ($anios >= 30 & $anios <= 34) $dias = 24;
        else $dias = -1;
        return $dias;
    }

    /**
     * Obtiene los días tomados del empleado y año ingresado
     * @param int $contrato_id Id del contrato del empleado
     * @param int $anio Año
     */
    private function ObtenerDiasTomados($contrato_id, $fecha)
    {
        try
        {
            $dias_tomados = DB::table("rh_vacaciones_empleados as rve")
                ->where("rve.contrato_id", $contrato_id)
                ->whereRaw("rve.fecha_inicio < ?", [$fecha])
                ->sum("rve.dias_a_tomar");
            return $dias_tomados;
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            dd($e);
        }
    }
}
