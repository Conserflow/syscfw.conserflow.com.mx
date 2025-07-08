<?php

namespace App\Exports\Requisiciones;

use App\Http\Controllers\Otros\Styles;
use App\Http\Helpers\Utilidades;
use App\RequisicionModels\PartidaMaterial;
use App\RequisicionModels\Requisicion;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class MaterialPrincipalExport  implements FromView, WithEvents, WithTitle, ShouldAutoSize
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function registerEvents(): array
    {
        $n_partidas = PartidaMaterial::byRequisicion($this->id)->count();

        return [
            AfterSheet::class => function (AfterSheet $event) use ($n_partidas)
            {
                $drawing = new Drawing();
                $drawing->setName("Logo");
                $drawing->setDescription("Logo");
                $drawing->setPath(public_path("img/conserflow.png"));
                $drawing->setCoordinates("A2");
                $drawing->setHeight(50);
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(10);
                $drawing->setWorksheet($event->sheet->getDelegate());

                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(15);
                $event->sheet->getDelegate()->getRowDimension(2)->setRowHeight(15);
                $event->sheet->getDelegate()->getRowDimension(3)->setRowHeight(15);
                $event->sheet->getDelegate()->getRowDimension(5)->setRowHeight(7);
                $event->sheet->getDelegate()->getRowDimension(6)->setRowHeight(7);
                $event->sheet->getDelegate()->getRowDimension(7)->setRowHeight(10);
                $event->sheet->getDelegate()->getRowDimension(8)->setRowHeight(20);
                $event->sheet->getDelegate()->getRowDimension(9)->setRowHeight(20);
                $event->sheet->getDelegate()->getRowDimension(10)->setRowHeight(20);
                $event->sheet->getDelegate()->getRowDimension(11)->setRowHeight(20);
                $event->sheet->getDelegate()->getRowDimension(14)->setRowHeight(20);

                $event->sheet->getStyle("A1:H" . (27 + $n_partidas))->applyFromArray(Styles::$BG_WHITE);

                // Encabezado
                $event->sheet->getStyle("A1:H1")->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle("A1:H1")->applyFromArray(Styles::$FONT_BLUE);
                $event->sheet->getStyle("A1:H1")->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("A1:H1")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("A1:H1")->applyFromArray(Styles::FontSize(14));
                $event->sheet->getStyle("D2:F4")->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle("A6:H6")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("A6:H6")->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("G2:G4")->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle("G2:H4")->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("G2:H4")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("A6:H6")->applyFromArray(Styles::FontSize(8));
                $event->sheet->getStyle("D2:F4")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("D2:F4")->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("D2:F4")->applyFromArray(Styles::FontSize(22));
                $event->sheet->getStyle("A2:H4")->applyFromArray(Styles::$BORDER_ALL_THIN);
                $event->sheet->getStyle("A2:H4")->applyFromArray(Styles::$BORDER_EXTERNAL_BOLD);

                // Datos
                $event->sheet->getStyle("A8:B11")->applyFromArray(Styles::$BG_BLUE);
                $event->sheet->getStyle("A8:B11")->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle("G8:G11")->applyFromArray(Styles::$BG_BLUE);
                $event->sheet->getStyle("G8:G11")->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle("G8:G11")->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("G8:G11")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("A8:E11")->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("A8:E11")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("A8:B11")->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle("G8:G11")->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle("A6:H11")->applyFromArray(Styles::FontSize(8));
                $event->sheet->getStyle("A8:E11")->applyFromArray(Styles::$BORDER_ALL_THIN);
                $event->sheet->getStyle("A8:E11")->applyFromArray(Styles::$BORDER_EXTERNAL_BOLD);
                $event->sheet->getStyle("G8:H11")->applyFromArray(Styles::$BORDER_ALL_THIN);
                $event->sheet->getStyle("G8:H11")->applyFromArray(Styles::$BORDER_EXTERNAL_BOLD);
                $event->sheet->getStyle("H8:H11")->applyFromArray(Styles::$HORIZONTAL_CENTER);

                // Partidas
                $event->sheet->getStyle("B13:D13")->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle("B13:D13")->applyFromArray(Styles::$BG_BLUE);
                $event->sheet->getStyle("A13:H14")->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle("A14:H14")->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle("A14:H14")->applyFromArray(Styles::$BG_BLUE);
                $event->sheet->getStyle("A14:H14")->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("A14:H14")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("B13:D13")->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("B13:D13")->applyFromArray(Styles::$HORIZONTAL_CENTER);

                $event->sheet->getStyle("A" . (12 + $n_partidas) . ":H" . (12 + $n_partidas))->applyFromArray(Styles::FontSize(11));
                $event->sheet->getStyle("A" . (15 + $n_partidas) . ":H" . (15 + $n_partidas))->applyFromArray(Styles::FontSize(8));
                $event->sheet->getStyle("A14:H" . (14 + $n_partidas))->applyFromArray(Styles::$BORDER_ALL_THIN);
                $event->sheet->getStyle("A14:H" . (14 + $n_partidas))->applyFromArray(Styles::$BORDER_EXTERNAL_BOLD);

                for ($i = 15; $i < 15 + $n_partidas; $i++)
                {
                    $event->sheet->getStyle("A$i:H$i")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                }

                // Notas
                $event->sheet->getStyle("A" . (16 + $n_partidas) . ":H" . (16 + $n_partidas))->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle("A" . (16 + $n_partidas) . ":H" . (16 + $n_partidas))->applyFromArray(Styles::$BG_BLUE);
                $event->sheet->getStyle("A" . (16 + $n_partidas) . ":H" . (16 + $n_partidas))->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("A" . (16 + $n_partidas) . ":H" . (16 + $n_partidas))->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("A" . (17 + $n_partidas) . ":H" . (17 + $n_partidas))->applyFromArray(Styles::$BG_YELLOW);
                $event->sheet->getStyle("A" . (16 + $n_partidas) . ":H" . (23 + $n_partidas))->applyFromArray(Styles::FontSize(11));
                $event->sheet->getStyle("A" . (16 + $n_partidas) . ":H" . (23 + $n_partidas))->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("A" . (16 + $n_partidas) . ":H" . (18 + $n_partidas))->applyFromArray(Styles::$BORDER_EXTERNAL_BOLD);
                $event->sheet->getStyle("A" . (16 + $n_partidas) . ":H" . (23 + $n_partidas))->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("A" . (16 + $n_partidas) . ":H" . (18 + $n_partidas))->applyFromArray(Styles::$BORDER_ALL_THIN);

                // Firmas
                $event->sheet->getStyle("G" . (20 + $n_partidas) . ":G" . (22 + $n_partidas))->applyFromArray(Styles::$BG_BLUE);
                $event->sheet->getStyle("G" . (20 + $n_partidas) . ":G" . (22 + $n_partidas))->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle("G" . (20 + $n_partidas) . ":G" . (22 + $n_partidas))->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("G" . (20 + $n_partidas) . ":G" . (22 + $n_partidas))->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("G" . (20 + $n_partidas) . ":G" . (22 + $n_partidas))->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle("G" . (20 + $n_partidas) . ":H" . (22 + $n_partidas))->applyFromArray(Styles::$BORDER_ALL_THIN);
                $event->sheet->getStyle("G" . (20 + $n_partidas) . ":H" . (22 + $n_partidas))->applyFromArray(Styles::$BORDER_EXTERNAL_BOLD);
                $event->sheet->getStyle("G" . (20 + $n_partidas) . ":H" . (22 + $n_partidas + 4))->applyFromArray(Styles::$HORIZONTAL_CENTER);

                $event->sheet->getStyle("A1:G" . (36 + $n_partidas))->applyFromArray(Styles::FontFamily("Arial"));

                $event->sheet->getDelegate()->getColumnDimension("A")->setWidth(9);
                $event->sheet->getDelegate()->getColumnDimension("B")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("C")->setWidth(13);
                $event->sheet->getDelegate()->getColumnDimension("D")->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension("E")->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension("F")->setWidth(100);
                $event->sheet->getDelegate()->getColumnDimension("G")->setWidth(35);
                $event->sheet->getDelegate()->getColumnDimension("H")->setWidth(44);
            }
        ];
    }

    public function view(): View
    {
        try
        {
            $requisicion = Requisicion::withRelations()
                ->with(
                    "tipo:id,nombre",
                    "proyecto:id,nombre_corto",
                )
                ->with(["solicita" => function ($query)
                {
                    $query->selectRaw("empleados.id, CONCAT_WS(' ', empleados.nombre, empleados.ap_paterno, empleados.ap_materno) as nombre");
                }])
                ->with(["aprueba" => function ($query)
                {
                    $query->selectRaw("empleados.id, CONCAT_WS(' ', empleados.nombre, empleados.ap_paterno, empleados.ap_materno) as nombre");
                }])
                ->with(["tipo" => function ($query)
                {
                    $query->select("id", "nombre");
                }])
                ->with(["area" => function ($query)
                {
                    $query->select("id", "nombre");
                }])
                ->select(
                    "*",
                    DB::raw("IF(requisiciones2.deleted_at is null,1,0) as condicion")
                )
                ->byid($this->id)
                ->first();

            $partidas = PartidaMaterial::with("unidadMedida")
                ->byRequisicion($this->id)
                ->select(
                    "requisicion_materiales_partidas.id",
                    "requi_id",
                    "concepto",
                    "comentarios",
                    "marca",
                    "cantidad",
                    "um_id",
                    "tipo",
                    "documentos_requeridos",
                )
                ->get();

            return view("excel.requisiciones.materialprincipal", compact("requisicion", "partidas"));
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }

    public function title(): string
    {
        return "Material";
    }
}
