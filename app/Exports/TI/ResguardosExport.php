<?php

namespace App\Exports\TI;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use \App\Http\Helpers\Utilidades;
use App\TIModels\TiAccesorio;
use App\TIModels\TiComputo;
use App\TIModels\TiImpresion;
use App\TIModels\TiVideo;
use Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ResguardosExport implements FromView, WithEvents
{

    public function __construct()
    {
    }

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

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(5);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(13);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(50);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(35);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(35);
            },
        ];
    }

    public function view(): View
    {
        try
        {
            $resguardos = DB::table("ti_material_resguardo as tmr")
                ->join("empleados as e", "e.id", "tmr.empleado_recibe")
                ->join("empleados as ee", "ee.id", "tmr.empleado_entrega")
                ->select(
                    "tmr.*",
                    DB::raw("CONCAT_WS(' ',e.nombre,e.ap_paterno,e.ap_materno) as empleado_r"),
                    DB::raw("CONCAT_WS(' ',ee.nombre,ee.ap_paterno,ee.ap_materno) as empleado_e"),
                    DB::raw("'-' as descripcion")
                )
                ->where("tmr.estado", 1)
                ->orderBy("tmr.fecha")
                ->orderBy("empleado_r")
                ->get();

            $list_resguardos = [];
            foreach ($resguardos as $r)
            {
                $desc = "";
                if ($r->tipo == 1)
                {
                    $cdata = TiComputo::where("id", $r->caiv)
                        ->select(
                            DB::raw("CONCAT_WS(' ',no_serie,marca_modelo,cpu,ram,almacenamiento,
                        tarjeta_video,tarjeta_red,observaciones,mac) as descripcion")
                        )
                        ->first();
                    $desc = $cdata->descripcion;
                }
                if ($r->tipo == 2)
                {
                    $cdata = TiAccesorio::select(DB::raw("CONCAT_WS(' ',descripcion,modelo,marca,no_serie) as descripcion"))
                        ->where("id", $r->caiv)
                        ->first();
                    $desc = $cdata->descripcion;
                }
                if ($r->tipo == 3)
                {
                    $cdata = TiImpresion::select(DB::raw("CONCAT_WS(' ',descripcion,modelo,marca,no_serie) as descripcion"))
                        ->where("id", $r->caiv)
                        ->first();
                    $desc = $cdata->descripcion;
                }
                if ($r->tipo == 4)
                {
                    $cdata = TiVideo::select(DB::raw("CONCAT_WS(' ',descripcion,modelo,marca,no_serie) as descripcion"))
                        ->where("id", $r->caiv)
                        ->first();
                    $desc = $cdata->descripcion;
                }
                $r->descripcion = $desc;
                $list_resguardos[] = $r;
            }
            return view("excel.ti.resguardos", compact("list_resguardos"));
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view('errors.500');
        }
    }
}
