<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ExistenciaGeneralExport implements FromView, WithEvents, ShouldAutoSize
{
    public function registerEvents(): array
    {

        $styleArray = [
            [
                "font" => [
                    "bold"       =>  true, #0
                    "color" => ["argb" => "0489B1"],
                ],
            ],
            [
                "font" => [
                    "bold"       =>  true,
                    "color" => ["argb" => "FFFFFF"],
                ],
            ],
            [
                "font" => [
                    "bold"       =>  true,  #2
                    "color" => ["argb" => "000000"],
                ],
            ],
            [
                "font" => [
                    "bold"       =>  true,
                    "color" => ["argb" => "08298A"],
                ],
            ],
            [
                "alignment" =>  #4
                [
                    "horizontal" => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            [
                "borders" => [
                    "allBorders" => [
                        "borderStyle" => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        "color" => ["argb" => "000000"],
                    ],
                ],
            ],
            [
                "alignment" =>  #4
                [
                    "vertical" => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]

        ];

        return [
            //todos los estilos los cargas aqui
            AfterSheet::class => function (AfterSheet $event) use ($styleArray)
            {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName("Logo");
                $drawing->setDescription("Logo");
                $drawing->setPath(public_path("img/conserflow.png"));
                $drawing->setCoordinates("A2");
                $drawing->setHeight(60);
                $drawing->setWorksheet($event->sheet->getDelegate());


                $event->sheet->getDelegate()->getRowDimension(4)->setRowHeight(30);

                $event->sheet->getDelegate()->getColumnDimension("A")->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension("B")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("C")->setWidth(60);
                $event->sheet->getDelegate()->getColumnDimension("D")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("E")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("F")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("G")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("H")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("I")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("J")->setWidth(15);

                $event->sheet->getStyle("A1:J1")->applyFromArray($styleArray[4]);
                $event->sheet->getStyle("A1:j4")->applyFromArray($styleArray[6]);
                $event->sheet->getStyle("A1:j4")->applyFromArray($styleArray[5]); //Border
                $event->sheet->getStyle("E6:j6")->applyFromArray($styleArray[5]); //Border
                $event->sheet->getStyle("A7:j7")->applyFromArray($styleArray[5]); //Border
                $event->sheet->getStyle("A7:j7")->applyFromArray($styleArray[1]); //se lllama el array de la posicion 0
                // $event->sheet->getStyle("A7:H7")->applyFromArray($styleArray[4]);
                $event->sheet->getStyle("A1")->applyFromArray($styleArray[0]); //se lllama el array de la posicion 0
            },
        ];
    }

    public function view(): View
    {
        $reporte = DB::table("proyectos as p")
            ->join("ordenes_compras as oc", "oc.proyecto_id", "p.id")
            ->join("requisicion_has_ordencompras as rho", "rho.orden_compra_id", "oc.id")
            ->join("partidas_entradas as pe", "pe.req_com_id", "rho.id")
            ->join("entradas as e", "e.id", "pe.entrada_id")
            ->join("articulos as a", "a.id", "rho.articulo_id")
            ->leftjoin("grupos as g", "g.id", "a.grupo_id")
            ->leftjoin("lotes as l", "l.entrada_id", "pe.id")
            ->leftjoin("lote_almacen as la", "la.lote_id", "l.id")
            ->select(
                "g.nombre as nombre_grupo",
                "a.nombre as descripcion",
                "a.unidad",
                "rho.precio_unitario",
                "e.fecha",
                "p.nombre_corto",
                "pe.cantidad",
                "e.orden_compra_id",
                "oc.proyecto_id",
                "la.cantidad as la_cantidad",
                "l.cantidad as l_cantidad",
                "la.id as lote_almacen_id",
                "oc.folio"
            )
            ->orderBy("p.nombre_corto")
            ->get();

        $array = [];
        foreach ($reporte as $key => $value)
        {
            $salidas = DB::table("partidas as p")
                ->where("p.lote_id", $value->lote_almacen_id)
                ->select(DB::raw("SUM(cantidad) as cantidad"))->first();

            $retornos = DB::table("partidas as p")
                ->where("p.lote_id", $value->lote_almacen_id)
                ->select(DB::raw("SUM(cantidad_retorno) as cantidad_retorno"))->first();


            $array[] = [
                "a" => $value,
                "b" => $salidas->cantidad,
                "c" => $retornos->cantidad_retorno,
            ];
        }

        return view("excel.existencias_general", compact("array"));
    }
}
