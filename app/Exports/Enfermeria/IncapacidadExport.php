<?php

namespace App\Exports\Enfermeria;

use App\Http\Controllers\Otros\Styles;
use App\Http\Helpers\Utilidades;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class IncapacidadExport implements FromView, ShouldAutoSize, WithEvents
{
    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event)
            {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');
                $drawing->setPath(public_path('img/conserflow.png'));
                $drawing->setCoordinates('B2');
                $drawing->setHeight(60);
                $drawing->setWorksheet($event->sheet->getDelegate());
                // $event->sheet->getStyle('A1:F4')->applyFromArray($styleArray[5]);
                $event->sheet->getStyle('A1:I1')->applyFromArray(Styles::$FONT_BLUE);
                $event->sheet->getStyle('A1:I1')->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle('C3:I3')->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle('A6:J6')->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle('A6:Z8')->applyFromArray(Styles::$BG_BLUE);
                $event->sheet->getStyle('A6:Z8')->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle('A6:Z8')->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle('A6:Z8')->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle('A6:Z8')->applyFromArray(Styles::$BORDER_ALL_THIN);
                $event->sheet->getStyle('A6:Z8')->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle('A6:Z8')->applyFromArray(Styles::FontSize(10));
                // $event->sheet->getStyle('A1:A300')->getAlignment()->setWrapText(true);
                // $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(5);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(35);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(7);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(7);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(7);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(7);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(7);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(7);
                $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(7);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(7);
                $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(7);
                $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(7);
                $event->sheet->getDelegate()->getColumnDimension('P')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('Q')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('R')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('S')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('T')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('U')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('V')->setWidth(18);
                $event->sheet->getDelegate()->getColumnDimension('W')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('X')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('Y')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('Z')->setWidth(20);

                $event->sheet->getDelegate()->getRowDimension(6)->setRowHeight(20);
                $event->sheet->getDelegate()->getRowDimension(7)->setRowHeight(18);
                $event->sheet->getDelegate()->getRowDimension(8)->setRowHeight(15);
            },
        ];
    }

    public function view(): View
    {
        try
        {
            // Obtener todos los registros
            $incapacidad = DB::table("enfermeria_incapacidades as ei")
                ->join("empleados as e", "e.id", "ei.empleado_id")
                ->join("puestos as p", "p.id", "ei.puesto_id")
                ->join("departamentos as d","d.id","p.departamento_id")
                ->select(
                    "ei.*",
                    DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as empleado"),
                    "p.nombre as puesto",
                    "d.nombre as departamento"
                )
                ->orderBy("empleado")
                ->get();
            return view('excel.enfermeria.incapacidad',compact("incapacidad"));
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view('errors.500');
        }
    }
}
