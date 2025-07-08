<?php

namespace App\Http\Controllers;

use App\BeneficiarioViatico;
use App\DetalleViatico;
use App\DetalleViaticoCSCT;
use App\Http\Helpers\Utilidades;
use App\PersonalDetalles;
use App\PersonalDetallesDBP;
use App\Proyecto;
use App\Repositories\Viaticos;
use App\SolicitudViaticos;
use App\VehiculosItinerarioViaticos;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SolicitudViaticosController extends Controller
{

  protected $viaticos;

  public function __construct(Viaticos $viatico)
  {
    $this->viaticos = $viatico;
  }

  public function unique_multidim_array($array)
  {
    try
    {
      $temp_array = array();
      $i = 0;
      $key_array = array();
      foreach ($array as $val)
      {
        if (!in_array($val->id, $key_array))
        {
          $key_array[$i] = $val->id;
          $temp_array[$i] = $val;
        }
        $i++;
      }
      return $temp_array;
    }
    catch (\Throwable $e)
    {
      dd($e);
    }
  }

  public function ObtenerViaticosConser()
  {
    $id_emp = Auth::user()->empleado_id;
    $query = DB::table('solicitud_viaticos as sv')
      ->select(
        "sv.id as sv__id",
        "sv.fecha_solicitud as sv__fecha_solicitud",
        "sv.fecha_pago as sv__fecha_pago",
        "sv.folio as sv__folio",
        "sv.proyecto_id as sv__proyecto_id",
        "sv.origen_destino as sv__origen_destino",
        "sv.fecha_salida as sv__fecha_salida",
        "sv.hora_estimada_salida as sv__hora_estimada_salida",
        "sv.fecha_operacion as sv__fecha_operacion",
        "sv.fecha_retorno as sv__fecha_retorno",
        "sv.empleado_elabora_id as sv__empleado_elabora_id",
        "sv.empleado_revisa_id as sv__empleado_revisa_id",
        "sv.empleado_autoriza_id as sv__empleado_autoriza_id",
        "sv.total_personas as sv__total_personas",
        "sv.empleado_supervisor_id as sv__empleado_supervisor_id",
        "sv.estado as sv__estado",
        "sv.empleado_user_id as sv__empleado_user_id",
        "sv.tipo as sv__tipo",
        "sv.origen_destino_destino as sv__origen_destino_destino",
        "sv.eliminado as sv__eliminado",
        "sv.timbrado as sv__timbrado",
        "sv.total_efectivo as sv__total_efectivo",
        "sv.total_transferencia as sv__total_transferencia",
        'P.nombre_corto as nombre_proyecto',
        DB::raw("CONCAT_WS(' ',benef.nombre,benef.ap_paterno,benef.ap_materno) AS benef__nombre"),
        DB::raw("CONCAT_WS(' ', EA.nombre, EA.ap_paterno, EA.ap_materno) AS EA__nombre_autoriza"),
        DB::raw("CONCAT_WS(' ', EE.nombre, EE.ap_paterno, EE.ap_materno) AS EE__nombre"),
        DB::raw("CONCAT_WS(' ', ER.nombre, ER.ap_paterno, ER.ap_materno) AS ER__nombre_revisa"),
        DB::raw("CONCAT_WS(' ', ES.nombre, ES.ap_paterno, ES.ap_materno) AS ES__nombre_supervisor"),
      )
      ->leftJoin('permisos_viaticos as pv', 'pv.empleado_permitido_id', '=', DB::raw($id_emp))
      ->leftJoin('empleados as EE', 'EE.id', '=', 'sv.empleado_elabora_id')
      ->leftJoin('empleados as EA', 'EA.id', '=', 'sv.empleado_autoriza_id')
      ->leftJoin('empleados as ER', 'ER.id', '=', 'sv.empleado_revisa_id')
      ->leftJoin('empleados as ES', 'ES.id', '=', 'sv.empleado_supervisor_id')
      ->join('proyectos as P', 'P.id', '=', 'sv.proyecto_id')
      ->leftJoin('beneficiario_viatico as bv', 'bv.solicitud_viaticos_id', '=', 'sv.id')
      ->leftJoin('empleados as benef', 'bv.empleado_beneficiario_id', '=', 'benef.id')
      ->where(function ($query) use ($id_emp)
      {
        $query->where('sv.empleado_user_id', '=', $id_emp)
          ->orWhere('sv.empleado_user_id', '=', DB::raw('pv.propietario_id'))
          ->orWhere('bv.empleado_beneficiario_id', '=', $id_emp);
      })
      ->distinct();
    return $query;
  }

  public function ObtenerViaticosConserById($id)
  {
    $db = Config::get("database.connections.mysql.database");
    $tabla_viaticos = $db . ".solicitud_viaticos sv ";
    $query = "SELECT sv.*, P.nombre_corto as nombre_proyecto,
    CONCAT_WS(' ',EA.nombre,EA.ap_paterno,EA.ap_materno) AS nombre_autoriza,
    CONCAT_WS(' ',EE.nombre,EE.ap_paterno,EE.ap_materno) AS nombre_elabora,
    CONCAT_WS(' ',ER.nombre,ER.ap_paterno,ER.ap_materno) AS nombre_revisa,
    CONCAT_WS(' ',ES.nombre,ES.ap_paterno,ES.ap_materno) AS nombre_supervisor
    from " . $tabla_viaticos . "
    left join empleados as EE on EE.id = sv.empleado_elabora_id
    left join empleados as EA on EA.id = sv.empleado_autoriza_id
    left join empleados as ER on ER.id = sv.empleado_revisa_id
    left join empleados as ES on ES.id = sv.empleado_supervisor_id
    join proyectos as P on P.id = sv.proyecto_id
    left join beneficiario_viatico as bv on bv.solicitud_viaticos_id=sv.id
    where sv.id = $id
    ORDER BY sv.fecha_solicitud DESC";
    $solicitudes = DB::select($query);
    $d = $this->unique_multidim_array($solicitudes);
    return $d;
  }

  /**
   * [index Busqueda especifica de las partidas correspondientes ]
   * @return Response           [description]
   */
  public function getConserflow($empresa)
  {
    try
    {
      extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn']));

      $data = $this->ObtenerViaticosConser();
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
        $direction = $ascending == 1 ? 'ASC' : 'DESC';
        $data->orderBy($orderBy, $direction);
      }
      $results = $data->get();

      return [
        'data' => $results,
        'count' => $count,
      ];
    }
    catch (\Throwable $e)
    {
      return Status::Error($e, "obtener las solicitudes");
    }
  }

  protected function busqueda_filterByColumn($data, $queries)
  {
    $queries = json_decode($queries, true);

    foreach ($queries as $field => $query)
    {
      $_field = str_replace("__", ".", $field);
      $data->where($_field, 'LIKE', "%{$query}%");
    }
    return $data;
  }

  protected function busqueda_filter($data, $query, $fields)
  {
    return $data->where(function ($q) use ($query, $fields)
    {
      foreach ($fields as $index => $field)
      {
        $method = $index ? 'orWhere' : 'where';
        $q->{$method}($field, 'LIKE', "%{$query}%");
      }
    });
  }

  public function detalles($id)
  {
    $solicitud = $this->ObtenerViaticosConserById($id);
    $detalles_viaticos = $this->viaticos->DetalleViatico($id);
    $detalles_listado = DetalleViatico::where('solicitud_viaticos_id', '=', $id)
      ->select('detalle_viaticos.*')->get();

    $beneficiario_viaticos = $this->viaticos->BeneficiarioViatico($id);
    $vehiculos_viaticos = $vehiculos_viaticos = $this->viaticos->VehiculosItinerarioViaticos($id);
    $personal_servicio_viaticos = $this->viaticos->PersonalDetalles($id);
    $comprobacion = DB::table('viaticos')->select(DB::raw("SUM(gastos_comprobados_deducibles) AS total"))
      ->where('solicitud_viaticos_id', $id)->first();

    if (count($beneficiario_viaticos) == 0)
    {
      $nombre_beneficiario = "";
    }
    else
    {
      $aux_benefic = $beneficiario_viaticos[0];
      if ($aux_benefic->empleado_beneficiario_id == 0) // Sindicato
        $nombre_beneficiario = $aux_benefic->beneficiario_externo;
      else
        $nombre_beneficiario = $aux_benefic->nombre_beneficiario;
    }

    $solicitud_viaticos_respuesta[] = [
      'solicitud' => $solicitud,
      'detalle' => $detalles_viaticos,
      'detalles_listado' => $detalles_listado,
      'beneficiarios' => $beneficiario_viaticos,
      "nombre_beneficiario" => $nombre_beneficiario,
      'vehiculo' => $vehiculos_viaticos,
      'empleados' => $personal_servicio_viaticos,
      'comprobacion' => $comprobacion,
    ];
    return response()->json($solicitud_viaticos_respuesta);
  }

  /**
   * Crear folio de la solicitud
   */
  public static function crearFolioViaticos($proyecto_id)
  {
    try
    {

      $folio = "";
      $numero = 0;
      $proyecto = Proyecto::find($proyecto_id);
      $numero_solicitudes = SolicitudViaticos::where('proyecto_id', $proyecto_id)
        ->count();
      $numero = $numero_solicitudes + 1; // Aumentar uno

      $folio = strtoupper($proyecto->nombre_corto) . '-' . str_pad($numero, 3, "0", STR_PAD_LEFT);
      return $folio;
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
    }
  }

  /**
   * Crea un registro de solicitud de Viaticos
   */
  public function store(Request $request)
  {
    try
    {
      DB::beginTransaction();
      $user = Auth::user();

      $solicitud_viaticos = new SolicitudViaticos();
      $folio  = $this->crearFolioViaticos($request->proyecto_id);
      $solicitud_viaticos->fill($request->all());
      $solicitud_viaticos->folio = $folio; // folio
      $solicitud_viaticos->empleado_user_id = $user->empleado_id; // Empleado registra
      $solicitud_viaticos->empresa = $request->empresa;
      $solicitud_viaticos->total_efectivo = 0;
      $solicitud_viaticos->total_transferencia = 0;
      $solicitud_viaticos->save();
      Auditoria::AuditarCambios($solicitud_viaticos);

      // Guardar beneficiario
      $this->llenarBeneficiarios($solicitud_viaticos, $request->beneficiario_uno[0]);

      $detalles = $request->detalles_listado;
      // Detalles
      $total_efectivo = 0;
      $total_transferencia = 0;
      for ($i = 0; $i < count($detalles['efectivo']); $i++)
      {
        $this->llenardetallesViaticos(
          $solicitud_viaticos,
          $request->detalles_listado['efectivo'][$i],
          $request->detalles_listado['tranferencia'][$i],
          $request->detalles_listado['conceptos'][$i],
          $request->tipo
        );
        $total_efectivo += $request->detalles_listado['efectivo'][$i];
        $total_transferencia += $request->detalles_listado['tranferencia'][$i];
      }

      $solicitud_viaticos->total_efectivo = $total_efectivo;
      $solicitud_viaticos->total_transferencia = $total_transferencia;
      $solicitud_viaticos->update();

      // Unidad
      if (count($request->vehiculos_itinerario_viaticos) != 0)
      {
        for ($i = 0; $i < count($request->vehiculos_itinerario_viaticos); $i++)
        {
          $this->llenarVehiculosItinerarioViaticos(
            $solicitud_viaticos->id,
            $request->vehiculos_itinerario_viaticos[$i]['unidad'],
            $request->vehiculos_itinerario_viaticos[$i]['km_inicial'],
            $request->vehiculos_itinerario_viaticos[$i]['empleado_operador_id']
          );
        }
      }

      // Personal
      if (count($request->personal_servicio_viaticos_id) != 0)
      {
        for ($i = 0; $i < count($request->personal_servicio_viaticos_id); $i++)
        {
          $this->llenarpersonalServicioViaticos(
            $solicitud_viaticos->id,
            $request->personal_servicio_viaticos_id[$i]['id']
          );
        }
      }

      DB::commit();
      return Status::Success();
    }
    catch (Exception $e)
    {
      DB::rollBack();
      return Status::Error($e, "guardar la solicitud");
    }
  }

  /**
   * [update actualiza los registro del modelo SolicitudViaticos ]
   * @param Request
   * @param Int
   * @return Response           [description]
   */
  public function update(Request $request, $id)
  {
    try
    {
      $solicitud_viaticos = SolicitudViaticos::findOrFail($request->id);
      $solicitud_viaticos->fill($request->all());
      $solicitud_viaticos->empresa = $request->empresa;
      $solicitud_viaticos->save();
      //se vacian los registros existentes para poder agregar los nuevos no se busca ni actualiza por el tamanio de la consulta
      BeneficiarioViatico::where([['solicitud_viaticos_id', '=',  $solicitud_viaticos->id]])->delete();
      DetalleViatico::where([['solicitud_viaticos_id', '=',  $solicitud_viaticos->id]])->delete();
      VehiculosItinerarioViaticos::where([['solicitud_viaticos_id', '=',  $solicitud_viaticos->id],])->delete();
      PersonalDetalles::where([['solicitud_viaticos_id', '=',  $solicitud_viaticos->id]])->delete();

      $this->llenarBeneficiarios($solicitud_viaticos, $request->beneficiario_uno[0]);

      if ($request->detalles_listado != null)
      {
        $total_efectivo = 0;
        $total_transferencia = 0;
        $detalles = $request->detalles_listado;
        for ($i = 0; $i < count($detalles['efectivo']); $i++)
        {
          // TODO:
          $this->llenardetallesViaticos(
            $solicitud_viaticos,
            $detalles['efectivo'][$i],
            $detalles['tranferencia'][$i],
            $detalles['conceptos'][$i],
            $request->tipo
          );
          $total_efectivo += $request->detalles_listado['efectivo'][$i];
          $total_transferencia += $request->detalles_listado['tranferencia'][$i];
        }
        $solicitud_viaticos->total_efectivo = $total_efectivo;
        $solicitud_viaticos->total_transferencia = $total_transferencia;
        $solicitud_viaticos->update();
      }

      if (count($request->vehiculos_itinerario_viaticos) != 0)
      {
        for ($i = 0; $i < count($request->vehiculos_itinerario_viaticos); $i++)
        {
          $this->llenarVehiculosItinerarioViaticos(
            $solicitud_viaticos->id,
            $request->vehiculos_itinerario_viaticos[$i]['unidad'],
            $request->vehiculos_itinerario_viaticos[$i]['km_inicial'],
            $request->vehiculos_itinerario_viaticos[$i]['empleado_operador_id']
          );
        }
      }

      if (count($request->personal_servicio_viaticos_id) != 0)
      {
        for ($i = 0; $i < count($request->personal_servicio_viaticos_id); $i++)
        {
          $this->llenarpersonalServicioViaticos(
            $solicitud_viaticos->id,
            $request->personal_servicio_viaticos_id[$i]['id']
          );
        }
      }

      return response()->json(
        array('status' => true)
      );
    }
    catch (Exception $e)
    {
      return Status::Error($e, "actualizar la solicitud");
    }
  }

  /**
   *
   */
  public function llenarBeneficiarios($solicitud_viaticos, $request)
  {
    try
    {
      $beneficiario_viaticos = new BeneficiarioViatico();

      $beneficiario_viaticos->solicitud_viaticos_id = $solicitud_viaticos->id; //
      if (gettype($request['id']) === 'array' || gettype($request['id']) === 'object')
      {
        //interno
        $beneficiario_viaticos->empleado_beneficiario_id = $request['id']['id']; //
        $beneficiario_viaticos->datos_bancarios_empleado_id = $request['dbemp_id']; ///pendiente
        $beneficiario_viaticos->banco_nombre = $request['banco']; //n
        $beneficiario_viaticos->tarjeta = $request['tarjeta']; //n
        $beneficiario_viaticos->clabe = $request['clave']; //n
        $beneficiario_viaticos->cuenta = $request['cuenta']; //n
        $beneficiario_viaticos->beneficiario_externo = "-"; //n
      }
      else
      {
        //externo
        $beneficiario_viaticos->empleado_beneficiario_id = 0; //
        $beneficiario_viaticos->beneficiario_externo = $request['id']; //n
        $beneficiario_viaticos->datos_bancarios_empleado_id = 0;
        $beneficiario_viaticos->banco_nombre = $request['dbemp_id']; //n
        $beneficiario_viaticos->tarjeta = $request['tarjeta']; //n
        $beneficiario_viaticos->clabe = $request['clave']; //n
        $beneficiario_viaticos->cuenta = $request['cuenta']; //n

      }
      $beneficiario_viaticos->save();
      return true;
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      dd($e);
    }
  }

  /**
   * @param Object
   * @param Int
   * @param Int
   * @param Int
   * @return Status           [description]
   */
  public function llenardetallesViaticos($solicitud_viaticos, $efectivo, $transferencia, $i, $tipo)
  {
    try
    {
      $detalles_viaticos = new DetalleViatico();
      if ($tipo === '0' || $tipo == 0)
      {
        $detalles_viaticos->catalogo_conceptos_viaticos_id = 0;
        $detalles_viaticos->catalogo_concepto_viaticos = $i;
      }
      else
      {
        $detalles_viaticos->catalogo_conceptos_viaticos_id = $i;
        $detalles_viaticos->catalogo_concepto_viaticos = "N/D";
      }
      //catalogo_concepto_viaticos
      $detalles_viaticos->solicitud_viaticos_id = $solicitud_viaticos->id;
      $detalles_viaticos->transferencia_electronica = $transferencia;
      $detalles_viaticos->efectivo = $efectivo;
      $detalles_viaticos->total = ($efectivo + $transferencia);
      $detalles_viaticos->save();

      return true;
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      dd($e);
    }
  }

  /**
   * @param Object
   * @param Int
   * @param Int
   * @param Int
   * @return Status           [description]
   */
  public function llenardetallesViaticosCSCT($solicitud_viaticos, $efectivo, $transferencia, $i, $tipo)
  {
    try
    {
      if ($tipo === '0' || $tipo == 0)
      {
        $detalles_viaticos = new DetalleViaticoCSCT();
        $detalles_viaticos->solicitud_viaticos_id = $solicitud_viaticos->id;
        $detalles_viaticos->catalogo_conceptos_viaticos_id = 0;
        $detalles_viaticos->transferencia_electronica = $transferencia;
        $detalles_viaticos->efectivo = $efectivo;
        $detalles_viaticos->total = ($efectivo + $transferencia);
        $detalles_viaticos->catalogo_concepto_viaticos = $i;
        $detalles_viaticos->save();
      }
      else
      {
        $detalles_viaticos = new DetalleViaticoCSCT();
        $detalles_viaticos->solicitud_viaticos_id = $solicitud_viaticos->id;
        $detalles_viaticos->catalogo_conceptos_viaticos_id = $i;
        $detalles_viaticos->transferencia_electronica = $transferencia;
        $detalles_viaticos->efectivo = $efectivo;
        $detalles_viaticos->total = ($efectivo + $transferencia);
        Utilidades::auditar($detalles_viaticos, $detalles_viaticos->id);
        $detalles_viaticos->save();
      }
      return true;
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  /**
   * @param Object
   * @param Int
   * @param Int
   * @param Int
   * @return Status           [description]
   */
  public function llenarVehiculosItinerarioViaticos($solicitud_viaticos_id, $unidad, $km_inicial, $empleado_operador_id)
  {
    try
    {
      $vehiculos_itinerario_viaticos = new VehiculosItinerarioViaticos();
      $vehiculos_itinerario_viaticos->solicitud_viaticos_id  = $solicitud_viaticos_id;
      $vehiculos_itinerario_viaticos->unidad = $unidad;
      $vehiculos_itinerario_viaticos->km_inicial = $km_inicial;
      $vehiculos_itinerario_viaticos->empleado_operador_id = $empleado_operador_id;
      $vehiculos_itinerario_viaticos->save();
      return true;
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      dd($e);
    }
  }

  /**
   * @param Object
   * @param Int
   * @return Status           [description]
   */
  public function llenarpersonalServicioViaticos($solicitud_viaticos_id, $empleado_id)
  {
    try
    {
      $personal_servicio_viaticos = new PersonalDetalles();
      $personal_servicio_viaticos->solicitud_viaticos_id = $solicitud_viaticos_id;
      $personal_servicio_viaticos->empleado_id = $empleado_id;
      $personal_servicio_viaticos->save();
      return true;
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      dd($e);
    }
  }

  /**
   * @param Object
   * @param Int
   * @return Status           [description]
   */
  public function llenarpersonalServicioViaticosCSCT($solicitud_viaticos_id, $empleado_id)
  {
    $personal_servicio_viaticos = new PersonalDetallesDBP();
    $personal_servicio_viaticos->solicitud_viaticos_id = $solicitud_viaticos_id;
    $personal_servicio_viaticos->empleado_id = $this->empleadoCSCT($empleado_id);
    $personal_servicio_viaticos->save();
    return true;
  }

  /**0.4242 = 25...
   * [estados actuaciza el campo estado del modelo SolicitudViaticos dependiendo del estado ]
   * @param Request
   * @return Status           [description]
   */
  public function estados(Request $request)
  {
    try
    {
      if ($request->empresa == 1)
      {
        //enviar a revision la solicitud de viaticos
        if ($request->edo == 10)
        { //2 DESABILITADO
          $mensaje = 'Aviso de revisión de viaticos';
          $mensaje_adicional = '';
          $this->estadoGuardar($request->id, $request->edo);
          $this->enviarCorreoVia($request->id, $mensaje, $mensaje_adicional, $request->edo, '');
        }
        if ($request->edo == 3)
        { // DESABILITADO
          $mensaje = 'Aviso de autorización de viaticos';
          $mensaje_adicional = '';
          $this->estadoGuardar($request->id, $request->edo);
          $this->enviarCorreoVia($request->id, $mensaje, $mensaje_adicional, $request->edo, '');
        }
        if ($request->edo == 2)
        { //4 NUEVO
          $mensaje = 'Viaticos en revisión';
          $mensaje_adicional = '';
          $this->estadoGuardar($request->id, $request->edo);
          $correos_d = ['viaticos@conserflow.com'];
          for ($i = 0; $i < count($correos_d); $i++)
          {
            $this->enviarCorreoVia($request->id, $mensaje, $mensaje_adicional, $request->edo, 'control', $correos_d[$i]);
          }
        }
        if ($request->edo == 5)
        {
          $this->estadoGuardar($request->id, $request->edo);
        }
        if ($request->edo == 6)
        {
          $this->estadoGuardar($request->id, $request->edo);
        }
        //Solicitud rechazada
        if ($request->edo == 0)
        {
          $this->estadoGuardar($request->id, $request->edo);
          $mensaje = 'Aviso de rechazado de viaticos ';
          $mensaje_adicional = $request->motivo;
          $this->enviarCorreoVia($request->id, $mensaje, $mensaje_adicional, $request->edo, '');
        }
        return response()->json(array('status' => true));
      }
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  /**
   * [show Funcion que selecciona una solicitud de viaticos por id solicitado]
   * @param Int $id
   * @return Response json
   */
  public function show($id)
  {
    $solicitud_viaticos_respuesta = [];
    $solicitudes =  $this->viaticos->solicitud_consulta()
      ->where('solicitud_viaticos.id', '=', $id)->get();
    foreach ($solicitudes as $key => $solicitud)
    {
      $detalles_viaticos = $this->viaticos->DetalleViatico($solicitud->id);

      $detalles_listado = DetalleViatico::where('solicitud_viaticos_id', '=', $solicitud->id)
        ->join('catalogo_conceptos_viaticos AS CCV', 'CCV.id', '=', 'detalle_viaticos.catalogo_conceptos_viaticos_id')
        ->select('detalle_viaticos.*', 'CCV.nombre')->get();

      $beneficiario_viaticos = $this->viaticos->BeneficiarioViatico($solicitud->id);
      $vehiculos_viaticos = $this->viaticos->VehiculosItinerarioViaticos($solicitud->id);
      $personal_servicio_viaticos = $this->viaticos->PersonalDetalles($solicitud->id);

      $solicitud_viaticos_respuesta[] = [
        'solicitud' => $solicitud,
        'detalles' => $detalles_viaticos,
        'detalles_listado' => $detalles_listado,
        'beneficiarios' => $beneficiario_viaticos,
        'vehiculo' => $vehiculos_viaticos,
        'empleados' => $personal_servicio_viaticos,
      ];
    }
    return response()->json($solicitud_viaticos_respuesta);
  }

  /**
   * [revision Funcion que revisa si la solicitud de viaticos tenga beneficiarios agreagdos para poder enviarse a revision]
   * @param Int $id
   * @return Response json
   */
  public function revision($id)
  {
    $beneficiario_viaticos = BeneficiarioViatico::where('solicitud_viaticos_id', '=', $id)->get();
    if (count($beneficiario_viaticos) > 0)
    {
      return true;
    }
    else
    {
      return false;
    }
  }


  /**
   * [estadoGuardar Funcion que actualiza el estado de la solicitud de viaticos]
   * @param Int $id
   * @param Char $edo
   * @return Boolean true
   */
  public function estadoGuardar($id, $edo)
  {
    try
    {
      $solicitud = SolicitudViaticos::where('id', $id)->first();
      $solicitud->estado = $edo;
      Utilidades::auditar($solicitud, $solicitud->id);
      $solicitud->save();
      return true;
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  /**
   * [enviarCorreoVia Funcion que envia segun la revision o autorizacion ]
   */
  public function enviarCorreoVia($id, $mensaje, $mensaje_adicional, $edo, $tipo, $correo = "")
  {
    try
    {

      $respuesta = $this->viaticos->DatosCorreos($id, $edo);
      $correo_e = '';
      $mensaje_adicional_final = '';
      if ($tipo === 'control')
      {
        $correo_e = $correo;
        $mensaje_adicional_final = $respuesta['solicitud_viaticos']->nombre_autoriza;
      }
      else
      {
        $correo_e = $respuesta['correo_electronico'];
        $mensaje_adicional_final = $mensaje_adicional;
      }

      $hoy = date("Y-m-d");
      $data = [
        'estado' => $edo,
        'nombre' => $respuesta['nombre'],
        'correo_electronico' => $correo_e,
        'fecha' => $hoy,
        'solicitud_viaticos' => $respuesta['solicitud_viaticos'],
        'beneficiario_viatico' => $respuesta['beneficiario_viatico'][0],
        'detalle_viatico' => $respuesta['detalle_viatico'],
        'tet' => $respuesta['tet'],
        'et' => $respuesta['et'],
        'tt' => $respuesta['tt'],
        'vehiculoiv' => $respuesta['vehiculoiv'],
        'personalsv' => $respuesta['personalsv'],
        'mensaje' => $mensaje,
        'mensaje_adicional' => $mensaje_adicional_final,
      ];
      Mail::send('emails.viatico', $data, function ($message) use ($data, $mensaje)
      {
        $core = $data['correo_electronico'];
        $folio = $data['solicitud_viaticos']->folio;
        $pdf = PDF::loadView('pdf.forvia', $data);
        $message->to($core, $mensaje)->subject($mensaje);
        // $message->to('romerovelascogregorio@gmail.com', $mensaje)->subject($mensaje);
        $message->from('webmaster@conserflow.com', 'Conserflow');
        $message->attachData($pdf->output(), $folio . '.pdf');
        // $message->attach('public/img/1.png');
      });
      return  true;
      // }
      // else {
      //   return true;
      // }
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function eliminar($id)
  {
    $valores = explode('&', $id);

    $solicitud_id = $valores[0];
    $empresa = $valores[1];

    if ($empresa == 1)
    {
      $sol = SolicitudViaticos::find($solicitud_id);
      $sol->eliminado = 1;
      $sol->update();
    }
    return response()->json(['status' => true]);
  }

  public function cargarDatos($proyecto_id)
  {
    $solicitudes = SolicitudViaticos::ByProyecto($proyecto_id)->get();
    $i = 0;
    foreach ($solicitudes as $soli)
    {
      $detalles = $soli->detalles;
      $total_efectivo = $detalles->sum('efectivo');
      $total_transferencia = $detalles->sum('transferencia_electronica');
      $soli->total_efectivo = $total_efectivo;
      $soli->total_transferencia = $total_transferencia;
      $soli->update();
      $i++;
    }

    return $i;
  }
}
