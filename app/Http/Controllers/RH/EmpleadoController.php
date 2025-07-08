<?php

namespace App\Http\Controllers\RH;

use App\Exports\RH\EmpleadosGeneralExport;
use App\Http\Controllers\Auditoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Otros\Utils;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\Proyecto;
use App\RHModels\Empleado;
use App\RHModels\Historial;
use App\RHModels\Puesto;
use App\RHModels\Sueldo;
use App\User;
use Barryvdh\DomPDF\Facade;
use DateTime;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class EmpleadoController extends Controller
{

  /**
   * FIXME: Cambiar todas las rutas get "/empleado"
   * Obtiene los empleados 
   */
  public function EmpleadoTemp()
  {
    $empleados = DB::table("empleados as e")
      ->where("e.condicion", 1)
      ->orderBy("nombre")
      ->get();
    $arreglo = [];
    foreach ($empleados as $e)
    {
      $arreglo[] = [
        "empleado" => $e,
        "puesto" => "",
        "departamento" => "",
        "estados" => "",
        "ubicaciones" => "",
      ];
    }
    return response()->json($arreglo);
  }

  /**
   * Obtener los empleados de la empresa seleccionada
   */
  public function ObtenerEmpleadosEmpresa($emp_id)
  {
    try
    {

      $rango = $emp_id == 1 ? [1, 2] : [3, 4]; // 1. Conser 2.CSCT
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
      return Status::Success("empleados", $empleados);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los empleados");
    }
  }

  /**
   * Obtener todos los empleados para RH
   */
  public function index()
  {
    try
    {
      $empleados = DB::table("empleados as e")
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
        $aux["c_inicio"] = $inicio;
        $aux["c_fin"] = $fin;
        $aux["c_condicion"] = $condicion;
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
   * Guardar el empleado
   * CONSERFLOW SEMANAL = 1
   * CONSERFLOW QUINCENAL = 2
   * CSCT SEMANAL = 3
   * CSCT QUINCENAL = 4
   */
  public function store(Request $request)
  {
    try
    {
      if (!$request->ajax()) return redirect("/");
      $datos = LimpiarInput::LimpiarCampos(
        $request->all(),
        [
          "nombre",
          "ap_paterno",
          "ap_materno",
          "lug_nac",
          "curp",
          "rfc",
          "nss_imss",
          "fiscal_cp",
          "fiscal_estado"
        ]
      );
      if ($request->id == null)
      {
        // Validar CURP
        $existe = DB::table("empleados")->where("curp", $request->curp)->first();
        if ($existe != null) return Status::Error2("CURP ya existe");

        // Validar RFC
        $existe = DB::table("empleados")->where("rfc", $request->rfc)->first();
        if ($existe != null) return Status::Error2("El RFC ya existe");

        $empleado = new Empleado($datos);
        $empleado->empleado_registra_id = Auth::user()->empleado_id;
        $empleado->save();
        Auditoria::AuditarCambios($empleado);
      }
      else
      {
        $empleado = Empleado::find($request->id);
        $empleado->fill($datos);
        Auditoria::AuditarCambios($empleado);
        $empleado->update();
      }
      return Status::Success("emp", $empleado);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "guardar el empleado");
    }
  }

  /**
   * Muestra los datos del empleado ingresado
   */
  public function show($id)
  {
    try
    {
      $empleado = DB::table("empleados as e")
        ->leftJoin("puestos as p", "p.id", "e.puesto_id")
        ->select(
          "e.*",
          "p.nombre as puesto_nombre"
        )
        ->where("e.id", $id)
        ->first();
      return Status::Success("empleado", $empleado);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los datos del empleado");
    }
  }

  /**
   * Activa o desactiva el empleado ingresado
   */
  public function ActivarDesactivar(Request $request)
  {
    try
    {
      //Cambiar estado
      DB::beginTransaction();
      $empleado = Empleado::findOrFail($request->id);
      $empleado->condicion = $request->condicion;
      Auditoria::AuditarCambios($empleado);
      $empleado->update();
      if ($request->condicion == 0)
      {
        // Registrar en el historial

        // Contrato
        $contrato = DB::table("contratos as c")
          ->where("empleado_id", $empleado->id)
          ->orderBy("id", "desc")
          ->first();
        if ($contrato == null)
        {
          $proyecto = "N/D";
          $fecha_alta = "0001-01-01";
          $fecha_baja = $request->fecha_baja;
          $puesto = "N/D";
          $salario_neto = 0;
        }
        else
        {
          $proyecto = Proyecto::find($contrato->proyecto_id)->nombre_corto;
          $fecha_alta = $contrato->fecha_ingreso;
          $fecha_baja = $request->fecha_baja;
          $puesto_aux = Puesto::find($contrato->puesto_id);
          $puesto = $puesto_aux == null ? "N/D" : $puesto_aux->nombre;
          $salario_aux = Sueldo::where("contrato_id", $contrato->id)
            ->orderby("fecha_act", "desc")->first();
          $salario_neto = $salario_aux == null ? 0 : $salario_aux->sueldo_diario_real;
        }
        $registro = new Historial();
        $registro->empleado_id = $empleado->id;
        $registro->nombre = "$empleado->nombre $empleado->ap_paterno $empleado->ap_materno";
        $registro->curp = $empleado->curp;
        $registro->nss = $empleado->nss_imss;
        $registro->proyecto = $proyecto;
        $registro->fecha_alta = $fecha_alta;
        $registro->fecha_baja = $fecha_baja;
        $registro->puesto = $puesto;
        $registro->salario_neto = $salario_neto;
        $registro->empleado_registra_id = Auth::user()->empleado_id;
        $registro->save();
        // Desactivar usuario
        $user = User::where("empleado_id", $empleado->id)->first();
        if ($user != null)
        {
          // Borrar datos del usuario
          $user->email = "-";
          $user->empleado_id = 0;
          $user->navegador = "-";
          $user->password = "-";
          $user->remember_token = "-";
          $user->condicion = 0;
          $user->name = "-";
          $user->name_user = "-";
          $user->tipo_ubicacion_id = 0;
          // ELiminar correo del empleado
          DB::table("correos_corporativos")
            ->where("empleado_id", $empleado->id)
            ->delete();

          // Quitar permisos
          DB::table('permisos')
            ->where('user_id', $user->id)
            ->delete();
          Auditoria::AuditarCambios($user);
          $user->update();
        }
      }

      DB::commit();
      return Status::Success();
    }
    catch (Exception $e)
    {
      DB::rollBack();
      return Status::Error($e, "cambiar el empleado");
    }
  }

  /**
   * Descarga el formato de alta de los empleados
   */
  public function FormatoAlta($emp_id)
  {
    try
    {

      // Buscar el empleado
      $empleado = DB::table("empleados as e")
        ->leftJoin("estados_civiles as ec", "ec.id", "e.edo_civil_id")
        ->leftJoin("contacto_empleados as ce", "ce.empleado_id", "e.id")
        ->select(
          DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre"),
          "e.rfc",
          "e.nss_imss as nss",
          "e.curp",
          "ec.nombre as edo_civil",
          "ce.tel_celular",
          "e.lug_nac",
          "e.fech_nac",
          "e.fech_ing",
          "e.fech_alta_imss",
          "e.talla_overol",
          "e.talla_botas",
          "e.fiscal_cp",
          "e.fiscal_estado",
          "e.id_checador",
          "e.puesto_id",
          "e.salario_neto"
        )
        ->where("e.id", $emp_id)
        ->first();

      // Obtener empresa
      $empresa_id = $empleado->id_checador <= 2 ? 1 : 2; // 1. Conser 2.CSCT
      $datos_empresa = [];
      if ($empresa_id == 1) // Conser
      {
        $datos_empresa = [
          "nombre" => "CONSERFLOW S.A. DE C.V.",
          "direccion1" =>
          "Calle del Mezquite Lote 5 Manzana 3, Parque Industrial Tehuacán - Miahuatlán",
          "direccion2" => "Col. Santa Clara C.P. 75820 RFC CON1912026U2",
          "img" => "conserflow.png"
        ];
      }
      else // CSCT
      {
        $datos_empresa = [
          "nombre" => "CONSTRUCTORA Y SERVICIOS CALDERON TORRES S.A. DE C.V.",
          "direccion1" =>
          "Avenida Francisco I. Madero No. 1000 Colonia Maria De La Piedad",
          "direccion2" => "C.P. 96410 Coatzacoalcos, Veracruz RFC CSC050609LF7",
          "img" => "CSCT.png"
        ];
      }

      $empleado = (array)$empleado;
      // Datos de nacimiento
      $aux_fecha_nac = explode("-", $empleado["fech_nac"]);
      $fecha_nacimiento = "EL $aux_fecha_nac[2] DE " .  Utils::NombreMesNumero($aux_fecha_nac[1]) .
        " DEL " . $aux_fecha_nac[0];
      $empleado["fecha_nacimiento"] = $fecha_nacimiento;

      // Fecha de ingreso
      $fecha_ingreso_aux = explode("-", $empleado["fech_alta_imss"]);
      $fecha_ingreso = "$fecha_ingreso_aux[2] DE " .  Utils::NombreMesNumero($fecha_ingreso_aux[1]) .
        " DEL " . $fecha_ingreso_aux[0];
      $empleado["fecha_ingreso"] = $fecha_ingreso;

      // Direccion
      $aux_direccion = DB::table("direcciones_empleados as de")
        ->where("de.empleado_id", $emp_id)
        ->where("de.condicion",1)
        ->first();

      if ($aux_direccion == null)
      {
        $direccion = "N/D";
      }
      else
      {
        $direccion = "CALLE $aux_direccion->calle $aux_direccion->numero_interior " .
          "$aux_direccion->colonia C.P. $aux_direccion->codigo_postal " .
          "$aux_direccion->localidad, $aux_direccion->entidad_federativa";
      }
      $empleado["direccion"] = $direccion;

      // Puesto
      $puesto_aux = DB::table("puestos as p")
        ->leftJoin("departamentos as d", "d.id", "p.departamento_id")
        ->where("p.id", $empleado["puesto_id"])
        ->select(
          "p.nombre as puesto",
          "d.nombre as departamento"
        )
        ->first();
      $puesto = $puesto_aux->puesto;
      $departamento = $puesto_aux->departamento;

      $sueldo_neto = $empleado["salario_neto"];
      $empleado["fecha_ingreso"] = $fecha_ingreso;
      $empleado["puesto"] = $puesto;
      $empleado["departamento"] = $departamento;
      // Guardar salarios
      if (Utils::ObtenerTipoNomina($empleado["id_checador"]) == 1) // Semanal
      {
        $neto_semanal = $sueldo_neto;
        $neto_quincenal = "---";
      }
      else
      {
        $neto_semanal = "---";
        $neto_quincenal = $sueldo_neto;
      }

      $empleado["neto_semanal"] = $neto_semanal;
      $empleado["neto_quincenal"] = $neto_quincenal;

      // Beneficiario
      $aux_beneficiario = DB::table("beneficiarios as b")
        ->where("b.condicion",1)
        ->where("b.empleado_id", $emp_id)
        ->first();
      if ($aux_beneficiario == null)
      {
        $beneficiario_nombre = "N/D";
        $beneficiario_parentesco = "N/D";
        $beneficiario_telefono = "N/D";
      }
      else
      {
        $beneficiario_nombre = $aux_beneficiario->nombre;
        $beneficiario_parentesco = $aux_beneficiario->parentesco;
        $beneficiario_telefono = $aux_beneficiario->telefono;
      }
      $empleado["beneficiario_nombre"] = $beneficiario_nombre;
      $empleado["beneficiario_parentesco"] = $beneficiario_parentesco;
      $empleado["beneficiario_telefono"] = $beneficiario_telefono;

      // Datos bancarios
      $banco_aux = DB::table("datos_bancarios_empleados as dbe")
        ->leftJoin("catalogo_bancos as cb", "cb.id", "dbe.banco_id")
        ->where("dbe.empleado_id", $emp_id)
        ->where("dbe.condicion", 1)
        ->select(
          "cb.nombre as banco",
          "dbe.numero_cuenta",
          "dbe.clabe",
          "dbe.numero_tarjeta"
        )
        ->first();
      $banco = (array)$banco_aux;
      if ($banco_aux == null) // Sin banco
      {
        $banco = [
          "banco" => "N/D",
          "numero_cuenta" => "N/D",
          "clabe" => "N/D",
          "numero_tarjeta" => "N/D",
        ];
      }
      // Calcular edad
      $hoy = new DateTime(date("Y-m-d"));
      $fecha_nac = new DateTime($empleado["fech_nac"]);
      $edad = $hoy->diff($fecha_nac)->y;
      $empleado = array_merge($empleado, $banco);


      $pdf = Facade::loadView(
        "pdf.rh.altaempleado",
        compact("empleado", "edad", "datos_empresa")
      );
      $pdf->setPaper("letter", "portrait");
      $pdf->getDomPDF()->set_option("enable_php", true);
      return $pdf->stream($empleado["nombre"] . " - FORMATO DE ALTA.pdf");
      return Status::Success();
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return view("errors.500");
    }
  }

  // TODO: Almacen
  public function vertodosempleados()
  {
    $empleados = Empleado::select("empleados.*", "empleados.ubicacion_id", "tipo_ubicacion.nombre AS ubicacion")
      ->leftJoin("tipo_ubicacion", "empleados.ubicacion_id", "=", "tipo_ubicacion.id")
->where("empleados.condicion",1)
      // ->where("empleados.ubicacion_id", "=",$ubicacion)
      ->orderBy("id", "asc")->get();

    return response()->json($empleados);
  }

  /**
   * Obtiene los empleados activos y su puesto actual
   */
  public function EmpleadosPuesto()
  {
    try
    {
      // Obtener los empleados
      $empleados_aux = DB::table("empleados as e")
        ->select(
          "e.id",
          DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre")
        )
        ->where("e.condicion", 1)
        ->orderBy("nombre")
        ->get();

      $empleados = [];
      foreach ($empleados_aux as $e)
      {
        $contrato = DB::table("contratos as c")
          ->select(
            "c.id as contrato_id",
            "p.nombre as puesto",
            "p.id as puesto_id"
          )
          ->join("puestos as p", "p.id", "c.puesto_id")
          ->where("c.empleado_id", $e->id) //Empleado
          ->where("c.condicion", 1) // Activo
          ->orderBy("c.id", "desc")
          ->first();

        if ($contrato == null) continue; // Sin contrato: ignorar

        $empleados[] = array_merge((array)$e, (array)$contrato);
      }

      return Status::Success("empleados", $empleados);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los empleados");
    }
  }

  public function ReporteGenral()
  {
    try
    {
      ob_end_clean();
      ob_start();
      return Excel::download(new EmpleadosGeneralExport(), "Empleados General.xlsx");
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return view("errors.500");
    }
  }
}
