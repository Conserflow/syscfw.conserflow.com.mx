<?php

namespace App\Exports\RH;

use App\Http\Controllers\Otros\Styles;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DatosBacariosExport implements FromView, WithEvents, ShouldAutoSize
{

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

        $empleados = DB::table("empleados as e")
            ->join("datos_bancarios_empleados as dbe", "e.id", "dbe.empleado_id")
            ->join("catalogo_bancos as cb", "cb.id", "dbe.banco_id")
            ->select(
                "e.nombre",
                "e.ap_paterno",
                "e.ap_materno",
                "cb.nombre as banco",
                "dbe.numero_cuenta as cuenta",
                "dbe.clabe",
                "dbe.numero_tarjeta as tarjeta"
            )
            ->where("e.condicion", 1)
            ->where("dbe.condicion", 1)
            ->orderBy("nombre")
            ->orderBy("ap_paterno")
            ->get();

        return view("excel.RH.datosbancarios", compact("empleados"));
    }
}
