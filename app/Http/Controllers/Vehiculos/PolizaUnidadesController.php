<?php

namespace App\Http\Controllers\Vehiculos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use Illuminate\Http\Request;
use App\VehiculosModels\PolizaUnidades;
use Illuminate\Support\Facades\Storage;
use \App\Http\Helpers\Utilidades;
use Exception;

class PolizaUnidadesController extends Controller
{
    /**
     * Obtiene todas las polizas
     */
    public function index()
    {
        try
        {
            $polizas = PolizaUnidades::where("condicion",1)->get();
            return Status::Success("polizas", $polizas);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener las polizas");
        }
    }

    /**
     * Registra una nueva poliza
     */
    public function store(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');
            $datos = LimpiarInput::LimpiarIngnorar($request->all(), ["comprobante"]);
            if ($request->metodo == 1)
            {
                /*NUEVO REGISTRO*/
                $poliza = new PolizaUnidades($datos);
                Utilidades::auditar($poliza, 0);
                $poliza->save();

                //--valida que campos del request contienen archivos y realiza el guardado--//
                if ($request->hasFile('comprobante'))
                {
                    //obtiene el nombre del archivo y su extension
                    $FacturaNE = $request->file('comprobante')->getClientOriginalName();
                    //Obtiene el nombre del archivo
                    $FacturaNombre = pathinfo($FacturaNE, PATHINFO_FILENAME);
                    //obtiene la extension
                    $FacturaExt = $request->file('comprobante')->getClientOriginalExtension();
                    //nombre que se guarad en BD
                    $FacturaStore = 'Poliza_' . uniqid() . '.' . $FacturaExt;
                    //Subida del archivo al servidor ftp
                    Storage::disk('local')->put('Archivos/' . $FacturaStore, fopen($request->file('comprobante'), 'r+'));
                    $poliza->comprobante = $FacturaStore;
                    $poliza->update();
                }
            }
            if ($request->metodo == 0)
            {
                $polizaes = PolizaUnidades::where('id', $request->id)->get();
                foreach ($polizaes as $key => $item)
                {
                    $ValorPoliza = $item->factura;
                    $FacturaStore = $item->factura;
                }

                $poliza = PolizaUnidades::findOrFail($request->id);
                $poliza->fill($datos);
                Utilidades::auditar($poliza, $poliza->id);
                $poliza->update();

                //--valida que campos del request contienen archivos y realiza el guardado--//
                if ($request->hasFile('comprobante'))
                {
                    //obtiene el nombre del archivo y su extension
                    $FacturaNE = $request->file('comprobante')->getClientOriginalName();
                    //Obtiene el nombre del archivo
                    $FacturaNombre = pathinfo($FacturaNE, PATHINFO_FILENAME);
                    //obtiene la extension
                    $FacturaExt = $request->file('comprobante')->getClientOriginalExtension();
                    //nombre que se guarad en BD
                    $FacturaStore = 'Poliza_' . uniqid() . '.' . $FacturaExt;
                    //Subida del archivo al servidor ftp
                    Storage::disk('local')->put('Archivos/' . $FacturaStore, fopen($request->file('comprobante'), 'r+'));
                    if ($ValorPoliza != '')
                    {
                        //Elimina el archivo en el servidor si requiere ser actualizado
                        Utilidades::ftpSolucionEliminar($ValorPoliza);
                    }

                    $poliza->comprobante = $FacturaStore;
                    Utilidades::auditar($poliza, $poliza->id);
                    $poliza->update();
                }
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar la poliza");
        }
    }

    /**
     * Obtiene las polizas de la unidad ingresada
     */
    public function show($id)
    {
        try
        {
            $polizas = PolizaUnidades::where('unidad_id', $id)
            ->where("condicion",1)
            ->get();
            return Status::Success("polizas", $polizas);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener las polizas de la unidad");
        }
    }

    /**
     * Descarga la poliza ingresada 
     */
    public function descarga($id)
    {
        // Se obtiene el archivo del FTP serve
        $archivo = Utilidades::ftpSolucion($id);
        // Se coloca el archivo en una ruta local
        Storage::disk('descarga')->put($id, $archivo);
        //--Devuelve la respuesta y descarga el archivo--//
        return response()->download(storage_path() . '/app/descargas/' . $id);
    }

    /**
     * Borra la poliza descargada del temporal
     */
    public function editar($id)
    {
        //elimina de la ruta local el archivo descargado
        Storage::disk('descarga')->delete($id);
        Storage::disk('local')->delete($id);
    }

    /**
     * Eliminar la poliza seleccionada
     */
    public function EliminarPoliza(Request $request)
    {
        try
        {
            $poliza = PolizaUnidades::find($request->id);
            $poliza->condicion = 0;
            $poliza->update();
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "eliminar la poliza");
        }
    }
}
