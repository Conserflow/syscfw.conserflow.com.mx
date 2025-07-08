<?php

namespace App\Exports;

use App\Existencia;
use App\Http\Controllers\Otros\Styles;
use App\Proyecto;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\AfterImport;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class SalidaExport implements FromView, WithEvents
{

  protected $id;

  public function __construct(int $id)
  {
    $this->id = $id;
  }

  public function registerEvents(): array
  {
    return [
      //todos los estilos los cargas aqui
      AfterSheet::class => function (AfterSheet $event)
      {
        $event->sheet->getDelegate()->getRowDimension(3)->setRowHeight(30);

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
        $event->sheet->getDelegate()->getColumnDimension("K")->setWidth(15);
        $event->sheet->getDelegate()->getColumnDimension("L")->setWidth(20);
        $event->sheet->getDelegate()->getColumnDimension("M")->setWidth(20);

        $event->sheet->getStyle("A3:M3")->applyFromArray(Styles::$FONT_WHITE);
        $event->sheet->getStyle("A1:M1")->applyFromArray(Styles::$FONT_BLUE);
      },
    ];
  }


  public function view(): View
  {
    $array = [];
    // Salidas taller
    DB::enableQueryLog();
    $st = \App\Partidas::join("salidas AS s", "s.id", "partidas.salida_id")
      ->join("lote_almacen AS la", "la.id", "partidas.lote_id")
      ->join("lotes AS l", "l.id", "la.lote_id")
      ->join("partidas_entradas AS pe", "pe.id", "l.entrada_id")
      ->join("requisicion_has_ordencompras AS rhoc", "rhoc.id", "pe.req_com_id")
      ->join("ordenes_compras AS oc", "oc.id", "rhoc.orden_compra_id")
      ->join("articulos AS a", "a.id", "=", "la.articulo_id")
      ->join("stocks AS ss", "ss.id", "la.stocke_id")
      ->Join("proyectos AS p", "p.id", "=", "ss.proyecto_id")
      ->Join("proyectos AS p2", "p2.id", "=", "s.proyecto_id")
      ->leftJoin("proyecto_subcategorias AS ps", "ps.id", "=", "p.proyecto_subcategorias_id")
      ->leftJoin("proyecto_categorias AS pc", "pc.id", "=", "ps.proyecto_categoria_id")
      ->where("s.proyecto_id", $this->id)
      ->where("partidas.tiposalida_id", "1")
      ->select(
        "rhoc.precio_unitario",
        "partidas.cantidad AS cantidad_salida",
        "s.id as ids",
        DB::raw("('Taller') AS tipo"),
        "s.folio as folio",
        "s.fecha as s_fecha",
        "p.nombre_corto",
        "p2.nombre_corto as p_salida",
        "a.nombre AS nombre",
        "a.descripcion AS desc",
        "a.unidad as unidad",
        "oc.folio AS oc_folio",
        "oc.moneda",
        "partidas.cantidad_retorno",
        DB::raw("(partidas.cantidad-partidas.cantidad_retorno) as pendiente")
      )
      ->get()
      ->toArray();

    // Salidas sitio
    $ss = \App\Partidas::join("salidassitio AS s", "s.id", "partidas.salida_id")
      ->join("lote_almacen AS la", "la.id", "partidas.lote_id")
      ->join("lotes AS l", "l.id", "la.lote_id")
      ->join("partidas_entradas AS pe", "pe.id", "l.entrada_id")
      ->join("requisicion_has_ordencompras AS rhoc", "rhoc.id", "pe.req_com_id")
      ->join("ordenes_compras AS oc", "oc.id", "rhoc.orden_compra_id")
      ->join("articulos AS a", "a.id", "=", "la.articulo_id")
      ->join("stocks AS ss", "ss.id", "la.stocke_id")
      ->Join("proyectos AS p", "p.id", "=", "ss.proyecto_id")
      ->join("proyectos AS p2", "p2.id", "s.proyecto_id")
      ->leftJoin("proyecto_subcategorias AS ps", "ps.id", "=", "p.proyecto_subcategorias_id")
      ->leftJoin("proyecto_categorias AS pc", "pc.id", "=", "ps.proyecto_categoria_id")
      ->where("s.proyecto_id", $this->id)
      ->where("partidas.tiposalida_id", "2")
      ->select(
        "rhoc.precio_unitario",
        "partidas.cantidad AS cantidad_salida",
        "s.id as ids",
        DB::raw("('Sitio') AS tipo"),
        "s.folio as folio",
        "s.fecha as s_fecha",
        "s.folio as ",
        "p.nombre_corto",
        "p2.nombre_corto AS p_salida",
        "a.nombre AS nombre",
        "a.descripcion AS desc",
        "a.unidad as unidad",
        "oc.folio AS oc_folio",
        "oc.moneda",
        "partidas.cantidad_retorno",
        DB::raw("(partidas.cantidad-partidas.cantidad_retorno) as pendiente")
      )
      ->get()
      ->toArray();

    $array_uno = array_merge($st, $ss);

    $sr = \App\Partidas::join("salidasresguardo AS s", "s.id", "partidas.salida_id")
      ->join("lote_almacen AS la", "la.id", "partidas.lote_id")
      ->join("lotes AS l", "l.id", "la.lote_id")
      ->join("partidas_entradas AS pe", "pe.id", "l.entrada_id")
      ->join("requisicion_has_ordencompras AS rhoc", "rhoc.id", "pe.req_com_id")
      ->join("ordenes_compras AS oc", "oc.id", "rhoc.orden_compra_id")
      ->join("articulos AS a", "a.id", "=", "la.articulo_id")
      ->join("stocks AS ss", "ss.id", "la.stocke_id")
      ->Join("proyectos AS p", "p.id", "=", "ss.proyecto_id")
      ->leftJoin("proyecto_subcategorias AS ps", "ps.id", "=", "p.proyecto_subcategorias_id")
      ->leftJoin("proyecto_categorias AS pc", "pc.id", "=", "ps.proyecto_categoria_id")
      ->where("s.proyecto_id", $this->id)
      ->where("partidas.tiposalida_id", "3")
      ->select(
        "rhoc.precio_unitario",
        "partidas.cantidad AS cantidad_salida",
        "s.id as ids",
        DB::raw("('Resguardo') AS tipo"),
        "s.folio as folio",
        "s.fecha as s_fecha",
        "p.nombre_corto",
        "a.nombre AS nombre",
        "a.descripcion AS desc",
        "a.unidad as unidad",
        "oc.folio AS oc_folio",
        "oc.moneda",
        "partidas.cantidad_retorno",
        DB::raw("(partidas.cantidad-partidas.cantidad_retorno) as pendiente")
      )
      ->get()
      ->toArray();
    $array = array_merge($array_uno, $sr);
    return view("excel.salidaexport", compact("array"));
  }
}
