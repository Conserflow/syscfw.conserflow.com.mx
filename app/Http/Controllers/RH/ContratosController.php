<?php

namespace App\Http\Controllers\RH;

use App\DireccionEmpleado;
use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Otros\Utils;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\RHModels\Beneficiario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\RHModels\Contrato;
use App\RHModels\Empleado;
use App\RHModels\Sueldo;
use Barryvdh\DomPDF\Facade;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ContratosController extends Controller
{

  /**
   * Obtener los contratos del empleado seleccionado
   */
  public function ObtenerContratos($emp_id)
  {
    try
    {
      $contratos = DB::table("contratos as c")
        ->select(
          "c.id",
          "c.proyecto_id",
          "c.tipo_contrato_id",
          "c.tipo_nomina_id",
          "c.horario_id",
          "c.fecha_ingreso",
          "c.fecha_fin",
          "c.puesto_id",
          "c.condicion",
          "c.testigo1_id",
          "c.testigo2_id",
          "c.finiquitado",
          "c.tipo_ubicacion_id",
          "c.empleado_id",
          "c.empresa_id",
          "tu.nombre as ubicacion",
          "pr.nombre_corto as proyecto",
          "pu.nombre as puesto",
          "tn.nombre as tipo_nomina",
          "tc.nombre as tipo_contrato",
          "tfc.nombre as motivo_fin",
          DB::raw("concat_ws(' ',e1.nombre,e1.ap_paterno,e1.ap_materno) as testigo1"),
          DB::raw("concat_ws(' ',e2.nombre,e2.ap_paterno,e2.ap_materno) as testigo2")
        )
        ->leftJoin("tipo_ubicacion as tu", "c.tipo_ubicacion_id", "tu.id")
        ->leftJoin("proyectos as pr", "c.proyecto_id", "pr.id")
        ->leftJoin("puestos as pu", "c.puesto_id", "pu.id")
        ->leftJoin("tipo_nomina as tn", "c.tipo_nomina_id", "tn.id")
        ->leftJoin("tipo_contratos as tc", "c.tipo_contrato_id", "tc.id")
        ->leftJoin("tipo_fin_contrato as tfc", "c.motivo_fecha_fin", "tfc.id")
        ->leftJoin("empleados as e1", "e1.id", "c.testigo1_id")
        ->leftJoin("empleados as e2", "e2.id", "c.testigo2_id")
        ->where("c.empleado_id", $emp_id)->get();

      return Status::Success("contratos", $contratos);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los contratos");
    }
  }

  /**
   * Obtiene los empleados, con su fecha de inicio/termino de contrato
   */
  public function ObtenerEmpleados($empresa_id)
  {
    try
    {
      // Obtener los empleados
      $rango = $empresa_id == 1 ? [1, 2] : [3, 4]; // 1. Conser 2.CSCT
      $empleados = DB::table("empleados as e")
        ->whereIn("id_checador", $rango) // Empresa
        ->where("e.condicion", 1) // Activos
        ->select(
          "e.id",
          "e.nombre",
          "e.ap_paterno",
          "e.ap_materno"
        )
        ->orderBy("nombre")
        ->get();

      $list_contratos = [];
      foreach ($empleados as $e)
      {
        $contrato = DB::table("contratos as c")
          ->select(
            "c.fecha_ingreso",
            "c.fecha_fin"
          )
          ->where("c.empleado_id", $e->id)
          ->where("c.condicion", 1)
          ->first();
        if ($contrato == null)
        {
          $inicio = "N/D";
          $fin = "N/D";
          $condicion = 0; // ND
        }
        else
        {
          $inicio = $contrato->fecha_ingreso;
          $fin = $contrato->fecha_fin;
          $hoy = date("Y-m-d");
          // proximo a vencer
          $limite = date("Y-m-d", strtotime($hoy . "+ 30 days"));
          if ($fin <= $hoy)
          {
            $condicion = 3; // Vencido
          }
          else if ($limite >= $fin)
          {
            $condicion = 2; // Por vencer
          }
          else $condicion = 1;
        }
        // Fechas
        $aux = (array)$e;
        $aux["inicio"] = $inicio;
        $aux["fin"] = $fin;
        $aux["condicion"] = $condicion;
        $list_contratos[] = $aux;
      }
      return Status::Success("empleados", $list_contratos);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los empleados");
    }
  }

  /**
   * Obtener los testigos del contrato ingresado
   */
  public function ObtenerTestigos($c_id)
  {
    try
    {
      $testigos = DB::table("testigos_contratos as tc")
        ->where("tc.contrato_id", $c_id)
        ->get();
      $n = count($testigos);
      $t1 = null;
      $t2 = null;
      if ($n >= 1)
      {
        $t1 = DB::table("empleados as e")
          ->select("id", DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre"))
          ->where("e.id", $testigos[0]->empleado_id)
          ->first();
      }
      if ($n >= 2)
      {
        $t2 = DB::table("empleados as e")
          ->select("id", DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre"))
          ->where("e.id", $testigos[1]->empleado_id)
          ->first();
      }
      $total = ["n" => $n, "empleados" => [$t1, $t2]];

      return Status::Success("testigos", $total);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los testigos");
    }
  }

  public function GuardarContrato(Request $request)
  {
    try
    {
      if (!$request->ajax()) return redirect("/");

      DB::beginTransaction();
      if ($request->id == null) // Nuevo
      {
        $contrato = new Contrato($request->all());
        $contrato->empleado_registra_id = Auth::user()->empleado_id;
        $contrato->save();
        Auditoria::AuditarCambios($contrato);

        // Buscar empleado para obtener sueldo neto
        $emp = Empleado::find($request->empleado_id);
        $neto = $emp->salario_neto;
        $dias = $request->tipo_nomina_id == 1 ? 7 : 15;

        // Crear salario inicial
        $sueldo = new Sueldo($request->all());
        $sueldo->contrato_id = $contrato->id;
        $sueldo->sueldo_diario_integral = 0; // SDI
        $sueldo->sueldo_diario_neto = number_format(($neto / $dias), 2, ".", ""); // Diario
        $sueldo->sueldo_diario_real = $neto; // Neto
        $sueldo->fecha_act = date("Y-m-d");
        $sueldo->sueldo_mensual = 0;
        $sueldo->infonavit = 0;
        $sueldo->viaticos_mensuales = 0;
        $sueldo->empleado_registra_id = Auth::user()->empleado_id;
        $sueldo->save();
        Auditoria::AuditarCambios($sueldo);
      }
      else
      {
        $contrato = Contrato::find($request->id);
        $contrato->fill($request->all());
        Auditoria::AuditarCambios($contrato);
        $contrato->update();
      }
      DB::commit();
      return Status::Success("c_id", $contrato->id);
    }
    catch (Exception $e)
    {
      DB::rollBack();
      return Status::Error($e, "guardar el contrato");
    }
  }

  /**
   * Descarga el nuevo contrato 2022
   */
  public function DescargarNuevo($c_id)
  {
    try
    {
      // Obtener los datos
      $contrato = DB::table("contratos as c")
        ->join("empleados as e", "e.id",  "c.empleado_id")
        ->leftJoin("puestos as pu", "pu.id",  "c.puesto_id")
        ->join("proyectos as pr", "pr.id",  "c.proyecto_id")
        ->join("estados_civiles as ec", "ec.id",  "e.edo_civil_id")
        ->join("tipo_ubicacion as tu", "tu.id", "c.tipo_ubicacion_id")
        ->leftJoin("tipo_contratos as tc", "tc.id",  "c.tipo_contrato_id")
        ->where("c.id", $c_id)
        ->select(
          "c.id",
          "c.tipo_contrato_id",
          "tc.nombre as tipo_contrato",
          "e.id as empleado_id",
          DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as empleado"),
          "e.fech_nac as fecha_nac",
          "e.sexo",
          "ec.nombre as edo_civil",
          "e.curp",
          "e.rfc",
          "pr.nombre_corto as proyecto",
          "c.fecha_ingreso",
          "c.fecha_fin",
          "c.tipo_nomina_id",
          "pu.nombre as puesto",
          "tu.nombre as ubicacion"
        )
        ->where("c.id",  $c_id)
        ->first();
      $contrato = (array)$contrato;

      // obtener domicilio
      $direccion = DireccionEmpleado::where("empleado_id", $contrato["empleado_id"])
        ->where("condicion", 1)->first();
      $aux_direccion = '';
      if ($direccion == null) $aux_direccion = '';
      else
      {
        $aux_direccion = "Calle $direccion->calle  $direccion->numero_exterior "
          . "$direccion->numero_interior Col. $direccion->colonia $direccion->localidad "
          . "$direccion->entidad_federativa $direccion->municipio";
      }
      $contrato["direccion"] = $aux_direccion;

      // Beneficiario
      $beneficiario = Beneficiario::where("empleado_id", $contrato["empleado_id"])
        ->where("condicion", 1)->first();
      $beneficiario_nombre = $beneficiario == null ? "N/D" : $beneficiario->nombre;
      $contrato["beneficiario"] = $beneficiario_nombre;

      // Salario
      $salario = DB::table("sueldos as s")
        ->where("s.contrato_id", $contrato["id"])
        ->select(
          "sueldo_diario_neto", // sueldo_diario_neto -> Sueldo Neto por Dia,
          "sueldo_diario_integral"  // sdi*dias=bruto
        )
        ->orderBy("id", "desc") // Ultimo
        ->first();

      if ($salario == null) $salario = new Sueldo();
      // Salario diario en letra
      $sd = $salario->sueldo_diario_neto;
      $entero = floor($sd);
      $centavos = round(($sd - $entero) * 100, 0);
      $sdi_letra = "$entero pesos $centavos centavos, Moneda Nacional";
      $contrato["sdi_numero"] = $salario->sueldo_diario_neto;
      $contrato["sdi_letra"] = $sdi_letra;
      // Tipo nomina
      $contrato["tipo_nomina"] = $contrato["tipo_nomina_id"] == 1 ? "Semanas" : "Quincenas";
      // Horario de comida
      $hora_salida_comida = $contrato["tipo_nomina"] == 1 ? "13:30" : "14:30";
      $hora_regreso_comida = $contrato["tipo_nomina"] == 1 ? "14:30" : "15:30";
      $contrato["hora_regreso_comida"] = $hora_regreso_comida;
      $contrato["hora_salida_comida"] = $hora_salida_comida;
      //Edad
      $hoy = new DateTime($contrato["fecha_ingreso"]); // Fecha del contrato
      $fecha_nac = new DateTime($contrato["fecha_nac"]);
      $edad = $hoy->diff($fecha_nac)->y;
      $dia = date("d");
      $mes_hoy = Utils::NombreMesNumero(date("m"));
      $anio = date("Y");
      $contrato["edad"] = $edad;
      $contrato["dia"] = $dia;
      $contrato["mes_hoy"] = $mes_hoy;
      $contrato["anio"] = $anio;

      // Fecha inicio contrato
      $ingreso_pts = explode("-", $contrato["fecha_ingreso"]);
      $mes_inicio = Utils::NombreMesFecha($contrato["fecha_ingreso"]);
      $fecha_inicio = "$ingreso_pts[2] del mes de $mes_inicio del año $ingreso_pts[0]";
      $contrato["fecha_inicio"] = $fecha_inicio;

      // Fecha fin contrato
      $fin_pts = explode("-", $contrato["fecha_fin"]);
      $mes_fin = Utils::NombreMesFecha($contrato["fecha_fin"]);
      $fecha_fin = "$fin_pts[2] del mes de $mes_fin del año $fin_pts[0]";
      $contrato["fecha_fin"] = $fecha_fin;

      $contrato = (object)$contrato;

      // Definir el tipo de cotrato
      if ($contrato->tipo_contrato_id == 4) //INDETERMINADO
      {
        $pdf = Facade::loadView("pdf.rh.nuevocontratoindeterminado", compact("contrato"));
      }
      else if ($contrato->tipo_contrato_id == 5) //DETERMINADO
      {
        $pdf = Facade::loadView("pdf.rh.nuevocontratodeterminado", compact("contrato"));
      }
      else if ($contrato->tipo_contrato_id == 6) //POR OBRA
      {
        $pdf = Facade::loadView("pdf.rh.nuevocontratoobra", compact("contrato"));
      }
      else
      {
        $ms = "Tipo de contrato incorrecto. Id: $contrato->id. Tipo: $contrato->tipo_contrato";
        Utilidades::errors(new Exception($ms));
        return view("errors.204");
      }
      $nombre = "$contrato->empleado - CONTRATO $contrato->tipo_contrato.pdf";

      $pdf->setPaper("letter", "portrait");
      $pdf->getDomPDF()->set_option("enable_php", true);
      return $pdf->stream($nombre);
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return view("errors.500");
    }
  }

  /**
   * Finalizar contrato
   */
  public function Finalizar(Request $request)
  {
    try
    {
      if (!$request->ajax()) return redirect("/");

      $contrato = Contrato::find($request->id);
      $contrato->motivo_fecha_fin = $request->motivo_fecha_fin;
      $contrato->fecha_real_fincontrato = date("Y-m-d");
      $contrato->condicion = 0;
      if ($request->motivo_fecha_fin == 2) // Finiquito dado: Renuncia, abandono
        $contrato->finiquitado = 2;
      else
        $contrato->finiquitado = 1;
      Auditoria::AuditarCambios($contrato);
      $contrato->update();
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "finalizar el contrato");
    }
  }

  /**
   * Obtener la fecha de imss, para inicio de contrato
   */
  public function ObtenerFecha(Request $request)
  {
    try
    {
      $empleado = Empleado::find($request->empleado_id);
      return Status::Success("fecha", $empleado->fech_alta_imss);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener la fecha de inicio");
    }
  }
}
