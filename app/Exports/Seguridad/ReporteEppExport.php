<?php

namespace App\Exports\Seguridad;

use App\Http\Controllers\Otros\Styles;
use App\Http\Helpers\Utilidades;
use Exception;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ReporteEppExport  implements FromView, WithEvents
{

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event)
            {
                // Estilo
                $event->sheet->getStyle('A1:F1')->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle('A1:F1')->applyFromArray(Styles::$BG_BLUE);
                $event->sheet->getStyle('A1:F1')->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle('A1:F1')->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle('A1:F1')->applyFromArray(Styles::$FONT_BOLD);
                // Width
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(25);
                $event->sheet->getDelegate()->getColumnDimension("A")->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension("B")->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension("C")->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension("D")->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension("E")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("F")->setWidth(15);
            },
        ];
    }

    public function view(): View
    {
        try
        {
            $equipos = DB::table("partidas_vale_epp as pve")
                ->join("empleados_vale_epp as eve", "eve.id", "pve.empleado_vale_id")
                ->join("articulos as a", "a.id", "pve.articulo_id")
                ->join("proyectos as p", "p.id", "pve.proyecto_id")
                ->join("empleados as e", "e.id", "eve.empleado_id")
                ->select(
                    "p.nombre_corto as proyecto",
                    DB::raw("if(a.nombre is null,a.descripcion,a.nombre) as equipo"),
                    "e.id as e_id",
                    DB::raw("concat_ws (' ', e.nombre, e.ap_paterno, e.ap_materno) as empleado"),
                    "pve.fecha as fecha_entrega",
                    "pve.motivo",
                    "pve.observaciones"
                )
                ->orderBy("fecha_entrega")
                ->get();

            return view("excel.seguridad.reporteepp", compact("equipos"));
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view('errors.500');
        }
    }
}
