<?php

namespace App\Exports\Compras;

use App\Http\Controllers\Otros\Styles;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ProveedoresPorAnioExport implements FromView, WithEvents, ShouldAutoSize
{
    protected $anio;

    public function __construct($anio)
    {
        $this->anio = $anio;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event)
            {
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(50);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(20);
                $event->sheet->getStyle('A3:B3')->applyFromArray(Styles::$HORIZONTAL_CENTER);
            },
        ];
    }

    public function view(): View
    {
        $anio = $this->anio;
        // Obtener proveedores activos, que se les ha comprado
        $proveedores = DB::table("ordenes_compras as oc")
            ->join("proveedores as p", "p.id", "oc.proveedore_id")
            ->whereYear("oc.fecha_orden", "=", $anio)
            ->where("p.condicion", 1)
            ->select(
                "p.id",
                "p.razon_social",
                DB::raw("'1' as total_ocs")
            )
            ->orderBy("p.razon_social")
            ->distinct()
            ->get();
        foreach ($proveedores as $p)
        {
            // Obtener el total de OC
            $n = DB::table("ordenes_compras as oc")
                ->where("oc.proveedore_id", $p->id)
                ->whereYear("oc.fecha_orden", "=", $anio)
                ->count();
            $p->total_ocs = $n;
        }
        return view("excel.compras.proveedoresporanio", compact("proveedores", "anio"));
    }
}
