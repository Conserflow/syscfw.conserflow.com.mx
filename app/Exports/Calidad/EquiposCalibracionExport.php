<?php

namespace App\Exports\Calidad;

use App\Http\Controllers\Otros\Styles;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Http\Helpers\Utilidades;
use Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class EquiposCalibracionExport implements FromView, WithEvents
{
    private $tipos_descargar;
    private $datos;
    private $omitidos;

    public function __construct($tipos_desargar)
    {
        $this->tipos_descargar = $tipos_desargar;
    }

    public function registerEvents(): array
    {
        $n = DB::table("calidad_equipos_calibracion as cec")
            ->select("cec.id")
            ->whereIn("cec.tipo", $this->tipos_descargar)
            ->count();
        $this->obtenerDatos();
        $omitidos = $this->omitidos;
        $n_tipos = count($this->tipos_descargar);
        $n1 = $n + 10 + $n_tipos - 1 - $omitidos;
        $n += 12 + 8;
        return [

            AfterSheet::class => function (AfterSheet $event) use ($n, $n1, $n_tipos)
            {
                $drawing = new Drawing();
                $drawing->setName("Logo");
                $drawing->setDescription("Logo");
                $drawing->setPath(public_path("img/conserflow.png"));
                $drawing->setCoordinates("B2");
                $drawing->setHeight(60);
                $drawing->setWorksheet($event->sheet->getDelegate());

                $event->sheet->getDelegate()->getColumnDimension("A")->setWidth(5);
                $event->sheet->getDelegate()->getColumnDimension("B")->setWidth(35);
                $event->sheet->getDelegate()->getColumnDimension("C")->setWidth(16);
                $event->sheet->getDelegate()->getColumnDimension("D")->setWidth(16);
                $event->sheet->getDelegate()->getColumnDimension("E")->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension("F")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("G")->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension("H")->setWidth(16);
                $event->sheet->getDelegate()->getColumnDimension("I")->setWidth(14);
                $event->sheet->getDelegate()->getColumnDimension("J")->setWidth(16);
                $event->sheet->getDelegate()->getColumnDimension("K")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("L")->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension("M")->setWidth(20);

                $event->sheet->getStyle("A8:F9")->applyFromArray(Styles::$BG_BLUE);
                $event->sheet->getStyle("G9:J9")->applyFromArray(Styles::$BG_GRAY);
                $event->sheet->getStyle("G8:J8")->applyFromArray(Styles::$BG_BLUE);
                $event->sheet->getStyle("K6:L6")->applyFromArray(Styles::$BG_BLUE);
                $event->sheet->getStyle("K8:M9")->applyFromArray(Styles::$BG_BLUE);

                $event->sheet->getStyle("A8:F9")->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle("G8:J8")->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle("G9:J9")->applyFromArray(Styles::$BG_GRAY);
                $event->sheet->getStyle("K6:L6")->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle("K8:M9")->applyFromArray(Styles::$FONT_WHITE);

                $event->sheet->getStyle("M1:M$n")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("J11:M$n")->applyFromArray(Styles::$HORIZONTAL_CENTER);

                $event->sheet->getStyle("A1:M9")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("A1:M9")->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("A1:M1")->applyFromArray(Styles::$FONT_BLUE);
                $event->sheet->getStyle("A8:M9")->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle("C2:K2")->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle("K6:L6")->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle("A1:M300")->applyFromArray(Styles::$FONT_ARIAL);

                $event->sheet->getStyle("A2:M4")->applyFromArray(Styles::$BORDER_ALL_THIN);
                $event->sheet->getStyle("A2:M4")->applyFromArray(Styles::$BORDER_EXTERNAL_BOLD);
                $event->sheet->getStyle("K6:L6")->applyFromArray(Styles::$BORDER_EXTERNAL_BOLD);
                $event->sheet->getStyle("M6")->applyFromArray(Styles::$BORDER_EXTERNAL_BOLD);
                $event->sheet->getStyle("A8:M$n1")->applyFromArray(Styles::$BORDER_ALL_THIN);
                $event->sheet->getStyle("A8:M$n1")->applyFromArray(Styles::$BORDER_EXTERNAL_BOLD);
                $event->sheet->getStyle("A8:M$n")->applyFromArray(Styles::$VERTICAL_CENTER);

                $n2 = $n + 2;
                $n3 = $n + 3;
                $event->sheet->getStyle("K$n2:L$n2")->applyFromArray(Styles::$BORDER_BOTTOM_THIN);
                $event->sheet->getStyle("K$n2:L$n2")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("K$n3:L$n3")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("K$n3:L$n3")->applyFromArray(Styles::$FONT_BOLD);

                for ($i = 11; $i < $n; $i++)
                {
                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(20);
                }
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(18);
                $event->sheet->getDelegate()->getRowDimension(2)->setRowHeight(18);
                $event->sheet->getDelegate()->getRowDimension(3)->setRowHeight(18);
                $event->sheet->getDelegate()->getRowDimension(4)->setRowHeight(18);
                $event->sheet->getDelegate()->getRowDimension(5)->setRowHeight(13);
                $event->sheet->getDelegate()->getRowDimension(6)->setRowHeight(28);
                $event->sheet->getDelegate()->getRowDimension(7)->setRowHeight(5);
                $event->sheet->getDelegate()->getRowDimension(8)->setRowHeight(28);
                $event->sheet->getDelegate()->getRowDimension(9)->setRowHeight(35);
            },
        ];
    }

    public function view(): View
    {
        try
        {
            $equipos = $this->datos;
            return view("excel.calidad.equiposcalibracion", compact("equipos"));
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }

    private function obtenerDatos()
    {
        $tipos = $this->tipos_descargar;
        $equipos = [];
        $omitidos = 0;
        foreach ($tipos as $t)
        {
            $registros = $this->Obtener($t);
            if ($registros->isEmpty())
            {
                $omitidos++;
                continue;
            }
            $equipos[$t] = $registros;
        }
        $this->datos = $equipos;
        $this->omitidos = $omitidos;
    }

    private function Obtener($tipo)
    {
        $equipos = DB::table("calidad_equipos_calibracion as cec")
            ->join("empleados as e", "e.id", "cec.empleado_revisa_id")
            ->where("cec.tipo", $tipo)
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
                DB::raw("'-' as fecha_servicio"),
                DB::raw("'-' as proxima_fecha"),
                DB::raw("'' as condicion")
            )
            ->get();
        $hoy = date("Y-m-d");
        foreach ($equipos as $e)
        {
            $calibracion = DB::table("calidad_calibraciones as cc")
                ->where("cc.equipo_id", $e->id)
                ->select(
                    "cc.proxima_fecha",
                    "cc.fecha_servicio"
                )
                ->orderBy("id", "desc")
                ->first();
            if ($calibracion == null)
            {
                $e->fecha_servicio = "-";
                $e->proxima_fecha = "-";
                $e->condicion = 0;
            }
            else
            {
                $e->fecha_servicio = $calibracion->fecha_servicio;
                $e->proxima_fecha = $calibracion->proxima_fecha;
                $e->condicion = $calibracion->proxima_fecha <= $hoy ? 0 : 1;
            }
        }
        return $equipos;
    }
}
