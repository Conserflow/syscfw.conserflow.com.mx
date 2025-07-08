<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\Viaticos;
use \App\Http\Helpers\Utilidades;
use Illuminate\Support\Facades\Storage;
use App\DetalleViatico;
use Barryvdh\DomPDF\Facade as PDF;

class ViaticosController extends Controller
{
  protected $viaticos;

  public function __construct(Viaticos $viatico)
  {
    $this->viaticos = $viatico;
  }

  /**
   * Ingresar un nuevo viatico
   *
   * @param Request $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $viatico = \App\Viaticos::where('solicitud_viaticos_id', '=', $request->solicitud_viaticos_id)->where('pda', '=', $request->pda)->first();
    if (isset($viatico) != true)
    {
      $viaticos = new \App\Viaticos();
      $viaticos->solicitud_viaticos_id = $request->solicitud_viaticos_id;
      $viaticos->gastos_comprobados_deducibles = $request->gastos_comprobados_deducibles;
      $viaticos->gastos_comprobados_no_deducibles = $request->gastos_comprobados_no_deducibles;
      $viaticos->pda = $request->pda;
      $viaticos->save();

      $this->reporteGastos($request->solicitud_viaticos_id, $request->pda);
      return response()->json(array('status' => true));
    }
    else
    {
      DB::table('viaticos')->select('viaticos.*')
        ->where('viaticos.solicitud_viaticos_id', '=', $request->solicitud_viaticos_id)
        ->where('viaticos.pda', '=', $request->pda)
        ->update([
          'gastos_comprobados_deducibles' => $request->gastos_comprobados_deducibles,
          'gastos_comprobados_no_deducibles' => $request->gastos_comprobados_no_deducibles,
        ]);
      $this->reporteGastos($request->solicitud_viaticos_id, $request->pda);
      return response()->json(array('status' => true));
    }
  }

  /**
   * Seleccionar viaticos de un proyecto
   *
   * @param int $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $arreglo = [];
    $viaticos = DB::table('solicitud_viaticos')
      ->select('solicitud_viaticos.*')
      ->where('solicitud_viaticos.proyecto_id', '=', $id)
      ->where('solicitud_viaticos.estado', '!=', '1')
      ->get();
    foreach ($viaticos as $key => $viatico)
    {

      $beneficiarios = $this->viaticos->BeneficiarioViatico($viatico->id);
      $detalles_viaticos = $this->viaticos->DetalleViatico($viatico->id);

      $arreglo[] = [
        'solicitud' => $viatico,
        'beneficiario' => $beneficiarios,
        'detalle' => $detalles_viaticos,
      ];
    }

    return response()->json($arreglo);
  }

  /**
   * Busca un encabezado de viatico por id
   * @param int $id
   * @return \Illuminate\Http\Response
   */
  public function verviaticos($id)
  {
    $viaticos = \App\Viaticos::where('id', '=', $id)->first();
    return response()->json($viaticos);
  }

  /**
   * Actualizacion de un encabezado de viatico
   * @param Request request
   * @param Int $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $viaticos = \App\Viaticos::where('id', '=', $id)->first();
    $viaticos->tipo = $request->tipo;
    $viaticos->proyecto = $request->proyecto;
    $viaticos->beneficiario = $request->beneficiario;
    $viaticos->importe_TE = $request->importe_TE;
    $viaticos->importe_efectivo = $request->importe_efectivo;
    $viaticos->save();

    return response()->json($viaticos);
  }

  public function reporteGastos($id, $pda)
  {
    $reporte = DB::table('reporte_gastos_viaticos')
      ->select('reporte_gastos_viaticos')->where('solicitud_viaticos_id', '=', $id)
      ->where('pda', '=', $pda)->update([
        'condicion' => 2,
      ]);
    return true;
  }

  public function descargarViaticos($id)
  {
    try
    {
      $valores = explode('&', $id);
      $id = $valores[0];
      $empresa = $valores[1];

      $beneficiario_viatico = [];
      $solicitud_viaticos = $this->viaticos->solicitud_consulta()
        ->where('solicitud_viaticos.id', $id)
        ->first();

      $detalle_viatico = \App\DetalleViatico::where('solicitud_viaticos_id', '=', $id)->get();
      $beneficiario = $this->viaticos->BeneficiarioViatico($id);
      if (count($beneficiario) != 0)
      {
        $beneficiario_viatico = $beneficiario[0];
      }
      $tet = 0;
      $et = 0;
      $tt = 0;
      foreach ($detalle_viatico as $key => $value)
      {
        $tet += $value->transferencia_electronica;
        $et += $value->efectivo;
        $tt += $value->total;
      }
      $vehiculoiv = $this->viaticos->VehiculosItinerarioViaticos($id);
      $personalsv = $this->viaticos->PersonalDetalles($id);

        $pdf = PDF::loadView('pdf.forvia', compact(
	//$pdf = PDF::loadView('viaticos.forvia', compact(
        'solicitud_viaticos',
        'beneficiario_viatico',
        'detalle_viatico',
        'tet',
        'et',
        'tt',
        'vehiculoiv',
        'personalsv'
      ));
      $pdf->setPaper("letter", "portrait");
      return $pdf->stream();
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
      return view('errors.204');
    }
  }

  public function descargarnFij($id)
  {
    try
    {



      $valores = explode('&', $id);
      $id = $valores[0];
      $empresa = $valores[1];
      if ($empresa == 1)
      {

        $beneficiario_viatico = [];
        $solicitud_viaticos = $this->viaticos->solicitud_consulta()
          ->where('solicitud_viaticos.id', $id)
          ->first();
        $detalle_viatico = DetalleViatico::where('solicitud_viaticos_id', '=', $id)->get();

        $beneficiario = $this->viaticos->BeneficiarioViatico($id);
        if (count($beneficiario) != 0)
        {
          $beneficiario_viatico = $beneficiario[0];
        }
        // return $beneficiario_viatico;
        $tet = 0;
        $et = 0;
        $tt = 0;
        foreach ($detalle_viatico as $key => $value)
        {
          $tet += $value->transferencia_electronica;
          $et += $value->efectivo;
          $tt += $value->total;
        }
        // $vehiculoiv = $this->viaticos->VehiculosItinerarioViaticos($id);
        // $personalsv = $this->viaticos->PersonalDetalles($id);

        $pdf = PDF::loadView('pdf.forfij', compact(
          'solicitud_viaticos',
          'beneficiario_viatico',
          'detalle_viatico',
          'tet',
          'et',
          'tt'
        ));
        $pdf->setPaper("letter", "portrait");
        return $pdf->stream();
      }
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
      return view('errors.204');
    }
  }

  public function obtenerNombre($request)
  {
    //obtiene el nombre del archivo y su extension
    $FacturaNE = $request->file('adjunto')->getClientOriginalName();
    //Obtiene el nombre del archivo
    $FacturaNombre = pathinfo($FacturaNE, PATHINFO_FILENAME);
    //obtiene la extension
    $FacturaExt = $request->file('adjunto')->getClientOriginalExtension();
    //nombre que se guarad en BD
    $FacturaStore = 'ReporteGastoM_' . uniqid() . '.' . $FacturaExt;

    return $FacturaStore;
  }


  public function subirAdjunto($request, $nombre_archivo)
  {
    Storage::disk('local')->put('Archivos/' . $nombre_archivo, fopen($request->file('adjunto'), 'r+'));
    return true;
  }

  public function eliminararch($id)
  {
    Storage::disk('descarga')->delete($id);
    Storage::disk('local')->delete($id);
  }
}
