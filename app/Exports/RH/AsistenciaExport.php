<?php

namespace App\Exports\RH;

use App\Http\Controllers\Otros\Styles;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AsistenciaExport implements FromView, WithEvents
{
  protected $id;

  public function __construct(string $id)
  {
    $this->id = $id;
  }

  /**
   * @return array
   */
  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event)
      {

        $event->sheet->getStyle('A1:B2')->applyFromArray(Styles::$HORIZONTAL_CENTER);
        $event->sheet->getStyle('A1:B2')->applyFromArray(Styles::$VERTICAL_CENTER);
        $event->sheet->getStyle('A1:Z2')->applyFromArray(Styles::$FONT_WHITE);
        $event->sheet->getStyle('A1:Z2')->applyFromArray(Styles::$FONT_BOLD);
        $event->sheet->getDelegate()->getColumnDimension("A")->setWidth(40);
        $event->sheet->getDelegate()->getColumnDimension("B")->setWidth(30);
      },
    ];
  }

  public function view(): View
  {
    $nomina = [];
    $suma = [];
    $asistencias_array = [];
    $valores = explode("&", $this->id);
    $fecha_inicial = $valores[0];
    $fecha_final = $valores[1];
    $tipo = $valores[2];

    $fechaInicio = strtotime($fecha_inicial);
    $fechaFin = strtotime($fecha_final);

    $fechas_asistencia = [];
    for ($i = $fechaInicio; $i <= $fechaFin; $i += 86400)
    {
      $fechas_asistencia[] = date("Y-m-d", $i);
      $dias[] = $this->get_nombre_dia(date("Y-m-d", $i));
    }
    $empleados = DB::table("empleados as e")
      ->leftJoin("reg_checador as rc", "e.id", "rc.empleado_id")
      ->select("e.id as empleado_id")
      ->where("e.id_checador", $tipo)
      ->where("e.condicion", 1)
      ->groupBy("e.id")
      ->orderBy("e.nombre")
      ->get()->toArray();
    $empleados = array_unique($empleados, SORT_REGULAR);

    foreach ($empleados as $e)
    {
      $emp = DB::table("empleados as e")
        ->join("puestos as p", "p.id", "e.puesto_id")
        ->select(
          DB::raw("CONCAT_WS(' ',e.nombre,ap_paterno,ap_materno) as empleado"),
          "p.nombre as puesto"
        )
        ->where("e.id", $e->empleado_id)
        ->first();

      $asistencias_arreglo = [];

      foreach ($fechas_asistencia as $a)
      {
        $asistencias = DB::table("reg_checador as rc")
          ->where("empleado_id", $e->empleado_id)
          ->where("fecha", $a)
          ->select("hora")
          ->orderBy("fecha")
          ->get();

        $ubicaciones = DB::select(
          "SELECT GROUP_CONCAT(ubicacion SEPARATOR ' - ') as ubicacion 
        from reg_checador rc where date(fecha) = ? and empleado_id =? limit 1",
          [$a, $e->empleado_id]
        )[0]->ubicacion;
        $asistencias_arreglo[] = [
          "asistencias" => $asistencias,
          "ubicaciones" => $ubicaciones
        ];
      }

      if (count($asistencias_arreglo) > 0)
      {
        $asistencias_array[] = [
          "empleado" => $emp,
          "registros" => $asistencias_arreglo,
        ];
      }
    }
    return view("excel.RH.asistencias", compact("fechas_asistencia", "dias", "asistencias_array"));
  }

  public function get_nombre_dia($fecha)
  {
    $index_dia = date("w", strtotime($fecha));
    $dias = ["Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"];
    return $dias[$index_dia];
  }
}
