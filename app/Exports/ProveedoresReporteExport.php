<?php

namespace App\Exports;

use App\Http\Controllers\Otros\Styles;
use Exception;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use \App\Http\Helpers\Utilidades;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ProveedoresReporteExport implements FromView, WithEvents
{
    private $anio = 0;

    public function __construct($anio)
    {
        $this->anio = $anio;
    }
    /**
     * @return array
     */
    public function registerEvents(): array
    {

        $anio = $this->anio;
        $total = DB::table("ordenes_compras as oc")
            ->join("proveedores as p", "p.id", "oc.proveedore_id")
            ->whereYear("oc.fecha_orden", $anio)
            ->where("p.condicion", 1)
            ->select(
                "p.id",
                "p.fecha_alta",
                "p.razon_social",
                "p.nombre",
                "p.rfc",
                "p.giro",
                "p.direccion",
                "p.contacto",
                "p.telefono",
                "p.correo",
                "p.regimen_fiscal as tipo",
                "p.pagina",
                "p.limite_credito"
            )
            ->orderBy("p.nombre")
            ->distinct()
            ->get()->count();
        $total += 6;

        return [
            AfterSheet::class => function (AfterSheet $event) use ($total)
            {
                $drawing = new Drawing();
                $drawing->setName("Logo");
                $drawing->setDescription("Logo");
                $drawing->setPath(public_path("img/conserflow.png"));
                $drawing->setCoordinates("B2");
                $drawing->setHeight(60);
                $drawing->setWorksheet($event->sheet->getDelegate());

                $event->sheet->getDelegate()->getColumnDimension("A")->setWidth(5);
                $event->sheet->getDelegate()->getColumnDimension("B")->setWidth(45);
                $event->sheet->getDelegate()->getColumnDimension("C")->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension("D")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("E")->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension("F")->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension("G")->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension("H")->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension("I")->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension("J")->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension("K")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("L")->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension("M")->setWidth(20);
                $event->sheet->getRowDimension(6)->setRowHeight(30);

                $event->sheet->getStyle("A1:O1")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("A6:M6")->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("A6:M6")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("B2:K4")->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("B2:K4")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("L2:L4")->applyFromArray(Styles::$HORIZONTAL_CENTER);

                $event->sheet->getStyle("A1:L$total")->applyFromArray(Styles::$FONT_ARIAL);
                $event->sheet->getStyle("A6:L$total")->applyFromArray(Styles::$BORDER_ALL_THIN);
                $event->sheet->getStyle("A6:L$total")->applyFromArray(Styles::$BORDER_EXTERNAL_BOLD);
                $event->sheet->getStyle("A2:L4")->applyFromArray(Styles::$BORDER_ALL_THIN);
                $event->sheet->getStyle("A2:L4")->applyFromArray(Styles::$BORDER_EXTERNAL_BOLD);
                $event->sheet->getStyle("A2:B4")->applyFromArray(Styles::$BORDER_EXTERNAL_BOLD);
            },
        ];
    }

    public function view(): View
    {
        try
        {
            $anio = $this->anio;
            // Obtener proveedores activos, que se les ha comprado
            $proveedores_aux = DB::table("ordenes_compras as oc")
                ->join("proveedores as p", "p.id", "oc.proveedore_id")
                ->whereYear("oc.fecha_orden", $anio)
                ->where("p.condicion", 1)
                ->select(
                    "p.id",
                    "p.fecha_alta",
                    "p.razon_social",
                    "p.nombre",
                    "p.rfc",
                    "p.giro",
                    "p.direccion",
                    "p.contacto",
                    "p.telefono",
                    "p.correo",
                    "p.regimen_fiscal as tipo",
                    "p.pagina",
                    "p.limite_credito"
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
                        "ep.id as ep_id",
                        "ep.fecha as fecha_evaluacion",
                        DB::raw("(
                            ep.uno+ep.dos+ep.tres+ep.cuatro+ep.cinco+ep.seis+ep.siete+
                            ep.ocho+ep.nueve+ep.diez+ep.once+ep.doce+ep.trece+ep.catorce+ep.quince+
                            ep.diesiseis+ep.diesisiete+ep.diesiocho
                            ) as total_evaluacion")
                    )
                    ->first();
                if ($evaluacion == null)
                {
                    $evaluacion = ["fecha_evaluacion" => "N/D", "total_evaluacion" => "0"];
                }
                $proveedores[] = array_merge((array)$p, (array)$evaluacion);
            }
            return view("excel.compras.proveedores", compact("proveedores"));
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
