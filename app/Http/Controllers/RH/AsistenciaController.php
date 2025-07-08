<?php

namespace App\Http\Controllers\RH;

use App\Exports\RH\AsistenciaExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\RHModels\RegChecador;
use Exception;

class AsistenciaController extends Controller
{

  /**
   * Busca las asistencias de las fechas ingresadas
   */
  public function BuscarAsistenciasFecha($id)
  {
    try
    {
      $ms = "";
      $TOTAL_POR_PAGINA = 20;

      // Fechas
      $dts = explode('&', $id);
      if (count($dts) != 3) return Status::Error2("Datos incompletos");
      $fecha_uno = $dts[0];
      $fecha_dos = $dts[1];
      $index = $dts[2];
      $fechas = [];

      $empleados = DB::table("reg_checador")
        ->select('empleado_id', 'empleado')
        ->orderBy('empleado')
        ->whereBetween('reg_checador.fecha', [$fecha_uno, $fecha_dos])
        ->groupBy('empleado_id', 'empleado')
        ->offset($index * $TOTAL_POR_PAGINA)
        ->limit($TOTAL_POR_PAGINA)
        ->get();

      $a = DB::table("reg_checador")
        ->select('empleado_id')
        ->whereBetween('fecha', [$fecha_uno, $fecha_dos])
        ->groupBy('empleado_id')
        ->get();

      $aux = count($a);
      $paginas = [];
      $n = ceil($aux / $TOTAL_POR_PAGINA);

      for ($i = 0; $i < $n; $i++)
      {
        array_push($paginas, ["index" => $i, "nombre" => $i + 1]);
      }

      $todo = [];
      // Obtener registros
      foreach ($empleados as $empleado)
      {
        $fecha_inicio = strtotime($fecha_uno);
        $fecha_fin = strtotime($fecha_dos);
        $dia = [];
        for ($i = $fecha_inicio; $i <= $fecha_fin; $i += 86400)
        {
          $fecha_actual = date("Y-m-d", $i);
          $asistencia = RegChecador::join('empleados AS e', 'e.id', 'reg_checador.empleado_id')
            ->select('reg_checador.*', DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS nombre"))
            ->where("empleado_id", $empleado->empleado_id)
            ->where("fecha", $fecha_actual)->get();

          $horario = "";
          foreach ($asistencia as $va)
          {
            // Autorizado o no
            $autorizado = $va->empleado_registra_id == 150 || $va->empleado_registra_id == 0 || $va->empleado_registra_id=737;
            $ht1 = $autorizado ? "<span class='badge badge-pill badge-success text-no-efect ml-1'>" :
              "<span class='badge badge-pill badge-danger text-no-efect no-autorizado ml-1
              data-toggle='tooltip' data-placement='top' title='No autorizado'>";
            $ht2 = "</span>";
            $horario .= $ht1 . (substr($va->hora, 0, 5)) . $ht2;
          }
          $ms = 0;
          array_push($dia, [
            "fecha" => $fecha_actual,
            "horario" => $horario,
            "reg_id" => 0,
            "estado" => 1,
            "comentarios" => '',
          ]);
        }
        array_push(
          $todo,
          ["asistencias" => $dia, "datos_empleado" =>
          ["id" => $empleado->empleado_id, "nombre" => $empleado->empleado]]
        );
      }

      // Obtener todas las fechas
      for ($i = strtotime($dts[0]); $i <= strtotime($dts[1]); $i += 86400)
      {
        $dia_nombre = $this->ObtenerNombreDia(date('Y-m-d', $i));
        array_push($fechas, [$dia_nombre, date('d/m/Y', $i)]);
      }
      return response()->json(
        [
          "status" => true, "asistencias" => $todo,
          "fechas" => $fechas,
          "paginas" => $paginas,
          "ms" => $ms,
        ]
      );
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener la asistencias");
    }
  }

  public function BuscarEmpleado(Request $request)
  {
    try
    {
      $ms = "";
      $TOTAL_POR_PAGINA = 20;
      DB::beginTransaction();

      $fecha_uno = $request->fecha_uno;
      $fecha_dos = $request->fecha_dos;
      $index = $request->index;
      $fechas = [];

      $empleados = RegChecador::select('empleado_id', 'empleado')
        ->orderBy('empleado')
        ->where('empleado', 'LIKE', '%' . $request->data . '%')
        ->whereBetween('reg_checador.fecha', [$fecha_uno, $fecha_dos])
        ->groupBy('empleado_id', 'empleado')
        ->offset($index * $TOTAL_POR_PAGINA)
        ->limit($TOTAL_POR_PAGINA)
        ->get();

      $a = RegChecador::select('empleado_id')
        ->where('empleado', 'LIKE', '%' . $request->data . '%')
        ->whereBetween('fecha', [$fecha_uno, $fecha_dos])
        ->groupBy('empleado_id')
        ->get();

      $aux = count($a);
      $paginas = [];
      $n = ceil($aux / $TOTAL_POR_PAGINA);

      for ($i = 0; $i < $n; $i++)
      {
        array_push($paginas, ["index" => $i, "nombre" => $i + 1]);
      }

      $todo = [];
      // Obtener registros
      foreach ($empleados as $empleado)
      {
        $fecha_inicio = strtotime($fecha_uno);
        $fecha_fin = strtotime($fecha_dos);
        $dia = [];
        for ($i = $fecha_inicio; $i <= $fecha_fin; $i += 86400)
        {
          $fecha_actual = date("Y-m-d", $i);
          $asistencia = RegChecador::join('empleados AS e', 'e.id', 'reg_checador.empleado_id')
            ->select('reg_checador.*', DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS nombre"))
            ->where("empleado_id", $empleado->empleado_id)
            ->where("fecha", $fecha_actual)->get();

          $horario = "";
          $registro = null;

          foreach ($asistencia as $ka => $va)
          {
            $horario .= (substr($va->hora, 0, 5)) . '-';
          }

          $ms = 0;
          array_push($dia, [
            "fecha" => $fecha_actual,
            "horario" => $horario,
            "reg_id" => 0,
            "estado" => 1,
            "comentarios" => '',
          ]);
        }
        array_push(
          $todo,
          ["asistencias" => $dia, "datos_empleado" =>
          ["id" => $empleado->empleado_id, "nombre" => $empleado->empleado]]
        );
      }

      $fecha_inicio = strtotime($fecha_uno);
      $fecha_fin = strtotime($fecha_dos);
      // Obtener todas las fechas
      for ($i = $fecha_inicio; $i <= $fecha_fin; $i += 86400)
      {
        $dia_nombre = $this->ObtenerNombreDia(date('Y-m-d', $i));
        array_push($fechas, [$dia_nombre, date('d/m/Y', $i)]);
      }
      DB::commit();
      return response()->json(
        [
          "status" => true, "asistencias" => $todo,
          "fechas" => $fechas,
          "paginas" => $paginas,
          "ms" => $ms,
        ]
      );
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener las asistencias");
    }
  }

  public function GeneraReporte($id)
  {
    $valores = explode('&', $id);
    $tipos = ["", "Conserflow_Semanal", "Conserflow_Quincena", "CSCT_Semanal", "CSCT_Quincena"];
    $tipo = $tipos[$valores[2]];
    ob_end_clean();
    ob_start();
    return Excel::download(new AsistenciaExport($id), 'Asistencia_' . $tipo . '.xlsx');
  }

  /**
   * Obtiene el día de la semana
   */
  private function ObtenerNombreDia($fecha)
  {
    $index_dia = date("w", strtotime($fecha));
    $dias = ["Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"];
    return $dias[$index_dia];
  }
}
