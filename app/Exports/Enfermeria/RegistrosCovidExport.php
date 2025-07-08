<?php

namespace App\Exports\Enfermeria;

use App\Http\Controllers\Otros\Styles;
use App\Http\Helpers\Utilidades;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class RegistrosCovidExport  implements FromView, WithEvents
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
                $drawing->setCoordinates('A2');
                $drawing->setHeight(60);
                $drawing->setWorksheet($event->sheet->getDelegate());
                // $event->sheet->getStyle('A1:F4')->applyFromArray($styleArray[5]);
                $event->sheet->getStyle('A1:I1')->applyFromArray(Styles::$FONT_BLUE);
                $event->sheet->getStyle('A1:I1')->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle('C3:I3')->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle('A6:J6')->applyFromArray(Styles::$FONT_WHITE);
                // $event->sheet->getStyle('A1:A300')->getAlignment()->setWrapText(true);
                // $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(15);
            },
        ];
    }

    public function view(): View
    {
        try
        {
            // Obtener todos los registros
            $registros = DB::table("enfermeria_registros_covid as erc")
                ->join("empleados as e", "e.id", "erc.empleado_id")
                ->join("puestos as p", "p.id", "erc.puesto_id")
                ->select(
                    "erc.*",
                    DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre"),
                    "p.nombre as puesto"
                )
                ->orderBy("nombre")
                ->get();

            return view('excel.enfermeria.registroscovid', compact("registros"));
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view('errors.500');
        }
    }
}
