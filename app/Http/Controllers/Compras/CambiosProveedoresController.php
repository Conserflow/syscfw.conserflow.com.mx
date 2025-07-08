<?php

namespace App\Http\Controllers\Compras;

use App\ComprasModel\ProveedorCambio;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use Barryvdh\DomPDF\Facade;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CambiosProveedoresController extends Controller
{

    /**
     * Obtener el historial de cambio del proveedor ingresado
     */
    public function ObtenerHistorial($id)
    {
        try
        {
            $historial = ProveedorCambio::where("proveedor_id", $id)
                ->select(
                    "id",
                    "tipo_movimiento",
                    "fecha",
                    "modificacion"
                )
                ->orderBy("fecha")
                ->get();
            return Status::Success("historial", $historial);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener el historial");
        }
    }

    /**
     * Descargar el cambio seleccionado del proveedor
     */
    public function DescargarHistorial($id)
    {
        try
        {
            // Obtener el cambio
            $cambio = ProveedorCambio::find($id);
            $revision = $cambio->fecha < "2022-08-22" ? "00" : "01";
            $emision = $cambio->fecha < "2022-08-22" ? "29.JUN.20" : "10.AGO.22";
            $formatoNR02 = $cambio->fecha >= "2025-07-01";

            // Crear objeto
            $campos = json_decode($cambio["campos_cambios"]);
            $pdf = Facade::loadView(
                "pdf.compras.historialproveedor",
                compact("cambio", "campos", "revision", "emision","formatoNR02")
            );
            $pdf->setPaper("letter", "portrait");
            $pdf->getDomPDF()->set_option("enable_php", true);
            return $pdf->stream("PCO-02-F-01-ALTA Y_O MODIFICACIÓN DE PROVEEDORES" . ($formatoNR02 ? '_NR02' : '') . ".pdf");
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }

    /**
     * Obtener los cambios del proveedor ingresado
     * @param $id int Id del Proveedor 
     * @param $datos Array Cambios del proveedor
     */
    public function ObtenerCambios($id)
    {
        try
        {
            // Obtener proveedor original
            $proveedor = (array)DB::table("proveedores")->find($id);
            // Obtener ultimo banco registrado
            $banco = (array) DB::table("bancos_proveedores")->where("proveedor_id", $id)
                ->orderBy("id", "desc")->first();
            if ($banco == []) // Sin bancos registrados
            {
                $banco = [
                    "banco_id" => 0,
                    "banco" => "N/D",
                    "cuenta" => "N/D",
                    "clabe" => "N/D",
                    "proveedor_id" => $id,
                    "condiciones" => "N/D",
                    "moneda" => "N/D"
                ];
            }
            $data = array_merge($proveedor, $banco);
            $json = json_encode($data);

            return ["status" => true, "data" => $json];
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return ["status" => false, "data" => ""];
        }
    }
}
