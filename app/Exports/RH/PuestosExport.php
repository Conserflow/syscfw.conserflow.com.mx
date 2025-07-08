<?php

namespace App\Exports\RH;

use App\Http\Controllers\Otros\Styles;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PuestosExport implements FromView, WithEvents, ShouldAutoSize
{
    public function __construct()
    {
        ob_end_clean();
        ob_start();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event)
            {
                $event->sheet->getStyle('A1:H1')->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle('A1:H1')->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle('A1:H1')->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle('A1:H1')->applyFromArray(Styles::$FONT_WHITE);

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(25);
            },
        ];
    }

    public function view(): View
    {
        $puestos = DB::table("puestos as p")
            ->join("departamentos as d", "d.id", "p.departamento_id")
            ->join("direcciones_administrativas as da", "da.id", "d.direccion_administrativa_id")
            ->select(
                "p.nombre as puesto",
                "p.area",
                "d.nombre as departamento",
                "da.nombre as direccion"
            )
            ->orderBy("p.nombre")
            ->where("p.condicion", 1)
            ->get();

        return view("excel.RH.puestos", compact("puestos"));
    }
}
