<?php

namespace App\Exports\RH;

use App\RHModels\Correo;
use App\DireccionEmpleado;
use App\RHModels\Expediente;
use App\Familiare;
use App\Http\Helpers\Utilidades;
use App\RHModels\Referencia;
use App\RHModels\Beneficiario;
use App\RHModels\Contacto;
use App\RHModels\DatosBancariosEmpleado;
use App\RHModels\Telefono;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class EmpleadosGeneralExport  implements FromView, WithEvents
{
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event)
            {
                $drawing = new Drawing();
                $drawing->setName("Logo");
                $drawing->setDescription("Logo");
                $drawing->setPath(public_path("img/conserflow.png"));
                $drawing->setCoordinates("A2");
                $drawing->setHeight(60);
                $drawing->setWorksheet($event->sheet->getDelegate());
                $event->sheet->getDelegate()->getColumnDimension("A")->setWidth(35);
                $event->sheet->getDelegate()->getColumnDimension("B")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("C")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("D")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("F")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("G")->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension("H")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("I")->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension("J")->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension("K")->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension("L")->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension("M")->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension("N")->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension("O")->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension("P")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("Q")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("Q")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("Q")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("AX")->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension("AY")->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension("AZ")->setWidth(35);
            },
        ];
    }

    public function view(): View
    {
        try
        {
            $empleados_aux = DB::table("empleados as e")
                ->join("tipo_ubicacion as tu", "tu.id", "e.ubicacion_id")
                ->join("estados_civiles as ec", "ec.id", "e.edo_civil_id")
                ->select(
                    DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as empleado"),
                    "e.*",
                    "tu.nombre as ubicacion",
                    "ec.nombre as estado_civil"
                )
                ->where("e.condicion", 1)
                ->orderBy("empleado")
                ->get();

            $empleados = [];
            foreach ($empleados_aux as $e)
            {
                $contacto = Contacto::where("empleado_id", $e->id)->first();
                if ($contacto == null) $contacto = new Contacto();

                $banco = DatosBancariosEmpleado::where("condicion", 1)
                    ->where("empleado_id", $e->id)->first();
                if ($banco == null) $banco = new DatosBancariosEmpleado();

                $familiares = Familiare::where("empleado_id", $e->id)
                    ->where("condicion", 1)->get();
                $aux_fami = "";
                foreach ($familiares as $f)
                {
                    $aux_fami .= $f->nombre . ": " . $f->parentesco . "|";
                }

                $referencias = Referencia::where("empleado_id", $e->id)
                    ->where("condicion", 1)->get();
                $aux_referencias = "";
                foreach ($referencias as $c)
                {
                    $aux_referencias .= $c->nombre . ": " . $c->ocupacion . $c->direccion
                        . $c->telefono . "|";
                }

                $expedientes = Expediente::where("empleado_id", $e->id)->first();
                if ($expedientes == null) $expedientes = new Expediente();

                $direccion = DireccionEmpleado::where("empleado_id", $e->id)
                    ->where("condicion", 1)->first();
                $aux_direccion = "";
                if ($direccion == null) $aux_direccion = "";
                else
                {
                    $aux_direccion = "$direccion->calle  $direccion->numero_exterior "
                        . "$direccion->numero_interior $direccion->colonia $direccion->localidad "
                        . "$direccion->entidad_federativa $direccion->municipio";
                }

                $aux_benef = "";
                $beneficiario = Beneficiario::where("empleado_id", $e->id)
                    ->where("condicion", 1)->first();
                if ($beneficiario == null) $beneficiario = new Beneficiario();

                $telefono = Telefono::where("empleado_id", $e->id)
                    ->where("condicion", 1)->first();
                if ($telefono == null) $telefono = new Telefono();
                $correo = Correo::where("empleado_id", $e->id)
                    ->where("condicion", 1)->first();
                if ($correo == null) $correo = new Correo();

                $aux = (array)$e;
                $aux["contacto"] = $contacto;
                $aux["banco"] = $banco;
                $aux["familiares"] = $aux_fami;
                $aux["conocidos"] = [];
                $aux["referencias"] = $aux_referencias;
                $aux["expedientes"] = $expedientes;
                $aux["direccion"] = $aux_direccion;
                $aux["beneficiario"] = $aux_benef;
                $aux["telefono"] = $telefono;
                $aux["correo"] = $correo;
                $empleados[] = $aux;
            }
            return view("excel.RH.generalempleados", compact("empleados"));
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
