<?php

namespace App\Http\Controllers\Compras;

use \App\Http\Helpers\Utilidades;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\ComprasModels\Proveedor;
use App\BancoProveedor;
use App\ComprasModel\ProveedorCambio;
use App\Exports\Compras\ProveedoresPorAnioExport;
use App\Exports\ProveedoresReporteExport;
use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use Carbon\Carbon;
use Error;
use Exception;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ProveedoresController extends Controller
{
  /**
   * Obtener todos los proveedores registrados y su evaluación para catalogo de registro
   */
  public function ObtenerProveedores($anio)
  {
    try
    {
      // Obtener proveedores activos, que se les ha comprado
      $proveedores_aux = DB::table("ordenes_compras as oc")
        ->join("proveedores as p", "p.id", "oc.proveedore_id")
        ->whereYear("oc.fecha_orden", $anio)
        ->where("p.condicion", 1)
        ->select(
          "p.id",
          "p.id as idControl",
          "p.nombre",
          "p.razon_social",
          "p.giro",
          "p.rfc",
          "p.estado",
          "p.pagina",
          "p.calle",
          "p.nacionalidad",
          "p.no_exterior",
          "p.no_interior",
          "p.cp",
          "p.colonia",
          "p.municipio",
          "p.ventas_contacto",
          "p.ventas_telefono",
          "p.ventas_celular",
          "p.ventas_correo",
          "p.facturacion_contacto",
          "p.facturacion_telefono",
          "p.facturacion_celular",
          "p.facturacion_correo",
          "p.condicion",
          "p.regimen_fiscal",
          "p.limite_credito"
        )
        ->orderBy("p.nombre")
        ->distinct()
        ->get();

      $proveedores = [];
      // Obtener la evaluación del proveedor en el año ingresado
      foreach ($proveedores_aux as $p)
      {
        $evaluacion = DB::table("evaluacion_provee as ep")
          ->where("ep.proveedor_id", $p->id)
          ->whereRaw("year(fecha)=?", [$anio])
          ->select(
            "ep.id as ep_id",
            DB::raw("(
            ep.uno+ep.dos+ep.tres+ep.cuatro+ep.cinco+ep.seis+ep.siete+
            ep.ocho+ep.nueve+ep.diez+ep.once+ep.doce+ep.trece+ep.catorce+ep.quince+
            ep.diesiseis+ep.diesisiete+ep.diesiocho
            ) as total_evaluacion")
          )
          ->first();

        // Obtener bancos
        $bancos = BancoProveedor::where("proveedor_id", $p->id)->get();
        $aux_p = (array)$p;
        $aux_p["bancos"] = $bancos;

        // Unir proveedor y evaluacion
        $proveedores[] = array_merge($aux_p, (array)$evaluacion);
      }
      return Status::Success("proveedores", $proveedores);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los proveedores");
    }
  }

  /**
   * Obtiene los datos de los proveedores activos para los catalogos fuera de compras
   */
  public function ProveedoresActivos()
  {
    try
    {
      $proveedores = Proveedor::select(
        "id",
        "nombre",
        "razon_social"
      )
        ->where("condicion", 1)
        ->orderBy("nombre")
        ->get();
      return Status::Success("proveedores", $proveedores);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los proveedores");
    }
  }

  /**
   * Obtener todos los proveedores (activos e inactivos) para OC
   */
  public function ObtenerTodos()
  {
    try
    {
      $proveedores = Proveedor::select(
        "id",
        "nombre",
        "razon_social"
      )
        ->orderBy("nombre")
        ->get();
      return Status::Success("proveedores", $proveedores);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los proveedores");
    }
  }

  /**
   * Registra o actualiza el proveedor ingresado
   */
  public function store(Request $request)
  {
    try
    {
      if (!$request->ajax()) return redirect("/");

      $datos = LimpiarInput::LimpiarCampos(
        $request->all(),
        [
          "nombre", "razon_social", "giro", "limite_credito", "estado", "pagina",
          "calle", "no_exterior", "no_interior", "cp", "colonia", "municipio",
          "ventas_contacto", "ventas_telefono", "ventas_celular", "ventas_correo",
          "facturacion_contacto", "facturacion_telefono", "facturacion_celular",
          "facturacion_correo", "nacionalidad"
        ]
      );

      $tipo_movimiento = "";
      $proveedor_modificado = false; // Indica si el proveedor fue modificado
      DB::beginTransaction();
      if ($request->id == null) // Nuevo
      {
        $tipo_movimiento = "ALTA";
        $modificacion = "-";
        $anexos = "-";
        // Comprobar que no exista RFC
        $rfc_existe = Proveedor::where("rfc", $request->rfc)->first();
        // if ($rfc_existe != null)
        //   return Status::Error2("El proveedor ya está registrado");
        // Comprobar que el proveedor no sea empleado
        $es_empleado = DB::table("empleados as e")->where("rfc", $request->rfc)->first();

        if ($es_empleado != null)
          return Status::Error2("El Proveedor no puede ser un Empleado");

        // Registrar
        $proveedor = new Proveedor();
        $proveedor->fill($datos);
        $proveedor->fecha_alta = date("Y-m-d");
        $proveedor->empleado_registra_id = Auth::user()->empleado_id;
        // Datos anteriores
        $proveedor->direccion = $request->calle . " " . $request->colonia . " " . $request->municipio
          . " " . $request->estado;
        $proveedor->contacto = $request->ventas_contacto;
        $proveedor->telefono = $request->ventas_telefono;
        $proveedor->correo = $request->ventas_correo;

        // Ya no son requeridos
        $proveedor->referencia1_empresa = "N/A";
        $proveedor->referencia1_contacto = "N/A";
        $proveedor->referencia1_telefono = "N/A";
        $proveedor->referencia1_relacion = "N/A";
        $proveedor->referencia1_comentarios = "N/A";
        $proveedor->referencia2_empresa = "N/A";
        $proveedor->referencia2_contacto = "N/A";
        $proveedor->referencia2_telefono = "N/A";
        $proveedor->referencia2_relacion = "N/A";
        $proveedor->referencia2_comentarios = "N/A";
        $proveedor->dia_credito = 0;

        $proveedor->save();
        Auditoria::AuditarCambios($proveedor);

        // Crear Banco inicial
        $banco = new BancoProveedor();
        $banco->banco = $request->temp2_proveedor_banco;
        $banco->cuenta = $request->temp2_proveedor_cuenta;
        $banco->clabe = $request->temp2_proveedor_clabe;
        $banco->proveedor_id = $proveedor->id;
        $banco->condiciones = $request->temp2_proveedor_condiciones;
        $banco->moneda = $request->temp2_proveedor_moneda;
        $banco->save();
        Auditoria::AuditarCambios($banco);
        $proveedor_modificado = true;
      }
      else // actu
      {
        $tipo_movimiento = "MODIFICACION";
        $modificacion = $request->modificacion;
        $anexos = $request->anexos;
        $proveedor = Proveedor::find($request->id);
        $proveedor->fill($datos);
        // Datos anteriores
        $proveedor->direccion = $request->calle . " " . $request->colonia . " " . $request->municipio
          . " " . $request->estado;
        $proveedor->contacto = $request->ventas_contacto;
        $proveedor->telefono = $request->ventas_telefono;
        $proveedor->correo = $request->ventas_correo;
        $proveedor_modificado = Auditoria::AuditarCambios($proveedor);
        $proveedor->update();
      }


      // guardar bancos
      if ($request->lista_bancos != null)
      {
        $aux_bancos_list = json_decode($request->lista_bancos, true);
        $banco_modificado = count($aux_bancos_list) > 0; // Se agregó un banco nuevo
        foreach ($aux_bancos_list as $banco)
        {
          if (!isset($banco["temp"])) continue;

          if ($banco["temp"])
          {
            $nuevo = new BancoProveedor();
            $nuevo->banco = $banco["banco"];
            $nuevo->cuenta = $banco["cuenta"];
            $nuevo->clabe = $banco["clabe"];
            $nuevo->condiciones = $banco["condiciones"];
            $nuevo->moneda = $banco["moneda"];
            $nuevo->proveedor_id = $proveedor->id;
            $nuevo->save();
            Auditoria::AuditarCambios($nuevo);
          }
        }
      }

      if ($proveedor_modificado || $banco_modificado)
      {
        /** Guardar historial de cambios **/
        $cambios = $this->ObtenerCambios($proveedor->id);
        
        if ($cambios["status"]) // Ok
        {
          // Guardar en el historial
          $proveedorCambio = new ProveedorCambio();
          $proveedorCambio->proveedor_id = $proveedor->id;
          $proveedorCambio->tipo_movimiento = $tipo_movimiento;
          $proveedorCambio->fecha = date("Y-m-d");
          $proveedorCambio->modificacion = $modificacion;
          $proveedorCambio->anexos = $anexos;
          $proveedorCambio->empleado_registro_id = Auth::user()->empleado_id;
          $proveedorCambio->campos_cambios = $cambios["data"];
          $proveedorCambio->save();
          Auditoria::AuditarCambios($proveedorCambio);
        }
        else // Error.
        {
          DB::rollBack(); // No guardar nada
          return Status::Error2("Error al guardar el historial del proveedor");
        }
      }
      DB::commit();
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "registrar el proveedor");
    }
  }

  /**
   * Obtener los cambios del proveedor ingresado
   * @param $id int Id del Proveedor 
   */
  public function ObtenerCambios($id)
  {
    try
    {
      // Obtener proveedor original
      $proveedor = (array)DB::table("proveedores")->find($id);
      // Obtener ultimo banco registrado
      /*$banco = (array) DB::table("bancos_proveedores")->where("proveedor_id", $id)
        ->orderBy("id", "desc")->first();*/
       $bancos = DB::table("bancos_proveedores")->where("proveedor_id", $id)
            ->orderBy("id", "desc")
            ->take(2)
            ->get();
           
        $bancoVacio = [
                "banco_id" => 0,
                "banco" => "N/D",
                "cuenta" => "N/D",
                "clabe" => "N/D",
                "proveedor_id" => $id,
                "condiciones" => "N/D",
                "moneda" => "N/D"
            ];

        
      $banco1 = (array)($bancos->get(0) ?? (object) $bancoVacio);
      $banco2 = (array)($bancos->get(1) ?? (object) $bancoVacio);
      
      $campos = ['banco_id','banco', 'cuenta', 'clabe','proveedor_id', 'condiciones', 'moneda'];
      
      $bancoFinal = array_intersect_key($banco1, array_flip($campos));
      
      foreach ($campos as $campo) {
        $bancoFinal[$campo . '2'] = $banco2[$campo] ?? 'N/D';
      }
     
      
      $data = array_merge($proveedor, $bancoFinal);
      $json = json_encode($data);

      return ["status" => true, "data" => $json];
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return ["status" => false, "data" => ""];
    }
  }

  /**
   * Activa o desactiva el proveedor ingresado
   */
  public function Desactivar(Request $request)
  {
    try
    {
      $proveedor = Proveedor::find($request->id);
      $proveedor->condicion = $request->condicion;
      Auditoria::AuditarCambios($proveedor);
      $proveedor->update();
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "modicicar el proveedor");
    }
  }

  /**
   * Obtiene los detalles del proveedor buscado
   */
  public function show($id)
  {
    try
    {
      $proveedor = Proveedor::find($id);
      return response()->json($proveedor);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los detalles del proveedore");
    }
  }

  /**
   * Filtrar y buscar en table-server
   */
  protected function busqueda_filterByColumn($data, $queries)
  {
    $queries = json_decode($queries, true);

    return $data->where(function ($q) use ($queries)
    {
      foreach ($queries as $field => $query)
      {
        $_field = $field;

        if (is_string($query))
        {
          $q->where($_field, "LIKE", "%{$query}%");
        }
        else
        {
          $start = Carbon::createFromFormat("Y-m-d", substr($query["start"], 0, 10))->startOfDay();
          $end = Carbon::createFromFormat("Y-m-d", substr($query["end"], 0, 10))->endOfDay();

          $q->whereBetween($_field, [$start, $end]);
        }
      }
    });
  }

  /**
   * Filtrar y buscar en table-server
   */
  protected function busqueda_filter($data, $query, $fields)
  {
    return $data->where(function ($q) use ($query, $fields)
    {
      foreach ($fields as $index => $field)
      {
        $method = $index ? "orWhere" : "where";
        $q->{$method}($field, "LIKE", "%{$query}%");
      }
    });
  }

  /**
   * Filtrar y buscar en table-server
   */
  public function servertable()
  {
    extract(request()->only(["query", "limit", "page", "orderBy", "ascending", "byColumn"]));

    $data = $this->Proveedor1();

    if (isset($query) && $query)
    {
      $data = $byColumn == 1 ?
        $this->busqueda_filterByColumn($data, $query) :
        $this->busqueda_filter($data, $query, $fields);
    }

    $count = $data->count();

    $data->limit($limit)
      ->skip($limit * ($page - 1));

    if (isset($orderBy))
    {
      $direction = $ascending == 1 ? "ASC" : "DESC";
      $data->orderBy($orderBy, $direction);
    }
    // leftJoin("tipo_calidad AS TC","TC.id","=","articulos.calidad_id")
    // ->
    $results = $data->get();

    return [
      "data" => $results,
      "count" => $count,
    ];
  }

  /**
   * Obtiene los datos bancarios del proveedor
   */
  public function getDataBankProveedor($id)
  {
    try
    {
      $datos_bancarios = DB::table("bancos_proveedores AS bp")
        ->where("proveedor_id", $id)->get();

      return response()->json($datos_bancarios);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener la información del proveedor");
    }
  }

  /**
   * Genera el reporte de los proveedores en excel
   */
  public function DescargarReporte($anio)
  {
    ob_end_clean(); // this
    ob_start(); // and this
    return Excel::download(new ProveedoresReporteExport($anio), "PCO-02_F-01 Catálogo de proveedores.xlsx");
  }

  /**
   * Cambia a mayúsculas los campos ingresados
   */
  public function CambioMayus($proveedor)
  {
    $proveedor["nombre"] = $this->QuitarAcentos($proveedor["nombre"]);
    $proveedor["razon_social"] = $this->QuitarAcentos($proveedor["razon_social"]);
    $proveedor["direccion"] = $this->QuitarAcentos($proveedor["direccion"]);
    $proveedor["giro"] = $this->QuitarAcentos($proveedor["giro"]);
    $proveedor["contacto"] = $this->QuitarAcentos($proveedor["contacto"]);
    $proveedor["descripcion"] = $this->QuitarAcentos($proveedor["descripcion"]);
    //    $proveedor["regimen_fiscal"] = $this->QuitarAcentos($proveedor["regimen_fiscal"]);
    return $proveedor;
  }

  /**
   * Elimina los acentos del campo ingresado
   */
  public function QuitarAcentos($cadena)
  {
    $no_permitidas = [
      "á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "À", "Ã", "Ì",
      "Ò", "Ù", "Ã™", "Ã ", "Ã¨", "Ã¬", "Ã²", "Ã¹", "ç", "Ç", "Ã¢", "ê", "Ã®",
      "Ã´", "Ã»", "Ã‚", "ÃŠ", "ÃŽ", "Ã”", "Ã›", "ü", "Ã¶", "Ã–", "Ã¯", "Ã¤", "«",
      "Ò", "Ã", "Ã„", "Ã‹"
    ];
    $permitidas = [
      "a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N",
      "A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "c", "C", "a", "e", "i",
      "o", "u", "A", "E", "I", "O", "U", "u", "o", "O", "i", "a", "e", "U", "I", "A", "E"
    ];
    $texto = strtoupper(str_replace($no_permitidas, $permitidas, $cadena));
    return $texto;
  }

  /**
   * Descarga el comprobante fiscal del proveedor
   */
  public function DescagarComFiscal($id)
  {
    try
    {
      $proveedor = Proveedor::find($id);
      $ruta_documento = "Archivos/Proveedores/Edo_fiscal/" . $proveedor->doc_fiscal;

      $path = storage_path("app/" . $ruta_documento);
      if (file_exists($path) && $proveedor->doc_fiscal != null)
      {
        $aux = explode("_", $proveedor->certificado_pdf);
        $nombre = $aux[count($aux) - 1];

        return response()->download($path, $nombre, [
          "Content-Type" => "application/pdf",
          "Content-Disposition' => 'inline; filename='" . $nombre . "'"
        ]);
      }
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return view("errors.500");
    }
  }

  /**
   * Obtener las ordenes de compra por año
   */
  public function OCPorAnio($anio)
  {
    try
    {
      ob_end_clean();
      ob_start();
      return Excel::download(
        new ProveedoresPorAnioExport($anio),
        "ComprasProveedores_$anio.xlsx"
      );
    }
    catch (Exception $e)
    {
      dd($e);
    }
  }

  /**
   * Reporte temporal de catálogo de proveedores
   */
  function DescargarReporte2()
  {
    try
    {
      $nombre = "Catálogo de Proveedores.xlsx";
      $ruta_documento = "Compras/$nombre";

      $path = storage_path("app/" . $ruta_documento);
      if (file_exists($path))
      {
        ob_end_clean();
        ob_start();
        return response()->download($path, $nombre, [
          "Content-Type" => "application/vnd.ms-excel",
          "Content-Disposition' => 'inline; filename='" . $nombre . "'"
        ]);
      }
      return view("errors.404");
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return view("errors.500");
    }
  }
}
