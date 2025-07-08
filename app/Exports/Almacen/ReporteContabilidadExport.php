<?php

namespace App\Exports\Almacen;

use App\Http\Controllers\Otros\Styles;
use App\Partidas;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ReporteContabilidadExport implements FromView, WithEvents
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
            AfterSheet::class => function (AfterSheet $event)
            {
                $event->sheet->getDelegate()->getRowDimension(4)->setRowHeight(30);

                $event->sheet->getDelegate()->getColumnDimension("A")->setWidth(5);
                $event->sheet->getDelegate()->getColumnDimension("B")->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension("C")->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension("D")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("E")->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension("F")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("G")->setWidth(45);
                $event->sheet->getDelegate()->getColumnDimension("H")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("I")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("I")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("J")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("k")->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension("l")->setWidth(20);

                $event->sheet->getStyle("A2:l2")->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle("A1:l1")->applyFromArray(Styles::$FONT_BLUE);
            },
        ];
    }


    public function view(): View
    {
        $array = [];

        DB::enableQueryLog();
        // Salidas taller
        $taller = DB::table("partidas as p")
            ->join("salidas AS s", "s.id", "p.salida_id")
            ->join("empleados as er", "er.id", "s.empleado_id")
            ->join("lote_almacen AS la", "la.id", "p.lote_id")
            ->join("lotes AS l", "l.id", "la.lote_id")
            ->join("partidas_entradas AS pe", "pe.id", "l.entrada_id")
            ->join("requisicion_has_ordencompras AS rhoc", "rhoc.id", "pe.req_com_id")
            ->join("ordenes_compras AS oc", "oc.id", "rhoc.orden_compra_id")
            ->join("articulos AS a", "a.id", "la.articulo_id")
            ->join("stocks AS ss", "ss.id", "la.stocke_id")
            ->Join("proyectos AS pr", "pr.id",  "ss.proyecto_id")
            ->Join("proyectos AS p2", "p2.id",  "s.proyecto_id")
            ->whereRaw("year(s.fecha) = ?", $this->anio) // Año
            ->whereRaw("month(s.fecha) = ?", $this->mes) // Mes
            ->where("p.tiposalida_id", 1)
            ->select(
                "a.descripcion AS desc",
                "a.nombre AS nombre",
                "a.unidad as unidad",
                "la.codigo_barras",
                "oc.folio AS oc_folio",
                "p.cantidad AS cantidad_salida",
                "p2.nombre_corto as p_salida",
                "pr.nombre_corto",
                "rhoc.precio_unitario",
                "s.fecha as s_fecha",
                "s.folio as folio",
                "s.id as ids",
                DB::raw("concat_ws(' ',er.nombre,er.ap_paterno,er.ap_materno) as empleado_solicita"),
                DB::raw("('Taller') AS tipo")
            )
            ->orderBy("s_fecha")
            ->get()
            ->toArray();

        // Salidas sitio
        $sitio = DB::table("partidas as p")
            ->join("salidassitio AS s", "s.id", "p.salida_id")
            ->join("empleados as er", "er.id", "s.empleado_solicita_id")
            ->join("lote_almacen AS la", "la.id", "p.lote_id")
            ->join("lotes AS l", "l.id", "la.lote_id")
            ->join("partidas_entradas AS pe", "pe.id", "l.entrada_id")
            ->join("requisicion_has_ordencompras AS rhoc", "rhoc.id", "pe.req_com_id")
            ->join("ordenes_compras AS oc", "oc.id", "rhoc.orden_compra_id")
            ->join("articulos AS a", "a.id",  "la.articulo_id")
            ->join("stocks AS ss", "ss.id", "la.stocke_id")
            ->Join("proyectos AS pr", "pr.id",  "ss.proyecto_id")
            ->join("proyectos AS p2", "p2.id", "s.proyecto_id")
            ->whereRaw("year(s.fecha) = ?", $this->anio) // Año
            ->whereRaw("month(s.fecha) = ?", $this->mes) // Mes
            ->where("p.tiposalida_id", "2")
            ->select(
                "a.descripcion AS desc",
                "a.nombre AS nombre",
                "a.unidad as unidad",
                "la.codigo_barras",
                "oc.folio AS oc_folio",
                "p.cantidad AS cantidad_salida",
                "p2.nombre_corto AS p_salida",
                "pr.nombre_corto",
                "rhoc.precio_unitario",
                "s.fecha as s_fecha",
                "s.folio as ",
                "s.folio as folio",
                "s.id as ids",
                DB::raw("concat_ws(' ',er.nombre,er.ap_paterno,er.ap_materno) as empleado_solicita"),
                DB::raw("('Sitio') AS tipo")
            )
            ->orderBy("s_fecha")
            ->get()
            ->toArray();

        $resguardo = DB::table("partidas as p")
            ->join("salidasresguardo AS s", "s.id", "p.salida_id")
            ->join("empleados as er", "er.id", "s.empleado_solicita_id")
            ->join("lote_almacen AS la", "la.id", "p.lote_id")
            ->join("lotes AS l", "l.id", "la.lote_id")
            ->join("partidas_entradas AS pe", "pe.id", "l.entrada_id")
            ->join("requisicion_has_ordencompras AS rhoc", "rhoc.id", "pe.req_com_id")
            ->join("ordenes_compras AS oc", "oc.id", "rhoc.orden_compra_id")
            ->join("articulos AS a", "a.id",  "la.articulo_id")
            ->join("stocks AS ss", "ss.id", "la.stocke_id")
            ->Join("proyectos AS pr", "p.id",  "ss.proyecto_id")
            ->whereRaw("year(s.fecha) = ?", $this->anio) // Año
            ->whereRaw("month(s.fecha) = ?", $this->mes) // Mes
            ->where("p.tiposalida_id", "3")
            ->select(
                "a.descripcion AS desc",
                "a.nombre AS nombre",
                "a.unidad as unidad",
                "la.codigo_barras",
                "oc.folio AS oc_folio",
                "p.cantidad AS cantidad_salida",
                "pr.nombre_corto",
                "rhoc.precio_unitario",
                "s.fecha as s_fecha",
                "s.folio as folio",
                "s.id as ids",
                DB::raw("concat_ws(' ',er.nombre,er.ap_paterno,er.ap_materno) as empleado_solicita"),
                DB::raw("('Resguardo') AS tipo")
            )
            ->orderBy("s_fecha")
            ->get()
            ->toArray();

        $salidas = array_merge($taller, $sitio, $resguardo);

        return view("excel.almacen.salidascontabilidad", compact("salidas"));
    }
}
