<?php

namespace App\Exports\Compras;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class EvaluacionProveedoresExport implements FromView, WithEvents, ShouldAutoSize
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
            },
        ];
    }

    public function view(): View
    {
        $anio = $this->anio;
        // Obtener proveedores activos, que se les ha comprado
        $proveedores_aux = DB::table("ordenes_compras as oc")
        ->join("proveedores as p", "p.id", "oc.proveedore_id")
        ->whereYear("oc.fecha_orden", "=", $anio)
        ->where("p.condicion", 1)
        ->select(
            "p.id",
            "p.razon_social",
            "p.nombre"
        )
        ->orderBy("p.nombre")
        ->distinct()
        ->get();

        $proveedores = [];
        // Obtener la evaluación del proveedor en el año ingresado
        foreach ($proveedores_aux as $p)
        {
            $evaluacion = DB::table("evaluacion_provee as ep")
                ->where("ep.proveedor_id", $p->id)
                ->whereRaw("year(fecha)=?", [$anio])
                ->select(
                    DB::raw("(
            ep.uno+ep.dos+ep.tres+ep.cuatro+ep.cinco+ep.seis+ep.siete+
            ep.ocho+ep.nueve+ep.diez+ep.once+ep.doce+ep.trece+ep.catorce+ep.quince+
            ep.diesiseis+ep.diesisiete+ep.diesiocho) as total_evaluacion")
                )
                ->first();
            if ($evaluacion == null) // Sin evaluacicón
                $evaluacion = ["total_evaluacion" => 0];
            // Unir proveedor y evaluacion
            $aux = array_merge((array)$p, (array)$evaluacion);

            $proveedores[] = $aux;
        }
        return view("excel.compras.reporteevaluacion", compact("proveedores", "anio"));
    }
}
