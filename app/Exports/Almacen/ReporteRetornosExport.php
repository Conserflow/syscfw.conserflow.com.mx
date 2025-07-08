<?php

namespace App\Exports\Almacen;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Http\Helpers\Utilidades;
use Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ReporteRetornosExport implements FromView, WithEvents
{
    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event)
            {
                $drawing = new Drawing();
                $drawing->setName("Logo");
                $drawing->setDescription("Logo");
                $drawing->setPath(public_path("img/conserflow.png"));
                $drawing->setCoordinates("A2");
                $drawing->setHeight(60);
                $drawing->setWorksheet($event->sheet->getDelegate());
                $event->sheet->getDelegate()->getColumnDimension("A")->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension("B")->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension("C")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("D")->setWidth(45);
                $event->sheet->getDelegate()->getColumnDimension("E")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("F")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("G")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("H")->setWidth(25);
            },
        ];
    }

    public function view(): View
    {
        try
        {
            //  Obtener las partidas de todos los retornos
            $salidas = DB::table("partidas_retorno as pr")
                ->select(
                    "p.salida_id",
                    "pro.nombre_corto as proyecto",
                    "tipo_salida as tipo",
                    "sr.fecha",
                    "pr.cantidad_entrada as cantidad_retornado",
                    DB::raw("(p.cantidad-p.cantidad_retorno) as pendiente"),
                    "a.descripcion as articulo",
                    "alm.nombre as almacen",
                    "rho.precio_unitario"
                )
                ->join("salidas_retorno as sr", "sr.id", "pr.salida_retorno_id")
                ->join("partidas as p", "p.id", "pr.partida_id")
                ->join("lote_almacen as la", "la.id", "p.lote_id")
                ->join("articulos as a", "a.id", "pr.articulo_id")
                ->join("almacenes as alm", "alm.id", "la.almacene_id")
                ->join("proyectos as pro", "pro.id", "pr.proyecto_id")
                ->join("lotes as l", "l.id", "la.lote_id")
                ->join("partidas_entradas as pe","pe.id","l.entrada_id")
                ->join("requisicion_has_ordencompras as rho","rho.id","pe.req_com_id")
                ->orderBy("proyecto")
                ->orderBy("tipo")
                ->orderBy("articulo")
                ->get();
            return view("excel.almacen.reporteretornos", compact("salidas"));
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
