<?php

namespace App\Exports;

use App\Http\Controllers\Otros\Styles;
use App\Http\Helpers\Utilidades;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class CombustibleExport implements FromView, WithEvents
{
    private $inicio;
    private $fin;
    private $ubicacion;

    public function __construct($inicio, $fin, $ubicacion)
    {
        $this->inicio = $inicio;
        $this->fin = $fin;
        $this->ubicacion = $ubicacion;
    }

    public function registerEvents(): array
    {

        return [
            //   Estilos
            AfterSheet::class => function (AfterSheet $event)
            {

                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');
                $drawing->setPath(public_path('img/conserflow.png'));
                $drawing->setCoordinates('A2');
                $drawing->setHeight(50);
                $drawing->setWorksheet($event->sheet->getDelegate());

                $event->sheet->getStyle('A1:Q1')->applyFromArray(Styles::$FONT_BLUE);
                $event->sheet->getStyle('A1:Q1')->applyFromArray(Styles::$FONT_BOLD);

                $event->sheet->getStyle('A4:Q4')->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle('A4:Q4')->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle('A4:Q4')->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle('A4:Q4')->applyFromArray(Styles::$HORIZONTAL_CENTER);

                $event->sheet->getStyle('A1:Q4')->applyFromArray(Styles::$BORDER_ALL_THIN);
                $event->sheet->getStyle('A1:Q4')->applyFromArray(Styles::$HORIZONTAL_CENTER);

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('l')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('P')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('Q')->setWidth(15);

                $event->sheet->getDelegate()->getRowDimension(2)->setRowHeight(40);
                $event->sheet->getDelegate()->getRowDimension(4)->setRowHeight(20);
            },
        ];
    }

    public function view(): View
    {
        try
        {
            // Vales de combustible
            $query_vales = "SELECT 
            c.*, p.nombre_corto, u.unidad,u.placas,
            concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre,
            vp.razon_social
            from  combustible as c
            left join proyectos as p on c.proyecto_id =p.id
            left join empleados as e on e.id= c.operador_id
            left join unidades as u on u.id=c.unidad_id
            left join vehiculos_proveedores as vp on vp.id=c.proveedor_id
            where (fecha >='" .
                $this->inicio . "' and fecha<='" . $this->fin . "')
            and ubicacion = $this->ubicacion and tipo_deposito = 1
            order by folio"; // vale
            $vales =
                DB::select($query_vales);

            $query_transferencias = "SELECT 
            c.*, p.nombre_corto, u.unidad,u.placas,
            concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre,
            vp.razon_social
            from  combustible as c
            left join proyectos as p on c.proyecto_id =p.id
            left join empleados as e on e.id= c.operador_id
            left join unidades as u on u.id=c.unidad_id
            left join vehiculos_proveedores as vp on vp.id=c.proveedor_id
            where (fecha >='" .
                $this->inicio . "' and fecha<='" . $this->fin . "')
            and ubicacion = $this->ubicacion  and tipo_deposito = 2
            order by fecha"; // transferencia
            $transferencias = DB::select($query_transferencias);
            return view("excel.combustible", compact('vales',"transferencias"));
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
