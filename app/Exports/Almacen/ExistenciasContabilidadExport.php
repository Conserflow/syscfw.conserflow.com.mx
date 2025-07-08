<?php

namespace App\Exports\Almacen;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


class ExistenciasContabilidadExport implements FromView, WithEvents
{
    protected $anio;
    protected $mes;

    public function __construct($anio, $mes)
    {
        $this->anio = $anio;
        $this->mes = $mes;
    }

    public function registerEvents(): array
    {
        return [
            //todos los estilos los cargas aqui
            AfterSheet::class => function (AfterSheet $event)
            {
                $event->sheet->getDelegate()->getColumnDimension("A")->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension("B")->setWidth(60);
                $event->sheet->getDelegate()->getColumnDimension("C")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("D")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("E")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("F")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("G")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("H")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("I")->setWidth(15);
            },
        ];
    }


    public function view(): View
    {
        // Fecha inicial
        $mes_letra = $this->mes($this->mes);
        $anio = $this->anio;
        DB::enableQueryLog();
        $entradas = DB::table("partidas_entradas as pe")
            ->join("requisicion_has_ordencompras as rhoc", "rhoc.id", "pe.req_com_id")
            ->join("entradas as e", "e.id", "pe.entrada_id")
            ->join("ordenes_compras as oc", "oc.id", "e.orden_compra_id")
            ->join("proyectos as p", "p.id", "oc.proyecto_id")
            ->join("articulos as a", "a.id", "pe.articulo_id")
            ->leftjoin("grupos as g", "g.id", "a.grupo_id")
            ->join("lotes as l", "l.entrada_id", "pe.id")
            ->join("lote_almacen as la", "la.lote_id", "l.id")
            ->select(
                "a.nombre as a_descripcion",
                "a.unidad as a_um",
                "e.fecha as e_fecha",
                // "e.orden_compra_id",
                "g.nombre as g_nombre",
                "la.codigo_barras as la_codigo_barras",
                "la.id as lote_almacen_id",
                "oc.proyecto_id",
                "p.nombre_corto as p_nombre",
                "pe.cantidad as pe_cantidad",
                "rhoc.precio_unitario as pu"
            )
            ->whereRaw("month(e.fecha) =?", $this->mes)
            ->whereRaw("year(e.fecha) =?", $this->anio)
            ->orderBy("e_fecha")
            ->get();
        return view("excel.almacen.existenciacontabilidad", compact("entradas", "mes_letra", "anio"));
    }

    public function mes($n)
    {
        $meses = [
            "",
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        ];
        return $meses[$n];
    }
}
