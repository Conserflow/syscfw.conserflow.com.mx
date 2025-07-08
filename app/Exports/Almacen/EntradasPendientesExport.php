<?php

namespace App\Exports\Almacen;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Http\Helpers\Utilidades;
use Exception;

class EntradasPendientesExport implements FromView, WithEvents
{
    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event)
            {
                $event->sheet->getDelegate()->getColumnDimension("A")->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension("B")->setWidth(50);
                $event->sheet->getDelegate()->getColumnDimension("C")->setWidth(50);
                $event->sheet->getDelegate()->getColumnDimension("D")->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension("E")->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension("F")->setWidth(15);
            },
        ];
    }

    public function view(): View
    {
        try
        {
            $entradas = DB::table("entradas_pendientes as ep")
                ->select(
                    "p.nombre_corto as proyecto",
                    "a.nombre",
                    "a.descripcion",
                    "ep.cantidad",
                    "ep.folio",
                    "pr.razon_social as proveedor",
                    DB::raw("date(ep.created_at) as fecha")
                )
                ->join("articulos as a", "a.id", "ep.articulo_id")
                ->join("lote_almacen as la", "la.id", "ep.lote_almacen")
                ->join("stocks as s", "s.id", "la.stocke_id")
                ->join("proyectos as p", "p.id", "s.proyecto_id")
                ->join("proveedores as pr", "pr.id", "ep.proveedor_id")
                ->where("ep.cantidad", ">", 0)
                ->orderBy("p.nombre_corto")
                ->orderBy("a.nombre")
                ->get();

            return view("excel.almacen.entradaspendientes", compact("entradas"));
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
