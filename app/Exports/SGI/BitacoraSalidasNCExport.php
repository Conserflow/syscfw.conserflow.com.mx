<?php

namespace App\Exports\SGI;

use App\Http\Controllers\Otros\Styles;
use App\SGIModels\SalidaNC;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class BitacoraSalidasNCExport implements FromView, WithEvents
{

    protected $anio;

    public function __construct(int $anio)
    {
        $this->anio = $anio;
    }

    public function registerEvents(): array
    {
        $n = SalidaNC::porAnio($this->anio)->count() + 6;
        return [
            AfterSheet::class => function (AfterSheet $event) use ($n)
            {
                $drawing = new Drawing();
                $drawing->setName("Logo");
                $drawing->setDescription("Logo");
                $drawing->setPath(public_path("img/conserflow.png"));
                $drawing->setCoordinates("A2");
                $drawing->setHeight(65);
                $drawing->setWorksheet($event->sheet->getDelegate());

                $event->sheet->getDelegate()->getRowDimension(2)->setRowHeight(20);
                $event->sheet->getDelegate()->getColumnDimension("A")->setWidth(23);
                $event->sheet->getDelegate()->getColumnDimension("B")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("C")->setWidth(17);
                $event->sheet->getDelegate()->getColumnDimension("D")->setWidth(35);
                $event->sheet->getDelegate()->getColumnDimension("E")->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension("F")->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension("G")->setWidth(35);
                $event->sheet->getDelegate()->getColumnDimension("H")->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension("I")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("J")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("K")->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension("L")->setWidth(20);

                $event->sheet->getStyle("A1:L1")->applyFromArray(Styles::$FONT_BLUE);
                $event->sheet->getStyle("A1:L1")->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle("B2:J4")->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle("K2:K4")->applyFromArray(Styles::$FONT_BOLD);

                $event->sheet->getStyle("B2:J4")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("B2:J4")->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("K2:L4")->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("A1:L4")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("A6:L6")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("A6:L6")->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("A6:L6")->applyFromArray(Styles::$BG_BLUE);
                $event->sheet->getStyle("A6:L6")->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle("A6:L6")->applyFromArray(Styles::$FONT_BOLD);

                $event->sheet->getStyle("A2:L4")->applyFromArray(Styles::FontFamily("Arial"));
                $event->sheet->getStyle("A1:L$n")->applyFromArray(Styles::FontSize(10));
                $event->sheet->getStyle("A6:L$n")->applyFromArray(Styles::$BORDER_ALL_THIN);
                $event->sheet->getDelegate()->getRowDimension(6)->setRowHeight(40);

                $event->sheet->getDelegate()->getStyle("A6:L6")->getAlignment()->setWrapText(true);
                for ($i = 7; $i < $n + 1; $i++)
                {
                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(25);
                    if ($i % 2 == 1)
                        $event->sheet->getStyle("A$i:L$i")->applyFromArray(Styles::$BG_GRAY2);
                    $event->sheet->getStyle("A$i:L$i")->applyFromArray(Styles::$VERTICAL_CENTER);
                }
                $event->sheet->getStyle("A1:L4")->applyFromArray(Styles::$BORDER_ALL_THIN);
                $event->sheet->getStyle("A1:L1")->applyFromArray(Styles::$BORDER_EXTERNAL_BOLD);
                $event->sheet->getStyle("A2:L4")->applyFromArray(Styles::$BORDER_EXTERNAL_BOLD);
            },
        ];
    }


    public function view(): View
    {
        $salidas = SalidaNC::porAnio($this->anio)->todos()->get();
        return view("excel.sgi.salidanc_bitacora", compact("salidas"));
    }
}
