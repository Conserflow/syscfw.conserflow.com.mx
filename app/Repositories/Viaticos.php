<?php
namespace App\Repositories;

use App\RHModels\Empleado;
use Illuminate\Support\Facades\DB;

class Viaticos
{

  public function solicitud_consulta()
  {
    return  DB::table("solicitud_viaticos")->leftJoin("empleados AS EA","EA.id","=","solicitud_viaticos.empleado_autoriza_id")
    ->join("empleados AS EE","EE.id","=","solicitud_viaticos.empleado_elabora_id")
    ->leftJoin("empleados AS ER","ER.id","=","solicitud_viaticos.empleado_revisa_id")
    ->leftJoin("empleados AS ES","ES.id","=","solicitud_viaticos.empleado_supervisor_id")
    ->join("proyectos AS P","P.id","=","solicitud_viaticos.proyecto_id")
    ->select("solicitud_viaticos.*","P.nombre_corto AS nombre_proyecto",
    DB::raw("CONCAT(EA.nombre,' ',EA.ap_paterno,' ',EA.ap_materno) AS nombre_autoriza"),
    DB::raw("CONCAT(EE.nombre,' ',EE.ap_paterno,' ',EE.ap_materno) AS nombre_elabora"),
    DB::raw("CONCAT(ER.nombre,' ',ER.ap_paterno,' ',ER.ap_materno) AS nombre_revisa"),
    DB::raw("CONCAT(ES.nombre,' ',ES.ap_paterno,' ',ES.ap_materno) AS nombre_supervisor"));
  }

  public function PersonalDetalles($id)
  {
    $personal_servicio_viaticos = [];
    $personal_servicio_viaticos = \App\PersonalDetalles::
    join('empleados AS E','E.id','=','personal_detalles.empleado_id')
    ->select('personal_detalles.*', DB::raw("CONCAT(E.nombre,' ',E.ap_paterno,' ',E.ap_materno) AS nombre_empleado"))
    ->where('personal_detalles.solicitud_viaticos_id','=',$id)
    ->get();
    return $personal_servicio_viaticos;
  }

  public function VehiculosItinerarioViaticos($id)
  {
    $vehiculos_viaticos = [];
    $vehiculos_viaticos = \App\VehiculosItinerarioViaticos::
    join('empleados AS EO','EO.id','=','vehiculos_itinerario_viaticos.empleado_operador_id')
    ->where('vehiculos_itinerario_viaticos.solicitud_viaticos_id','=',$id)
    ->select('vehiculos_itinerario_viaticos.*',
    DB::raw("CONCAT(EO.nombre,' ',EO.ap_paterno,' ',EO.ap_materno) AS nombre_operador"))->get();

    return $vehiculos_viaticos;
  }

  public function BeneficiarioViatico($id)
  {
    $beneficiario_viaticos  = [];
    $beneficiario_viaticos = DB::table("beneficiario_viatico")->
    leftJoin('empleados AS EB','EB.id','=','beneficiario_viatico.empleado_beneficiario_id')
    ->leftJoin('datos_bancarios_empleados AS DBE','DBE.id','=','beneficiario_viatico.datos_bancarios_empleado_id')
    ->leftJoin('catalogo_bancos AS CB','CB.id','=','DBE.banco_id')
    ->select('beneficiario_viatico.*',  DB::raw("CONCAT(EB.nombre,' ',EB.ap_paterno,' ',EB.ap_materno) AS nombre_beneficiario"),
    'DBE.numero_cuenta','DBE.numero_tarjeta','CB.nombre AS nombre_banco')
    ->where('beneficiario_viatico.solicitud_viaticos_id','=',$id)->get();

    return $beneficiario_viaticos;
  }

  public function BeneficiarioViaticoCSCT($id)
  {
    $beneficiario_viaticos  = [];
    $beneficiario_viaticos = \App\BeneficiarioViaticoDBP::
    leftJoin('empleados AS EB','EB.id','=','beneficiario_viatico.empleado_beneficiario_id')
    ->leftJoin('datos_bancarios_empleados AS DBE','DBE.id','=','beneficiario_viatico.datos_bancarios_empleado_id')
    ->leftJoin('catalogo_bancos AS CB','CB.id','=','DBE.banco_id')
    ->select('beneficiario_viatico.*',  DB::raw("CONCAT(EB.nombre,' ',EB.ap_paterno,' ',EB.ap_materno) AS nombre_beneficiario"),
    'DBE.numero_cuenta','DBE.numero_tarjeta','CB.nombre AS nombre_banco')
    ->where('beneficiario_viatico.solicitud_viaticos_id','=',$id)->get();

    return $beneficiario_viaticos;
  }

  public function DetalleViatico($id)
  {
    $detalles_viaticos = [];

    $detalles_viaticos = \App\DetalleViatico::where('solicitud_viaticos_id','=',$id)
    ->select(DB::raw("SUM(transferencia_electronica) AS transferencia"),
    DB::raw("SUM(efectivo) AS efectivo"),
    DB::raw("SUM(total) AS total"))->first();

    return $detalles_viaticos;

  }

  public function DetalleViaticoCSCT($id)
  {
    $detalles_viaticos = [];

    $detalles_viaticos = \App\DetalleViaticoCSCT::where('solicitud_viaticos_id','=',$id)
    ->select(DB::raw("SUM(transferencia_electronica) AS transferencia"),
    DB::raw("SUM(efectivo) AS efectivo"),
    DB::raw("SUM(total) AS total"))->first();

    return $detalles_viaticos;

  }

  public function DatosCorreos($id, $edo)
  {
    $beneficiario_viatico = [];
    $solicitud_viaticos = $this->solicitud_consulta()
    ->where('solicitud_viaticos.id',$id)
    ->first();

    $empleados = Empleado::leftJoin('users AS CC', 'empleados.id', '=', 'CC.empleado_id')
    ->where('empleados.id',$solicitud_viaticos->empleado_elabora_id)->select('empleados.*','CC.email as correo_electronico')->first();
    $empleado = $this->empleado($id,$edo);

    $detalle_viatico = \App\DetalleViatico::where('solicitud_viaticos_id','=',$id)->get();

    $beneficiario = $this->BeneficiarioViatico($id);
    if (count($beneficiario) != 0 ) {
      $beneficiario_viatico = $beneficiario;
    }
    $tet = 0;$et = 0 ; $tt = 0;
    foreach ($detalle_viatico as $key => $value) {
      $tet += $value->transferencia_electronica;
      $et += $value->efectivo;
      $tt += $value->total;
    }
    $vehiculoiv = $this->VehiculosItinerarioViaticos($id);
    $personalsv = $this->PersonalDetalles($id);

    $data =[
      'nombre' => $empleados->nombre.' '.$empleados->ap_paterno.' '.$empleados->ap_materno,
      'correo_electronico' => $empleado->correo_electronico,
      'solicitud_viaticos' => $solicitud_viaticos,
      'beneficiario_viatico' => $beneficiario_viatico,
      'detalle_viatico' => $detalle_viatico,
      'tet' => $tet,
      'et' => $et,
      'tt' => $tt,
      'vehiculoiv' => $vehiculoiv,
      'personalsv' => $personalsv,
    ];
    return $data ;

  }

  public function empleado($id, $edo)
  {
    $solicitud_viaticos = $this->solicitud_consulta()
    ->where('solicitud_viaticos.id',$id)
    ->first();
    switch ($edo) {
      case '2':
      $empleado = Empleado::leftJoin('users AS CC', 'empleados.id', '=', 'CC.empleado_id')
      ->where('empleados.id',$solicitud_viaticos->empleado_revisa_id)->select('empleados.*','CC.email as correo_electronico')->first();
        break;


      case '3':
      $empleado = Empleado::leftJoin('users AS CC', 'empleados.id', '=', 'CC.empleado_id')
      ->where('empleados.id',$solicitud_viaticos->empleado_autoriza_id)->select('empleados.*','CC.email as correo_electronico')->first();
        break;

      case '0':
      $empleado = Empleado::leftJoin('users AS CC', 'empleados.id', '=', 'CC.empleado_id')
      ->where('empleados.id',$solicitud_viaticos->empleado_elabora_id)->select('empleados.*','CC.email as correo_electronico')->first();
        break;

        case '4':
        $empleado = Empleado::leftJoin('users AS CC', 'empleados.id', '=', 'CC.empleado_id')
        ->where('empleados.id',$solicitud_viaticos->empleado_elabora_id)->select('empleados.*','CC.email as correo_electronico')->first();
          break;
    }
    return $empleado;
  }

  public function empleadoCSCT($id, $edo)
  {
    $solicitud_viaticos = $this->solicitud_consulta()
    ->where('solicitud_viaticos.id',$id)
    ->first();
    switch ($edo) {
      case '2':
      $empleado = Empleado::leftJoin('users AS CC', 'empleados.id', '=', 'CC.empleado_id')
      ->where('empleados.id',$solicitud_viaticos->empleado_revisa_id)->select('empleados.*','CC.email as correo_electronico')->first();
        break;


      case '3':
      $empleado = Empleado::leftJoin('users AS CC', 'empleados.id', '=', 'CC.empleado_id')
      ->where('empleados.id',$solicitud_viaticos->empleado_autoriza_id)->select('empleados.*','CC.email as correo_electronico')->first();
        break;

      case '0':
      $empleado = Empleado::leftJoin('users AS CC', 'empleados.id', '=', 'CC.empleado_id')
      ->where('empleados.id',$solicitud_viaticos->empleado_elabora_id)->select('empleados.*','CC.email as correo_electronico')->first();
        break;

        case '4':
        $empleado = Empleado::leftJoin('users AS CC', 'empleados.id', '=', 'CC.empleado_id')
        ->where('empleados.id',$solicitud_viaticos->empleado_elabora_id)->select('empleados.*','CC.email as correo_electronico')->first();
          break;
    }
    return $empleado;
  }
}
