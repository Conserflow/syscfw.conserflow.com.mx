<?php

namespace App\Http\Controllers\Vehiculos;

use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use Illuminate\Http\Request;
use App\VehiculosModels\TenenciaUnidades;
use Illuminate\Support\Facades\Storage;
use \App\Http\Helpers\Utilidades;
use Exception;

class TenenciaUnidadesController extends Controller
{
    //
    public function index()
    {
        $tenencia = TenenciaUnidades::get()->toArray();
        return response()->json($tenencia);
    }

    public function store(Request $request)
    {

        try
        {
            if (!$request->ajax()) return redirect('/');
            $datos = LimpiarInput::LimpiarIngnorar($request->all(), ["comprobante"]);
            if ($request->metodo == 1)
            {
                $tenencia = new TenenciaUnidades($datos);
                $tenencia->save();
                Auditoria::AuditarCambios($tenencia);

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
                    $FacturaStore = 'Tenen_' . uniqid() . '.' . $FacturaExt;
                    //Subida del archivo al servidor ftp
                    Storage::disk('local')->put('Archivos/' . $FacturaStore, fopen($request->file('comprobante'), 'r+'));
                    $tenencia->comprobante = $FacturaStore;
                    Auditoria::AuditarCambios($tenencia);
                    $tenencia->update();
                }
            }
            else
            {
                $tenencia = TenenciaUnidades::findOrFail($request->id);
                $tenencia->fill($datos);
                Auditoria::AuditarCambios($tenencia);
                $tenencia->update();

                /*ACTUALIZAR REGISTRO*/
                $tenenciaes = TenenciaUnidades::where('id', $request->id)->get();
                foreach ($tenenciaes as $key => $item)
                {
                    $ValorPoliza = $item->comprobante;
                    $FacturaStore = $item->comprobante;
                }

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
                    $FacturaStore = 'Tene_' . uniqid() . '.' . $FacturaExt;
                    //Subida del archivo al servidor ftp
                    Storage::disk('local')->put('Archivos/' . $FacturaStore, fopen($request->file('comprobante'), 'r+'));
                    if ($ValorPoliza != '')
                    {
                        //Elimina el archivo en el servidor si requiere ser actualizado
                        Utilidades::ftpSolucionEliminar($ValorPoliza);
                    }
                    $tenencia = TenenciaUnidades::findOrFail($request->id);
                    $tenencia->comprobante = $FacturaStore;
                    Auditoria::AuditarCambios($tenencia);
                    $tenencia->update();
                }
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar la tenencia");
        }
    }

    public function show($id)
    {
        $tenencia = TenenciaUnidades::where('unidad_id', $id)->get();
        return response()->json($tenencia);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        //elimina de la ruta local el archivo descargado
        Storage::disk('descarga')->delete($id);
        Storage::disk('local')->delete($id);
    }
}
