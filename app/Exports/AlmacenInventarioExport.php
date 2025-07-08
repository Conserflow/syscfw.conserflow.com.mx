<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use \App\Http\Helpers\Utilidades;
use Exception;

class AlmacenInventarioExport implements FromView, WithEvents
{
    protected $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function registerEvents(): array
    {
        $styleArray = [
            [
                'font' => [
                    'bold'       =>  true,
                    'color' => ['argb' => '0489B1'],
                ],
            ],
            [
                'font' => [
                    'bold'       =>  true,
                    'color' => ['argb' => '000000'],
                ],
            ],
            [
                'font' => [
                    'bold'       =>  true,
                    'color' => ['argb' => 'ffffff'],
                ],
            ],
            [

                'alignment' =>
                [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ],
            [
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ],
            [

                'alignment' =>
                [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],

        ];
        return [

            AfterSheet::class => function (AfterSheet $event) use ($styleArray)
            {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');
                $drawing->setPath(public_path('img/conserflow.png'));
                $drawing->setCoordinates('A2');
                $drawing->setHeight(60);
                $drawing->setWorksheet($event->sheet->getDelegate());

                $event->sheet->getStyle('A7:F7')->applyFromArray($styleArray[2]);
                $event->sheet->getStyle('A7:F7')->applyFromArray($styleArray[3]);

                $event->sheet->getStyle('B2:D4')->applyFromArray($styleArray[1]);
                $event->sheet->getStyle('B2:D4')->applyFromArray($styleArray[3]);
                $event->sheet->getStyle('B2:D4')->applyFromArray($styleArray[6]);

                // $event->sheet->getStyle('B2:E4')->applyFromArray($styleArray[6]);

                $event->sheet->getStyle('A1:F1')->applyFromArray($styleArray[3]);
                $event->sheet->getStyle('A1:F4')->applyFromArray($styleArray[4]);
                $event->sheet->getStyle('A1:F4')->applyFromArray($styleArray[5]);

                $event->sheet->getStyle('A1:A300')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(50);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(25);
            },
        ];
    }

    public function view(): View
    {
        try
        {
            // Obtener los artículos por grupo y sumar cantidad ingesada
            $articulos = DB::select("
            SELECT gi.nombre,ai.existecia_sistema,SUM(ai.existencia_real) as existencia_real,
            ' ' as observaciones
            from alm_articulos_grupos aag
            join grupos_inventario gi on gi.id =aag.grupo_id
            join alm_inventario ai on ai.id=aag.art_inv_id
            GROUP by gi.nombre,ai.existecia_sistema");

            return view('excel.almaceninventario', compact("articulos"));
        }
        catch (Exception $e)
        {
            dd($e);
            Utilidades::errors($e);
            return view('errors.500');
        }
    }
}
