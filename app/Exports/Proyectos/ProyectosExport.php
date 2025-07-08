<?php

namespace App\Exports\Proyectos;

use App\Http\Controllers\Otros\Styles;
use App\Proyecto;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ProyectosExport implements ShouldAutoSize, WithTitle, WithHeadings, WithEvents, FromView
{
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
                $drawing->setHeight(60);
                $drawing->setWorksheet($event->sheet->getDelegate());

                $event->sheet->getStyle('A6:K6')->applyFromArray(Styles::$BG_BLUE);
                $event->sheet->getStyle('A6:K6')->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle('A6:K6')->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle('A6:K6')->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle('A6:K6')->applyFromArray(Styles::$FONT_WHITE);

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(15);
            },
        ];
    }

    public function view(): View
    {

        $proyectos = Proyecto::activos()
            ->leftjoin("empleados as e", "e.id", "proyectos.empleado_registra_id")
            ->select(
                "proyectos.*",
                DB::raw("0 as total_oc"),
                DB::raw("0 as total_req"),
                DB::raw("DATE_FORMAT(proyectos.created_at, '%Y-%m-%d') as created_at2"),
                DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as registra")
            )
            ->orderBy("nombre_corto")
            ->get();

        foreach ($proyectos as $p)
        {
            $total_requis = DB::table("requisiciones as r")
                ->where("r.proyecto_id", $p->id)->count();
            $total_oc = DB::table("ordenes_compras as oc")
                ->where("oc.proyecto_id", $p->id)->count();
            $p->total_oc = $total_oc;
            $p->total_req = $total_requis;
        }
        return view("excel.proyectos.reporteproyectos", compact("proyectos"));
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "Proyectos activos";
    }

    /**
     * @return string[]
     */
    public function headings(): array
    {
        return [
            "Nombre",
            "Folio",
            "PO",
            "Monto Total",
            "Ciudad",
            "Total OC",
            "Total REQ",
            "Fecha inicio",
            "Fecha Fin",
            "Elabora",
            "Fecha / Hora",
        ];
    }
}
