<?php

namespace App\Http\Controllers\TI\Mtto;

use App\Http\Controllers\Auditoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimpiarInput;
use App\Http\Controllers\Status;
use App\TIModels\MttoImpresora;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImpresorasController extends Controller
{
    /**
     * Obtener todas las impresoras con su fecha de mmtto
     */
    public function ObtenerImpresoras()
    {
        try
        {
            $impresoras = DB::table("ti_impresoras as ti")
                ->select(
                    "ti.id",
                    "ti.descripcion",
                    "ti.modelo",
                    DB::raw("concat_ws(' / ',ti.marca,ti.modelo) as marca_modelo"),
                    "ti.no_serie",
                    DB::raw("'' as ultimo_mtto"),
                    "ti.condicion"
                )
                ->get();
            foreach ($impresoras as $i)
            {
                $mtto = DB::table("ti_impresoras_mtto as tim")
                    ->where("tim.impresora_id", $i->id)
                    ->select("tim.fecha")
                    ->orderBy("tim.fecha", "desc")
                    ->first();
                if ($mtto == null) continue;
                $i->ultimo_mtto = $mtto->fecha;
            }

            return Status::Success("impresoras", $impresoras);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener las impresoras");
        }
    }

    /**
     * Guardar un mantenimiento de impresora
     */
    public function GuardarMtto(Request $request)
    {
        try
        {
            $data = LimpiarInput::LimpiarCampos($request->all(), ["observaciones"]);
            $mtto = new MttoImpresora($data);
            $mtto->empleado_registra_id = Auth::user()->empleado_id;
            $mtto->save();
            Auditoria::AuditarCambios($mtto);
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el mantenimiento de impresora");
        }
    }

    /**
     * Obtener el historial de mtto de la impresora
     */
    public function Historial($imp_id)
    {
        try
        {
            $mttos = DB::table("ti_impresoras_mtto as tim")
                ->where("tim.impresora_id", $imp_id)
                ->select(
                    "tim.id",
                    "tim.fecha",
                    "tim.ubicacion",
                    "tim.total_hojas",
                    "tim.observaciones",
                    "tim.c",
                    "tim.m",
                    "tim.y",
                    "tim.k"
                )
                ->orderBy("tim.fecha")
                ->get();
            return Status::Success("historial", $mttos);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener el historial de impresion");
        }
    }
}
