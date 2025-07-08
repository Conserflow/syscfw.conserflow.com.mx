<?php

namespace App\Http\Controllers\Vehiculos;

use App\Http\Controllers\Auditoria;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use Illuminate\Http\Request;
use App\VehiculosModels\VerificacionUnidades;
use Illuminate\Support\Facades\Storage;
use \App\Http\Helpers\Utilidades;
use Exception;

class VerificacionUnidadesController extends Controller
{
    //
    public function index()
    {
        try
        {
            $poliza = VerificacionUnidades::get();
            return response()->json($poliza);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener las verificaciones");
        }
    }

    public function store(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');
            $datos = LimpiarInput::LimpiarIngnorar($request->all(), ["comprobante"]);

            if ($request->metodo == 1)
            {
                /*NUEVO REGISTRO*/
                $poliza = new VerificacionUnidades($datos);
                Utilidades::auditar($poliza, $poliza->id);
                $poliza->save();
                Auditoria::AuditarCambios(($poliza));

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
                    $FacturaStore = 'Vfca_' . uniqid() . '.' . $FacturaExt;
                    //Subida del archivo al servidor ftp
                    Storage::disk('local')->put('Archivos/' . $FacturaStore, fopen($request->file('comprobante'), 'r+'));

                    $poliza->comprobante = $FacturaStore;
                    Auditoria::AuditarCambios(($poliza));
                    $poliza->update();
                }
                return Status::Success();
            }
            else if ($request->metodo == 0)
            {
                /*ACTUALIZAR REGISTRO*/
                $poliza = VerificacionUnidades::findOrFail($request->id);
                $poliza->fill($datos);
                Auditoria::AuditarCambios(($poliza));
                $poliza->update();

                $ValorPoliza = "";
                $FacturaStore = "";
                //*Variables para actualizar nuevos archivos y eliminar existentes
                $polizaes = VerificacionUnidades::where('id', $request->id)->get();
                foreach ($polizaes as $key => $item)
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
                    $FacturaStore = 'Vfca_' . uniqid() . '.' . $FacturaExt;
                    //Subida del archivo al servidor ftp
                    Storage::disk('local')->put('Archivos/' . $FacturaStore, fopen($request->file('comprobante'), 'r+'));
                    if ($ValorPoliza != '')
                    {
                        //Elimina el archivo en el servidor si requiere ser actualizado
                        Utilidades::ftpSolucionEliminar($ValorPoliza);
                    }
                    $poliza->comprobante = $FacturaStore;
                    Auditoria::AuditarCambios(($poliza));
                    $poliza->update();
                }
                return Status::Success();
            }
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar la verificación");
        }
    }

    public function show($id)
    {
        $poliza = VerificacionUnidades::where('unidad_id', $id)->get();
        return response()->json($poliza);
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
