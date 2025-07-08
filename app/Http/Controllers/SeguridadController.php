<?php

namespace App\Http\Controllers;

use App\CatalogoAnalisisSeguridad;
use App\EvaluacionRiesgos;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use \App\Http\Helpers\Utilidades;
use Illuminate\Support\Facades\Auth;
use App\Exports\ReporteEntregaExport;
use App\RHModels\RegChecador;
use Exception;

class SeguridadController extends Controller
{
  //

  public function index()
  {
    try
    {
      $analisis = DB::table('analisis_seguridad AS ase')
        ->join('empleados AS eo', 'eo.id', 'ase.empleado_operaciones')
        ->join('empleados AS es', 'es.id', 'ase.empleado_shso')
        ->select(
          'ase.*',
          DB::raw("CONCAT(eo.nombre,' ',eo.ap_paterno,' ',eo.ap_materno) AS operaciones_empleado"),
          DB::raw("CONCAT(es.nombre,' ',es.ap_paterno,' ',es.ap_materno) AS shso_empleado")
        )
        ->where('ase.condicion', '1')
        ->orderBy('id', 'DESC')
        ->get();

      return response()->json($analisis);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function eliminarAnalisis($id)
  {
    $analisis = DB::table('analisis_seguridad AS ase')->where('id', $id)->update(['condicion' => 0]);
    return response()->json(['status' => true]);
  }

  public function getListaProyectos()
  {
    try
    {
      $proyectos = DB::table('proyectos')
        ->select('id', 'nombre_corto')->get();

      return response()->json($proyectos);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function getLista($id)
  {
    try
    {
      // code...
      $valores = explode('&', $id);
      $fecha_reporte = $valores[0];
      $tipo = $valores[2];

      if ($valores[2] == 1)
      {
        $empleados = RegChecador::select('reg_checador.empleado_id', 'reg_checador.empleado')
          ->join('empleados AS e', 'e.id', 'reg_checador.empleado_id')
          ->join('puestos AS p', 'p.id', 'e.puesto_id')
          ->where('reg_checador.fecha', $valores[0])
          ->where('e.id_checador', $valores[1])
          ->groupBy('empleado_id', 'empleado')
          ->orderBy('empleado')
          ->get();

        $arreglo = [];
        foreach ($empleados as $key => $value)
        {
          $nombre = '';
          $asistencias = RegChecador::where('empleado_id', $value->empleado_id)
            ->where('reg_checador.fecha', $valores[0])
            ->get();

          $control_tiempo = DB::table('control_tiempo')
            ->join('proyectos AS p', 'p.id', 'control_tiempo.proyecto_id')
            ->where('empleado_asignado_id', $value->empleado_id)
            ->where('fecha', $valores[0])
            ->select('p.nombre_corto AS nombre_proyeto')
            ->first();

          $nombre = isset($control_tiempo) == false ? '' : $control_tiempo->nombre_proyeto;

          $puestos = DB::table('empleados AS e')
            ->join('puestos AS p', 'p.id', 'e.puesto_id')
            ->where('e.id', $value->empleado_id)
            ->select('p.nombre')
            ->first();

          $arreglo[] = [
            'empleado' => $value,
            'registros' => $asistencias,
            'puesto' => $puestos,
            'proyecto' => $nombre,
          ];
        }



        // return Excel::download(new ListaAsistenciaExport($id), 'ListadoAsistencia'.date('Ymd').'.xlsx');
        $pdf = PDF::loadView('pdf.listasistencia', compact('arreglo', 'fecha_reporte', 'tipo'));
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('letter', 'portrait');

        // $pdf->setPaper('A4', 'landscape');
        // return $pdf->download('cv-interno.pdf');
        return $pdf->stream();
      }
      elseif ($valores[2] == 2)
      {

        $control_tiempo = DB::table('control_tiempo')
          ->join('empleados AS e', 'e.id', 'control_tiempo.empleado_asignado_id')
          ->leftjoin('puestos AS p', 'p.id', 'e.puesto_id')
          ->where('fecha', $valores[0])
          ->where('nave', $valores[1])
          ->select('e.*', 'control_tiempo.nave', 'p.nombre AS nom_puesto')
          ->get();

        $pdf = PDF::loadView('pdf.listasistencia', compact('control_tiempo', 'fecha_reporte', 'tipo'));
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream();
      }
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
      return view('errors.204');
    }
  }

  public function getCatalogo()
  {
    try
    {
      $catalogo = DB::table('catalogo_analisis_seguridad')->where("condicion", "=", 1)
        ->get();

      return response()->json($catalogo);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function getCatalogoR()
  {
    try
    {
      $catalogo = DB::table('catalogo_bitacora_residuos')
        ->get();

      $arreglo = [];
      foreach ($catalogo as $key => $value)
      {
        $fuente = DB::table('catalogo_bitacora_residuos_fuente')->where('catalogo_bitacora_residuos_id', $value->id)->get();

        $arreglo[] = [
          'residuo' => $value,
          'fuente' => $fuente,
        ];
      }

      return response()->json($arreglo);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function guardarCatalogo(Request $request)
  {
    try
    {
      $catalogo = new \App\CatalogoAnalisisSeguridad();
      $catalogo->secuencia = $request->secuencia;
      $catalogo->riesgo = $request->riesgo;
      $catalogo->recomendacion = $request->recomendacion;
      Utilidades::auditar($catalogo, $catalogo->id);
      $catalogo->save();

      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function guardarCatalogoR(Request $request)
  {
    try
    {
      $catalogo = new \App\CatalogoBitacoraResiduos();
      $catalogo->residuo = $request->residuo;
      $catalogo->tipo = $request->tipo;
      Utilidades::auditar($catalogo, $catalogo->id);
      $catalogo->save();

      foreach ($request->fuente as $key => $value)
      {
        $fuente = new \App\CatalogoBitacoraResiduosFuente();
        $fuente->catalogo_bitacora_residuos_id = $catalogo->id;
        $fuente->fuente_generacion = $value;
        Utilidades::auditar($fuente, $fuente->id);
        $fuente->save();
      }

      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function actualizarCatalogo(Request $request)
  {
    try
    {
      $catalogo = \App\CatalogoAnalisisSeguridad::where('id', $request->id)->first();
      $catalogo->secuencia = $request->secuencia;
      $catalogo->riesgo = $request->riesgo;
      $catalogo->recomendacion = $request->recomendacion;
      Utilidades::auditar($catalogo, $catalogo->id);
      $catalogo->save();

      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function actualizarCatalogoR(Request $request)
  {
    try
    {
      $catalogo = \App\CatalogoBitacoraResiduos::where('id', $request->id)->first();
      $catalogo->residuo = $request->residuo;
      $catalogo->tipo = $request->tipo;
      Utilidades::auditar($catalogo, $catalogo->id);
      $catalogo->save();

      $fuente = \App\CatalogoBitacoraResiduosFuente::where('catalogo_bitacora_residuos_id', $request->id)->delete();

      foreach ($request->fuente as $key => $value)
      {
        $fuente = new \App\CatalogoBitacoraResiduosFuente();
        $fuente->catalogo_bitacora_residuos_id = $catalogo->id;
        $fuente->fuente_generacion = $value;
        Utilidades::auditar($fuente, $fuente->id);
        $fuente->save();
      }

      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function guardar(Request $request)
  {
    try
    {
      //
      DB::beginTransaction();

      $analisis = new \App\AnalisisSeguridad();
      $analisis->ubicacion = $request->ubicacion;
      $analisis->numero_permiso = $request->permiso;
      $analisis->descripcion = $request->descripcion;
      $analisis->empleado_operaciones = $request->operaciones;
      $analisis->empleado_shso = $request->shso;
      $analisis->fecha = $request->fecha;
      Utilidades::auditar($analisis, $analisis->id);
      $analisis->save();

      for ($i = 0; $i < count($request->listado_id); $i++)
      {
        $evaluacion = new \App\EvaluacionRiesgos();
        $evaluacion->analisis_seguridad_id = $analisis->id;
        $evaluacion->catalogo_analisis_seguridad_id = $request->listado_id[$i];
        $evaluacion->supervisor_id = $request->listado_supervisor[$i];
        Utilidades::auditar($evaluacion, $evaluacion->id);
        $evaluacion->save();
      }

      DB::commit();
      // return response()->json($request);
      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      DB::rollBack();
      Utilidades::errors($e);
    }
  }

  public function actualizar(Request $request)
  {
    try
    {
      DB::beginTransaction();
      //inicio busqueda y elimino
      $evaluacion = \App\EvaluacionRiesgos::where('analisis_seguridad_id', $request->id)
        ->whereNotIn('catalogo_analisis_seguridad_id', $request->listado_id)
        ->whereNotIn('supervisor_id', $request->listado_supervisor)
        ->delete();

      //busco y agrego
      for ($i = 0; $i < count($request->listado_id); $i++)
      {
        $evaluacion_busqueda = \App\EvaluacionRiesgos::where('analisis_seguridad_id', $request->id)
          ->where('catalogo_analisis_seguridad_id', $request->listado_id[$i])
          ->where('supervisor_id', $request->listado_supervisor[$i])
          ->first();

        if (isset($evaluacion_busqueda) == false)
        {
          $evaluacion_guardar = new \App\EvaluacionRiesgos();
          $evaluacion_guardar->analisis_seguridad_id = $request->id;
          $evaluacion_guardar->catalogo_analisis_seguridad_id = $request->listado_id[$i];
          $evaluacion_guardar->supervisor_id = $request->listado_supervisor[$i];
          Utilidades::auditar($evaluacion_guardar, $evaluacion_guardar->id);
          $evaluacion_guardar->save();
        }
      }

      $analisis = \App\AnalisisSeguridad::where('id', $request->id)->first();
      $analisis->ubicacion = $request->ubicacion;
      $analisis->numero_permiso = $request->permiso;
      $analisis->descripcion = $request->descripcion;
      $analisis->empleado_operaciones = $request->operaciones;
      $analisis->empleado_shso = $request->shso;
      $analisis->fecha = $request->fecha;
      Utilidades::auditar($analisis, $analisis->id);
      $analisis->save();
      DB::commit();

      return response()->json($request);
    }
    catch (\Throwable $e)
    {
      DB::rollBack();
      Utilidades::errors($e);
    }
  }


  /** 
   * Aux para obtener los riesgos
   */
  private function AuxGetRiesgos($id)
  {
    try
    {
      $riesgos = EvaluacionRiesgos::join('catalogo_analisis_seguridad AS cas', 'cas.id', 'evaluacion_riesgos.catalogo_analisis_seguridad_id')
        ->join('empleados AS s', 's.id', 'evaluacion_riesgos.supervisor_id')
        ->select(
          'evaluacion_riesgos.*',
          'cas.secuencia',
          'cas.riesgo',
          'cas.recomendacion',
          DB::raw("CONCAT(s.nombre,' ',s.ap_paterno,' ',s.ap_materno) AS supervisor")
        )
        ->where('evaluacion_riesgos.analisis_seguridad_id', $id)
        ->get();

      return $riesgos;
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  /**
   * Obtiene los riesgos del analisis ingresado
   */
  public function getRiesgos($id)
  {
    try
    {
      $riesgos = $this->AuxGetRiesgos($id);
      return response()->json($riesgos);
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
    }
  }

  /**
   * Elimina el riesgo ingresado
   */
  public function EliminarRiesgo(Request $request)
  {
    try
    {
      $riesgo = EvaluacionRiesgos::find($request->id);
      $analisis_id = $riesgo->analisis_seguridad_id;
      $riesgo->delete();
      $nuevos_riesgos = $this->AuxGetRiesgos($analisis_id);
      return Status::Success("riesgos", $nuevos_riesgos);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "eliminar el riesgo");
    }
  }

  public function getParticipantes($id)
  {
    try
    {

      $participantes = DB::table('participantes_analisis AS pa')
        ->join('empleados AS e', 'e.id', 'pa.empleado_id')
        ->select(
          'pa.*',
          DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,'',e.ap_materno) AS empleado")
        )
        ->where('pa.analisis_seguridad_id', $id)
        ->get();

      return response()->json($participantes);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function guardarParticipantes(Request $request)
  {
    try
    {

      $participantes = new \App\ParticipantesAnalisis();
      $participantes->analisis_seguridad_id = $request->id;
      $participantes->empleado_id = $request->empleado;
      Utilidades::auditar($participantes, $participantes->id);
      $participantes->save();

      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function eliminarParticipante($id)
  {
    try
    {
      $valores = explode('&', $id);
      $participantes = DB::table('participantes_analisis')->where('analisis_seguridad_id', $valores[1])
        ->where('empleado_id', $valores[0])->delete();

      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function descargar($id)
  {
    try
    {

      $data = '';

      $analisis = DB::table('analisis_seguridad AS ase')
        ->join('empleados AS eo', 'eo.id', 'ase.empleado_operaciones')
        ->join('empleados AS es', 'es.id', 'ase.empleado_shso')
        ->select(
          'ase.*',
          DB::raw("CONCAT(eo.nombre,' ',eo.ap_paterno,' ',eo.ap_materno) AS operaciones_empleado"),
          DB::raw("CONCAT(es.nombre,' ',es.ap_paterno,' ',es.ap_materno) AS shso_empleado")
        )
        ->where('ase.id', $id)
        ->first();
      $riesgos = \App\EvaluacionRiesgos::join('catalogo_analisis_seguridad AS cas', 'cas.id', 'evaluacion_riesgos.catalogo_analisis_seguridad_id')
        ->join('empleados AS s', 's.id', 'evaluacion_riesgos.supervisor_id')
        ->select(
          'evaluacion_riesgos.*',
          'cas.secuencia',
          'cas.riesgo',
          'cas.recomendacion',
          DB::raw("CONCAT(s.nombre,' ',s.ap_paterno,' ',s.ap_materno) AS supervisor")
        )
        ->where('evaluacion_riesgos.analisis_seguridad_id', $id)
        ->orderBy('evaluacion_riesgos.id', 'ASC')
        ->get();

      $participantes = DB::table('participantes_analisis AS pa')
        ->join('empleados AS e', 'e.id', 'pa.empleado_id')
        ->leftJoin('puestos AS p', 'p.id', 'e.puesto_id')
        ->select(
          'pa.*',
          DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS empleado"),
          'p.nombre AS puesto'
        )
        ->where('pa.analisis_seguridad_id', $id)
        ->get();

      $celdas_uno = 60 - count($riesgos);
      $celdas_dos = 30 - count($participantes);

      $anio = substr($analisis->fecha, 2, 2);
      $mes = substr($analisis->fecha, 5, -3);
      $dia = substr($analisis->fecha, 8);

      $mesnombre = $this->getMes($mes);
      $fechafinal = $dia . '-' . $mesnombre . '-' . $anio;

      $fecha_analisis = strtotime($analisis->fecha);
      $fecha_01 = strtotime("2021-06-21");
      $fecha_02 = strtotime("2023-02-07");
      $medidas_seguridad = "";
      $tipo = 1;
      if ($fecha_analisis < $fecha_01) // Revision 0
      {
        $medidas_seguridad = "Medidas de control";
        $revision = "00";
        $titulo_supervisor = "Supervisor de Área";
        $emision = "01.ABR.20";
      }
      else if ($fecha_analisis < $fecha_02) // Revision 1
      {
        $medidas_seguridad = "Medidas de control";
        $revision = "01";
        $titulo_supervisor = "Nombre";
        $emision = "21.JUN.21";
      }
      else // Revision 2
      {
        $tipo = 2;
        $medidas_seguridad = "Recomendaciones para el trabajo seguro";
        $revision = "02";
        $titulo_supervisor = "Nombre";
        $emision = "07.FEB.23";
      }
      $pdf = PDF::loadView(
        'pdf.analisisseguridad',
        compact(
          "medidas_seguridad",
          "tipo",
          'data',
          'analisis',
          'riesgos',
          'participantes',
          'celdas_uno',
          'celdas_dos',
          'fechafinal',
          "revision",
          "emision",
          "titulo_supervisor"
        )
      );
      error_reporting(E_ALL ^ E_DEPRECATED);
      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('letter', 'landscape');
      return $pdf->stream();
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function guardarPermiso(Request $request)
  {
    try
    {
      DB::beginTransaction();
      $uno = new \App\PermisoSeguridad();
      $uno->proyecto_id = $request['uno']['proyecto_id'];
      $uno->tipo = $request['uno']['tipo'];
      $uno->uno = $this->CrearFolio($request['uno']['proyecto_id'], $request['uno']['tipo']);
      $uno->dos = $request['uno']['dos'];
      $uno->tres = $request['uno']['tres'];
      $uno->cuatro = $request['uno']['cuatro'];
      $uno->cinco = $request['uno']['cinco'];
      $uno->tipo = "";
      $uno->seis = $request['uno']['seis'];
      Utilidades::auditar($uno, $uno->id);
      $uno->save();

      $dos = new \App\PermisoSeguridadDos();
      $dos->permiso_seguridad_id = $uno->id;
      $dos->uno = $request['dos']['uno'];
      $dos->dos = $request['dos']['dos'];
      $dos->tres = $request['dos']['tres'];
      $dos->cuatro = $request['dos']['cuatro'];
      $dos->cinco = $request['dos']['cinco'];
      $dos->seis = $request['dos']['seis'];
      // Utilidades::auditar($dos, $dos->id);
      $dos->save();

      $tres = new \App\PermisoSeguridadTres();
      $tres->permiso_seguridad_id = $uno->id;
      $tres->uno = $request['tres']['uno'];
      $tres->dos = $request['tres']['dos'];
      $tres->tres = $request['tres']['tres'];
      $tres->cuatro = $request['tres']['cuatro'];
      Utilidades::auditar($tres, $tres->id);
      $tres->save();

      $cuatro = new \App\PermisoSeguridadCuatro();
      $cuatro->permiso_seguridad_id = $uno->id;
      $cuatro->uno = $request['cuatro']['uno'];
      $cuatro->dos = $request['cuatro']['dos'];
      $cuatro->tres = $request['cuatro']['tres'];
      $cuatro->cuatro = $request['cuatro']['cuatro'];
      $cuatro->cinco = $request['cuatro']['cinco'];
      $cuatro->seis = $request['cuatro']['seis'];
      $cuatro->siete = $request['cuatro']['siete'];
      $cuatro->ocho = $request['cuatro']['ocho'];
      $cuatro->nueve = $request['cuatro']['nueve'];
      $cuatro->diez = $request['cuatro']['diez'];
      $cuatro->once = $request['cuatro']['once'];
      $cuatro->doce = $request['cuatro']['doce'];
      $cuatro->trece = $request['cuatro']['trece'];
      Utilidades::auditar($cuatro, $cuatro->id);
      $cuatro->save();

      $cinco = new \App\PermisoSeguridadCinco();
      $cinco->permiso_seguridad_id = $uno->id;
      $cinco->uno = $request['cinco']['uno'];
      $cinco->dos = $request['cinco']['dos'];
      $cinco->tres = $request['cinco']['tres'];
      $cinco->cuatro = $request['cinco']['cuatro'];
      $cinco->cinco = $request['cinco']['cinco'];
      $cinco->seis = $request['cinco']['seis'];
      Utilidades::auditar($cinco, $cinco->id);
      $cinco->save();

      $seis = new \App\PermisoSeguridadSeis();
      $seis->permiso_seguridad_id = $uno->id;
      $seis->uno = $request['seis']['uno'];
      $seis->dos = $request['seis']['dos'];
      $seis->tres = $request['seis']['tres'];
      $seis->cuatro = $request['seis']['cuatro'];
      $seis->cinco = $request['seis']['cinco'];
      $seis->seis = $request['seis']['seis'];
      $seis->siete = $request['seis']['siete'];
      $seis->ocho = $request['seis']['ocho'];
      Utilidades::auditar($seis, $seis->id);
      $seis->save();

      $siete = new \App\PermisoSeguridadSiete();
      $siete->permiso_seguridad_id = $uno->id;
      $siete->uno = $request['siete']['uno'];
      Utilidades::auditar($siete, $siete->id);
      $siete->save();

      $ocho = new \App\PermisoSeguridadOcho();
      $ocho->permiso_seguridad_id = $uno->id;
      $ocho->uno = $request['ocho']['uno']['id'];
      $ocho->dos = $request['ocho']['dos'];
      $ocho->tres = $request['ocho']['tres']['id'];
      $ocho->cuatro = $request['ocho']['cuatro'];
      $ocho->cinco = $request['ocho']['cinco']['id'];
      $ocho->seis = $request['ocho']['seis'];
      Utilidades::auditar($ocho, $ocho->id);
      $ocho->save();

      $diez = new \App\PermisoSeguridadDiez();
      $diez->permiso_seguridad_id = $uno->id;
      if (isset($request['diez']['uno']['id']))
        $diez->uno = $request['diez']['uno']['id'];
      $diez->dos = $request['diez']['dos'];
      $diez->tres = $request['diez']['tres'];
      if (isset($request['diez']['cuatro']['id']))
        $diez->cuatro = $request['diez']['cuatro']['id'];
      $diez->cinco = $request['diez']['cinco'];
      $diez->seis = $request['diez']['seis'];
      Utilidades::auditar($diez, $diez->id);
      $diez->save();

      $nueve = new \App\PermisoSeguridadNueve();
      $nueve->permiso_seguridad_id = $uno->id;
      $nueve->uno = $request['nueve']['uno'];
      $nueve->dos = $request['nueve']['dos'];
      $nueve->tres = $request['nueve']['tres'];
      $nueve->cuatro = $request['nueve']['cuatro'];
      $nueve->cinco = $request['nueve']['cinco'];
      Utilidades::auditar($nueve, $nueve->id);
      $nueve->save();

      foreach ($request['once'] as $key => $value)
      {
        $once = new \App\PermisoSeguridadOnce();
        $once->permiso_seguridad_id = $uno->id;
        $once->uno = $value['uno'];
        $once->dos = $value['dos'];
        $once->tres = $value['tres'];
        $once->cuatro = $value['cuatro']['id'];
        $once->cinco = $value['cinco'];
        $once->seis = $value['seis'];
        $once->siete = $value['siete'];
        $once->ocho = $value['ocho']['id'];
        $once->nueve = $value['nueve'];
        $once->diez = $value['diez'];
        Utilidades::auditar($once, $once->id);
        $once->save();
      }
      DB::commit();
      return Status::Success();
    }
    catch (\Throwable $e)
    {
      DB::rollBack();
      return Status::Error($e, "guardar el permiso de seguridad");
    }
  }

  public function actualizarPermiso(Request $request)
  {
    try
    {
      DB::beginTransaction();
      $uno = \App\PermisoSeguridad::where('id', $request->id)->first();
      // $uno->uno = $request['uno']['uno'];
      $uno->dos = $request['uno']['dos'];
      $uno->tres = $request['uno']['tres'];
      $uno->cuatro = $request['uno']['cuatro'];
      $uno->cinco = $request['uno']['cinco'];
      $uno->seis = $request['uno']['seis'];
      $uno->save();

      $dos = \App\PermisoSeguridadDos::where('permiso_seguridad_id', $request->id)->first();;
      // $dos->permiso_seguridad_id = $uno->id;
      $dos->uno = $request['dos']['uno'];
      $dos->dos = $request['dos']['dos'];
      $dos->tres = $request['dos']['tres'];
      $dos->cuatro = $request['dos']['cuatro'];
      $dos->cinco = $request['dos']['cinco'];
      $dos->seis = $request['dos']['seis'];
      $dos->save();

      $tres = \App\PermisoSeguridadTres::where('permiso_seguridad_id', $request->id)->first();;
      // $tres->permiso_seguridad_id = $uno->id;
      $tres->uno = $request['tres']['uno'];
      $tres->dos = $request['tres']['dos'];
      $tres->tres = $request['tres']['tres'];
      $tres->cuatro = $request['tres']['cuatro'];
      $tres->save();

      $cuatro = \App\PermisoSeguridadCuatro::where('permiso_seguridad_id', $request->id)->first();
      // $cuatro->permiso_seguridad_id = $uno->id;
      $cuatro->uno = $request['cuatro']['uno'];
      $cuatro->dos = $request['cuatro']['dos'];
      $cuatro->tres = $request['cuatro']['tres'];
      $cuatro->cuatro = $request['cuatro']['cuatro'];
      $cuatro->cinco = $request['cuatro']['cinco'];
      $cuatro->seis = $request['cuatro']['seis'];
      $cuatro->siete = $request['cuatro']['siete'];
      $cuatro->ocho = $request['cuatro']['ocho'];
      $cuatro->nueve = $request['cuatro']['nueve'];
      $cuatro->diez = $request['cuatro']['diez'];
      $cuatro->once = $request['cuatro']['once'];
      $cuatro->doce = $request['cuatro']['doce'];
      $cuatro->trece = $request['cuatro']['trece'];
      $cuatro->catorce = $request['cuatro']['catorce'];
      $cuatro->save();

      $cinco = \App\PermisoSeguridadCinco::where('permiso_seguridad_id', $request->id)->first();
      // $cinco->permiso_seguridad_id = $uno->id;
      $cinco->uno = $request['cinco']['uno'];
      $cinco->dos = $request['cinco']['dos'];
      $cinco->tres = $request['cinco']['tres'];
      $cinco->cuatro = $request['cinco']['cuatro'];
      $cinco->cinco = $request['cinco']['cinco'];
      $cinco->seis = $request['cinco']['seis'];
      $cinco->save();

      $seis = \App\PermisoSeguridadSeis::where('permiso_seguridad_id', $request->id)->first();
      // $seis->permiso_seguridad_id = $uno->id;
      $seis->uno = $request['seis']['uno'];
      $seis->dos = $request['seis']['dos'];
      $seis->tres = $request['seis']['tres'];
      $seis->cuatro = $request['seis']['cuatro'];
      $seis->cinco = $request['seis']['cinco'];
      $seis->seis = $request['seis']['seis'];
      $seis->siete = $request['seis']['siete'];
      $seis->ocho = $request['seis']['ocho'];
      $seis->save();

      $siete = \App\PermisoSeguridadSiete::where('permiso_seguridad_id', $request->id)->first();
      // $siete->permiso_seguridad_id = $uno->id;
      $siete->uno = $request['siete']['uno'];
      $siete->save();

      $ocho = \App\PermisoSeguridadOcho::where('permiso_seguridad_id', $request->id)->first();
      // $ocho->permiso_seguridad_id = $uno->id;
      $ocho->uno = $request['ocho']['uno']['id'];
      $ocho->dos = $request['ocho']['dos'];
      $ocho->tres = $request['ocho']['tres']['id'];
      $ocho->cuatro = $request['ocho']['cuatro'];
      $ocho->cinco = $request['ocho']['cinco']['id'];
      $ocho->seis = $request['ocho']['seis'];
      $ocho->save();

      $diez = \App\PermisoSeguridadDiez::where('permiso_seguridad_id', $request->id)->first();
      // $diez->permiso_seguridad_id = $uno->id;
      if (isset($request['diez']['uno']['id']))
        $diez->uno = $request['diez']['uno']['id'];
      $diez->dos = $request['diez']['dos'];
      $diez->tres = $request['diez']['tres'];
      if (isset($request['diez']['cuatro']['id']))
        $diez->cuatro = $request['diez']['cuatro']['id'];
      $diez->cinco = $request['diez']['cinco'];
      $diez->seis = $request['diez']['seis'];
      $diez->save();

      $nueve = \App\PermisoSeguridadNueve::where('permiso_seguridad_id', $request->id)->first();
      // $nueve->permiso_seguridad_id = $uno->id;
      $nueve->uno = $request['nueve']['uno'];
      $nueve->dos = $request['nueve']['dos'];
      $nueve->tres = $request['nueve']['tres'];
      $nueve->cuatro = $request['nueve']['cuatro'];
      $nueve->cinco = $request['nueve']['cinco'];
      $nueve->save();

      $once_eliminar = \App\PermisoSeguridadOnce::where('permiso_seguridad_id', $request->id)->delete();

      foreach ($request['once'] as $key => $value)
      {

        $once = new \App\PermisoSeguridadOnce();
        $once->permiso_seguridad_id = $uno->id;
        $once->uno = $value['uno'];
        $once->dos = $value['dos'];
        $once->tres = $value['tres'];
        $once->cuatro = $value['cuatro']['id'];
        $once->cinco = $value['cinco'];
        $once->seis = $value['seis'];
        $once->siete = $value['siete'];
        $once->ocho = $value['ocho']['id'];
        $once->nueve = $value['nueve'];
        $once->diez = $value['diez'];
        $once->save();
      }
      DB::commit();
      return Status::Success();
    }
    catch (\Throwable $e)
    {
      DB::rollBack();
      Utilidades::errors($e);
    }
  }

  public function CrearFolio($proyecto_id, $tipo)
  {
    // Obtener proyecto
    $nombre = DB::table("seguridad_folios_proyectos")
      ->where("proyecto_id", $proyecto_id)
      ->select("nombre")->first()->nombre;

    // Consecutivo
    $n = DB::table("permiso_seguridad")
      ->where("proyecto_id", $proyecto_id)
      ->count();
    $folio = "CSF-$nombre-" . str_pad($n + 1, 3, "0", STR_PAD_LEFT);
    return $folio;
  }

  public function abc(Request $request)
  {
    return response()->json($request);
  }

  public function getPermiso()
  {
    $permiso = DB::table('permiso_seguridad')
      ->orderBy("id", "desc")
      ->limit(50)
      ->get();
    $arreglo = [];
    foreach ($permiso as $key => $value)
    {
      $dos = DB::table('permiso_seguridad_dos')->where('permiso_seguridad_id', $value->id)->first();
      $tres = DB::table('permiso_seguridad_tres')->where('permiso_seguridad_id', $value->id)->first();
      $cuatro = DB::table('permiso_seguridad_cuatro')->where('permiso_seguridad_id', $value->id)->first();
      $cinco = DB::table('permiso_seguridad_cinco')->where('permiso_seguridad_id', $value->id)->first();
      $seis = DB::table('permiso_seguridad_seis')->where('permiso_seguridad_id', $value->id)->first();
      $siete = DB::table('permiso_seguridad_siete')->where('permiso_seguridad_id', $value->id)->first();

      // $ocho = DB::table('permiso_seguridad_ocho')->where('permiso_seguridad_id',$value->id)->first();
      $ocho = DB::table('permiso_seguridad_ocho AS po')
        ->leftJoin('empleados AS ero', 'ero.id', 'po.uno')
        ->leftJoin('empleados AS est', 'est.id', 'po.tres')
        ->leftJoin('empleados AS erh', 'erh.id', 'po.cinco')
        ->select(
          'po.*',
          DB::raw("CONCAT(ero.nombre,' ',ero.ap_paterno,' ',ero.ap_materno) AS residente"),
          DB::raw("CONCAT(est.nombre,' ',est.ap_paterno,' ',est.ap_materno) AS supervisor"),
          DB::raw("CONCAT(erh.nombre,' ',erh.ap_paterno,' ',erh.ap_materno) AS shso")
        )
        ->where('permiso_seguridad_id', $value->id)->first();

      $nueve = DB::table('permiso_seguridad_nueve')->where('permiso_seguridad_id', $value->id)->first();

      // $diez = DB::table('permiso_seguridad_diez')->where('permiso_seguridad_id',$value->id)->first();
      $diez = DB::table('permiso_seguridad_diez AS po')
        ->leftJoin('empleados AS eu', 'eu.id', 'po.uno')
        ->leftJoin('empleados AS ec', 'ec.id', 'po.cuatro')
        ->select(
          'po.*',
          DB::raw("CONCAT(eu.nombre,' ',eu.ap_paterno,' ',eu.ap_materno) AS e_uno"),
          DB::raw("CONCAT(ec.nombre,' ',ec.ap_paterno,' ',ec.ap_materno) AS e_cuatro")
        )
        ->where('permiso_seguridad_id', $value->id)->first();

      // $once = DB::table('permiso_seguridad_once')->where('permiso_seguridad_id',$value->id)->get();
      $once = DB::table('permiso_seguridad_once AS po')
        ->leftJoin('empleados AS ee', 'ee.id', 'po.cuatro')
        ->leftJoin('empleados AS ea', 'ea.id', 'po.ocho')
        ->select(
          'po.*',
          DB::raw("CONCAT(ee.nombre,' ',ee.ap_paterno,' ',ee.ap_materno) AS e_cuatro"),
          DB::raw("CONCAT(ea.nombre,' ',ea.ap_paterno,' ',ea.ap_materno) AS e_ocho")
        )
        ->where('permiso_seguridad_id', $value->id)->get();


      $arreglo[] = [
        'uno' => $value,
        'dos' => $dos,
        'tres' => $tres,
        'cuatro' => $cuatro,
        'cinco' => $cinco,
        'seis' => $seis,
        'siete' => $siete,
        'ocho' => $ocho,
        'nueve' => $nueve,
        'diez' => $diez,
        'once' => $once,
      ];
    }
    return response()->json($arreglo);
  }

  public function descargarPermiso($id)
  {
    $permiso = DB::table('permiso_seguridad')->where('id', $id)->first();

    $dos = DB::table('permiso_seguridad_dos')->where('permiso_seguridad_id', $id)->first();
    $tres = DB::table('permiso_seguridad_tres')->where('permiso_seguridad_id', $id)->first();
    $cuatro = DB::table('permiso_seguridad_cuatro')->where('permiso_seguridad_id', $id)->first();
    $cinco = DB::table('permiso_seguridad_cinco')->where('permiso_seguridad_id', $id)->first();
    $seis = DB::table('permiso_seguridad_seis')->where('permiso_seguridad_id', $id)->first();
    $siete = DB::table('permiso_seguridad_siete')->where('permiso_seguridad_id', $id)->first();
    $ocho = DB::table('permiso_seguridad_ocho AS po')
      ->leftJoin('empleados AS ero', 'ero.id', 'po.uno')
      ->leftJoin('empleados AS est', 'est.id', 'po.tres')
      ->leftJoin('empleados AS erh', 'erh.id', 'po.cinco')
      ->select(
        'po.*',
        DB::raw("CONCAT(ero.nombre,' ',ero.ap_paterno,' ',ero.ap_materno) AS residente"),
        DB::raw("CONCAT(est.nombre,' ',est.ap_paterno,' ',est.ap_materno) AS supervisor"),
        DB::raw("CONCAT(erh.nombre,' ',erh.ap_paterno,' ',erh.ap_materno) AS shso")
      )
      ->where('permiso_seguridad_id', $id)->first();
    $nueve = DB::table('permiso_seguridad_nueve')->where('permiso_seguridad_id', $id)->first();

    $diez = DB::table('permiso_seguridad_diez AS po')
      ->leftJoin('empleados AS eu', 'eu.id', 'po.uno')
      ->leftJoin('empleados AS ec', 'ec.id', 'po.cuatro')
      ->select(
        'po.*',
        DB::raw("CONCAT(eu.nombre,' ',eu.ap_paterno,' ',eu.ap_materno) AS e_uno"),
        DB::raw("CONCAT(ec.nombre,' ',ec.ap_paterno,' ',ec.ap_materno) AS e_cuatro")
      )
      ->where('permiso_seguridad_id', $id)->first();

    $once = DB::table('permiso_seguridad_once AS po')
      ->leftJoin('empleados AS ee', 'ee.id', 'po.cuatro')
      ->leftJoin('empleados AS ea', 'ea.id', 'po.ocho')
      ->select(
        'po.*',
        DB::raw("CONCAT(ee.nombre,' ',ee.ap_paterno,' ',ee.ap_materno) AS e_cuatro"),
        DB::raw("CONCAT(ea.nombre,' ',ea.ap_paterno,' ',ea.ap_materno) AS e_ocho")
      )
      ->where('permiso_seguridad_id', $id)->get();

    $palabras = explode(' ', $ocho->shso);
    $iniciales = '';
    foreach ($palabras as $key => $value)
    {
      $iniciales .= substr($value, 0, 1);
    }

    $arreglo  = [
      'uno' => $permiso,
      'dos' => $dos,
      'tres' => $tres,
      'cuatro' => $cuatro,
      'cinco' => $cinco,
      'seis' => $seis,
      'siete' => $siete,
      'ocho' => $ocho,
      'nueve' => $nueve,
      'diez' => $diez,
      'once' => $once,
      'iniciales' => $iniciales,
    ];
    $pdf = PDF::loadView('pdf.permisoseguridad', compact('arreglo'));
    //  $pdf->setPaper('A4', 'landscape');
    // $pdf->setPaper("A4", "portrait");
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('letter', 'portrait');
    // return $pdf->download('cv-interno.pdf');
    return $pdf->stream();
  }

  private function getMes($numMes)
  {
    $mesnombre = '';
    switch ($numMes)
    {
      case '01':
        $mesnombre = 'ENE';
        break;
      case '02':
        $mesnombre = 'FEB';
        break;
      case '03':
        $mesnombre = 'MAR';
        break;
      case '04':
        $mesnombre = 'ABR';
        break;
      case '05':
        $mesnombre = 'MAY';
        break;
      case '06':
        $mesnombre = 'JUN';
        break;
      case '07':
        $mesnombre = 'JUL';
        break;
      case '08':
        $mesnombre = 'AGO';
        break;
      case '09':
        $mesnombre = 'SEP';
        break;
      case '10':
        $mesnombre = 'OCT';
        break;
      case '11':
        $mesnombre = 'NOV';
        break;
      case '12':
        $mesnombre = 'DIC';
        break;
    }
    return $mesnombre;
  }

  public function getCB()
  {
    $hoy = date('Y-m-d');
    $data = DB::table('control_cb')
      ->join('empleados AS e', 'e.id', 'control_cb.empleado_id')
      ->select('control_cb.*', DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS empleado"))
      ->where('fecha', $hoy)->get();

    return response()->json($data);
  }

  public function guardarCB(Request $request)
  {
    try
    {
      $hoy = date('Y-m-d');
      $user = Auth::user();

      $data = new \App\ControlCB();
      $data->empleado_id = $request->empleado_id;
      $data->cantidad = $request->cantidad;
      $data->tipo = $request->tipo;
      $data->fecha = $hoy;
      $data->autoriza_id = $user->empleado_id;
      Utilidades::auditar($data, $data->id);
      $data->save();
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function getFechasCB($id)
  {
    $d = explode('&', $id);
    // $hoy = date('Y-m-d');
    $data = DB::table('control_cb')
      ->join('empleados AS e', 'e.id', 'control_cb.empleado_id')
      ->select('control_cb.*', DB::raw("CONCAT(e.nombre,' ',e.ap_paterno,' ',e.ap_materno) AS empleado"))
      ->whereBetween('fecha', $d)->get();

    return response()->json($data);
  }

  public function excel($id)
  {
    ob_end_clean();
    ob_start();
    return Excel::download(new ReporteEntregaExport($id), 'ReporteEntrega.xlsx');
    // code...
  }

  public function eliminarPermiso($id)
  {
    $uno = \App\PermisoSeguridad::where('id', $id)->first();
    $uno->condicion = 0;
    Utilidades::auditar($uno, $uno->id);
    $uno->save();

    return response()->json(['status' => true]);
  }

  public function EliminarCatalogo(Request $request)
  {
    try
    {
      $catalogo = CatalogoAnalisisSeguridad::find($request->id);
      $catalogo->condicion = false;
      Utilidades::auditar($catalogo, $catalogo->id);
      $catalogo->update();

      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "eliminar el catálogo");
    }
  }

  /**
   * Obtener los proyectos que tienen folio creado
   */
  public function ObtenerProyectosFolios()
  {
    try
    {
      $proyectos = DB::table("seguridad_folios_proyectos as sfp")
        ->join("proyectos as p", "p.id", "sfp.proyecto_id")
        ->select("p.id", "p.nombre_corto")
        ->orderBy("p.nombre_corto")
        ->get();
      return Status::Success("proyectos", $proyectos);
    }
    catch (Exception $e)
    {
      return Status::Error($e);
    }
  }

  /**
   * Obtener folios de permisos activos
   */
  public function ObtenerFoliosPermisos()
  {
    try
    {
      $proyectos = DB::table("permiso_seguridad as ps")
        ->select("ps.id", "ps.uno as folio")
        ->orderBy("ps.uno")
        ->where("ps.condicion", 1)
        ->get();
      return Status::Success("folios", $proyectos);
    }
    catch (Exception $e)
    {
      return Status::Error($e);
    }
  }
}
