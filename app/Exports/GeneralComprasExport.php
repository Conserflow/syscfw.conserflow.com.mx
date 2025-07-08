<?php

namespace App\Exports;

use App\Http\Controllers\Otros\Styles;
use App\Http\Helpers\Utilidades;
use Exception;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class GeneralComprasExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event)
            {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');
                $drawing->setPath(public_path('img/conserflow.png'));
                $drawing->setCoordinates('A2');
                $drawing->setHeight(60);
                $drawing->setWorksheet($event->sheet->getDelegate());

                $event->sheet->getDelegate()->getRowDimension(6)->setRowHeight(30);
                $event->sheet->getDelegate()->getColumnDimension("A")->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension("B")->setWidth(60);
                $event->sheet->getDelegate()->getColumnDimension("C")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("D")->setWidth(45);
                $event->sheet->getDelegate()->getColumnDimension("E")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("F")->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension("G")->setWidth(45);
                $event->sheet->getDelegate()->getColumnDimension("I")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("L")->setWidth(50);
                $event->sheet->getDelegate()->getColumnDimension("M")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("N")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("O")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("P")->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension("P")->setWidth(25);

                $event->sheet->getStyle("A6:Q6")->applyFromArray(Styles::$FONT_WHITE);
                $event->sheet->getStyle("A6:Q6")->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle("A1:Q6")->applyFromArray(Styles::$HORIZONTAL_CENTER);
                $event->sheet->getStyle("A1:Q6")->applyFromArray(Styles::$VERTICAL_CENTER);
                $event->sheet->getStyle("B2:N4")->applyFromArray(Styles::$FONT_BOLD);
                $event->sheet->getStyle("A1:p1")->applyFromArray(Styles::$FONT_BOLD);
            },
        ];
    }

    public function view(): View
    {
        try
        {
            $ids = $this->ids;
            $ids_str=implode(",", $ids);
            $ids_str="$ids_str";
            // Obtener las ocs y los articulos
            $articulos = DB::select("SELECT
            oc.id as oc_id,
            oc.periodo_entrega,
            oc.folio as oc_folio,
            oc.conrequisicion,
            r.fecha_solicitud as fecha_requi,
            r.folio as req_folio,
            rho.cantidad,rho.precio_unitario,
            oc.condicion,oc.fecha_orden,
            round(rho.cantidad * rho.precio_unitario,2) as total,
            oc.moneda,
            p.razon_social,
            cp.nombre  as metodo,
            a.descripcion,
            um.nombre as um,
            p2.nombre_corto,
            g.nombre as grupo
            from ordenes_compras oc
            left join requisicion_has_ordencompras rho on rho.orden_compra_id =oc.id
            left join requisiciones r on r.id =rho.requisicione_id
            join proveedores p on p.id=oc.proveedore_id
            join proyectos p2 on p2.id=oc.proyecto_id
            left join condicion_pago cp on cp.id=oc.condicion_pago_id
            join articulos a on a.id=rho.articulo_id
            join grupos g on g.id=a.grupo_id
            left join unidades_medida um on um.id=a.um_id
            where oc.proyecto_id in ($ids_str) and oc.folio not like 'EP-OC%'");

            // Obtener las ocs y los servicios
            $servicios = DB::select("SELECT
            oc.id as oc_id,
            r.fecha_solicitud as fecha_requi,
            r.folio as req_folio,
            oc.conrequisicion,
            oc.folio as oc_folio,
            s.nombre_servicio as descripcion,
            oc.moneda,
            cantidad,rho.precio_unitario,
            round(rho.cantidad * rho.precio_unitario,2) as total,
            p.razon_social,
            cp.nombre as metodo,
            oc.condicion,
            oc.fecha_orden,
            oc.periodo_entrega,
            p2.nombre_corto,
            'SERVICIO' as um,
            'SERVICIO' as grupo
            from ordenes_compras oc
            left join requisicion_has_ordencompras rho on rho.orden_compra_id =oc.id
            left join requisiciones r on r.id =rho.requisicione_id
            join proveedores p on p.id=oc.proveedore_id
            join proyectos p2 on p2.id=oc.proyecto_id
            join servicios s on s.id=rho.servicio_id
            left join condicion_pago cp on cp.id=oc.condicion_pago_id
            where oc.proyecto_id in ($ids_str) and oc.folio not like 'EP-OC%';");

            $servicios_vehiculos = in_array(60,$ids) ? $this->servicios_vehiculos() : [];
            $ocs = array_merge($articulos, $servicios, $servicios_vehiculos); // unir articulos y servicios
            // ordernar por id
            usort($ocs, function ($a, $b)
            {
                return $a->oc_folio > $b->oc_folio;
            });

            usort($ocs, function ($a, $b)
            {
                return $a->oc_folio > $b->oc_folio;
            });
            return view('excel.compras.generalcompras', compact('ocs'));
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
        }
    }

    private function servicios_vehiculos()
    {
        $ids = 60;
        // Obtener las ocs y los servicios
        $servicios = DB::select("SELECT
        oc.id as oc_id,
        r.fecha_solicitud as fecha_requi,
        r.folio as req_folio,
        oc.conrequisicion,
        oc.folio as oc_folio,
        cmv.descripcion as descripcion,
        oc.moneda,
        cantidad,rho.precio_unitario,
        round(rho.cantidad * rho.precio_unitario,2) as total,
        p.razon_social,
        cp.nombre as metodo,
        oc.condicion,
        oc.fecha_orden,
        oc.periodo_entrega,
        p2.nombre_corto,
        'SERVICIO' as um,
        'SERVICIO' as grupo
        from ordenes_compras oc
        left join requisicion_has_ordencompras rho on rho.orden_compra_id =oc.id
        left join requisiciones r on r.id =rho.requisicione_id
        join proveedores p on p.id=oc.proveedore_id
        join proyectos p2 on p2.id=oc.proyecto_id
        join cat_mantenimiento_vehiculos cmv on cmv.id=rho.vehiculo_id
        left join condicion_pago cp on cp.id=oc.condicion_pago_id
        where oc.proyecto_id =$ids");
        return $servicios;
    }
}
