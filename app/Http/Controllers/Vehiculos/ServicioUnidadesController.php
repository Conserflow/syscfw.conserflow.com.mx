<?php

namespace App\Http\Controllers\Vehiculos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\VehiculosModels\ServicioUnidades;
use App\VehiculosModels\PartidasServicioUnidades;
use Exception;

class ServicioUnidadesController extends Controller
{
  //
  public function show($id)
  {
    $arreglo = [];
    $servicioUnidades = ServicioUnidades::where('unidad_id', $id)->get();
    foreach ($servicioUnidades as $key => $value)
    {
      $partida_servicio = PartidasServicioUnidades::join('catalogo_trafico AS CT', 'CT.id', '=', 'partidas_servicios_unidades.catalogo_trafico_id')
        ->select('partidas_servicios_unidades.*', 'CT.operacion_id', 'CT.nombre')
        ->where('unidad_id', $id)
        ->where('servicio_id', $value->id)->get();
      $arreglo[] = [
        'servicio' => $value,
        'partidas' => $partida_servicio,
      ];
    }

    return response()->json($arreglo);
  }

  public function store(Request $request)
  {
    try
    {
      if (!$request->ajax()) return redirect('/');
      $valores = explode(',', $request->tipo_servicios);
      $tamanio = count($valores);

      $datos = LimpiarInput::LimpiarCampos($request->all(), ["proveedor", "responsable"]);
      if ($request->metodo == 1)
      {
        /*NUEVO REGISTRO*/
        $servicioUnidades = new ServicioUnidades($datos);
        $servicioUnidades->save();
      }
      else
      {
        /*ACTUALIZAR REGISTRO*/
        $servicioUnidades = ServicioUnidades::find($request->id);
        $servicioUnidades->fill($datos);
        $servicioUnidades->update();
      }

      // Valida que campos del request contienen archivos y realiza el guardado--//
      if ($request->hasFile('comprobante'))
      {
        //obtiene el nombre del archivo y su extension
        //obtiene la extension
        $FacturaExt = $request->file('comprobante')->getClientOriginalExtension();
        //nombre que se guarad en BD
        $FacturaStore = 'Serv_' . uniqid() . '.' . $FacturaExt;
        //Subida del archivo al servidor ftp
        Storage::disk('local')->put('Archivos/' . $FacturaStore, fopen($request->file('comprobante'), 'r+'));
        $servicioUnidades->comprobante = $FacturaStore;
        $servicioUnidades->update();
      }

      // Llenar partidas
      $this->llenarCatalogo($request, $valores, $tamanio, $servicioUnidades->id);

      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "guardar el servicio");
    }
  }

  public function llenarCatalogo($request, $valores, $tamanio, $servicio_id)
  {
    $tipo = PartidasServicioUnidades::where('servicio_id', $servicio_id)->first();
    if (isset($tipo) == true)
    {
      $data = PartidasServicioUnidades::where('servicio_id', $servicio_id)->delete();
      for ($i = 0; $i < $tamanio; $i++)
      {
        $tipo_servicios = new PartidasServicioUnidades();
        $tipo_servicios->catalogo_trafico_id = $valores[$i];
        $tipo_servicios->unidad_id = $request->unidad_id;
        $tipo_servicios->servicio_id = $servicio_id;
        $tipo_servicios->save();
      }
    }
    else
    {
      for ($i = 0; $i < $tamanio; $i++)
      {
        $tipo_servicios = new PartidasServicioUnidades();
        $tipo_servicios->catalogo_trafico_id = $valores[$i];
        $tipo_servicios->unidad_id = $request->unidad_id;
        $tipo_servicios->servicio_id = $servicio_id;
        $tipo_servicios->save();
      }
    }
    return true;
  }
}
