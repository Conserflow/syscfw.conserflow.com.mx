<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Http\Request;

class FacturacionV4Controller extends Controller
{
    public function Timbrar(Request $request)
    {
        try
        {
            $facturador = new Facturador($request->id, $request->prueba);
            $res = $facturador->Timbrar();
            return response()->json($res);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "timbrar la factura");
        }
    }

    /**
     * Cancelar la factura seleccionada
     */
    public function Cancelar(Request $request)
    {
        try
        {
            $res = Facturador::Cancelar(
                $request->id,
                $request->motivo,
                $request->uuid_reemplazo,
                $request->prueba
            );
            return response()->json($res);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "cancelar la factura de prueba");
        }
    }

    /**
     * Descargar el XML de la factura seleccionada
     */
    public function DescargarXMLPrueba(Request $request)
    {
        try
        {
            $res = Facturador::DescargarXMLPrueba($request->id, $request->file);
            if ($res == null) return Status::Error2("No encontrada");
            return Status::Success("factura", $res);
        }
        catch (Exception $e)
        {
            dd($e);
        }
    }

    /**
     * Descargar el PDF de la factura seleccionada
     */
    public static function DescargarFactura($file)
    {
        try
        {
            return Facturador::DescargarFactura($file);
        }
        catch (Exception $e)
        {
            dd($e);
        }
    }
}
