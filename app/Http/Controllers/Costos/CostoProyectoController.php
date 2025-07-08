<?php

namespace App\Http\Controllers\Costos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Support\Facades\DB;

class CostoProyectoController extends Controller
{
    public function ObtenerViaticos($p_id)
    {
        try
        {
            // Viáticos
            $total_viaticos = DB::table("costos_viaticos as cv")
                ->select(
                    DB::raw("
                    sum(combustible+casetas+alimentos+
                    hospedaje+boletos+hidratacion+otros) as total")
                )
                ->where("cv.proyecto_id", $p_id)
                ->first()->total;

            // Comidas
            $total_comidas = 0;
            $comidas = DB::table("costos_comidas as cc")
                ->select(
                    DB::raw("cantidad*precio as total"),
                )
                ->where("cc.proyecto_id", $p_id)
                ->get();
            foreach ($comidas as $c)
            {
                $total_comidas += $c->total;
            }

            // Viaticos RH personal
            $total_contrataciones = DB::table("costos_contrataciones as cc")
                ->select(DB::raw("sum(boletos) as total"))
                ->where("cc.proyecto_id", $p_id)
                ->first()->total;

            // Combustible
            $combustible = DB::table("costos_combustible as cc")
                ->select(
                    DB::raw("TRUNCATE(cc.cantidad *cc.precio,2) as total"),
                )
                ->where("cc.proyecto_id", $p_id)
                ->get();
            $total_combustible = 0;
            foreach ($combustible as $c)
            {
                $total_combustible += $c->total;
            }
            $total_combustible = round($total_combustible, 2);
            // Agua
            $agua = DB::table("costos_agua as ca")
                ->select("tipo as tipo", DB::raw("sum(total) as total"))
                ->groupBy("tipo")
                ->where("ca.proyecto_id", $p_id)
                ->get();
            if (count($agua) < 2) $agua = [
                (object)["tipo" => 1, "total" => 0],
                (object)["tipo" => 1, "total" => 0]
            ];

            return Status::Success("costos", [
                "conceptos" => [
                    [
                        "nombre" => "Viáticos en Sitio",
                        "subtotal" => $total_viaticos
                    ],
                    [
                        "nombre" => "Comidas de Personal",
                        "subtotal" => $total_comidas
                    ],
                    [
                        "nombre" => "Contratacion de Personal",
                        "subtotal" => $total_contrataciones
                    ],
                    [
                        "nombre" => "Combustible de Unidades",
                        "subtotal" => $total_combustible
                    ],
                    [
                        "nombre" => "Consumo de Agua (Bonafont)",
                        "subtotal" => $agua[0]->total
                    ],
                    [
                        "nombre" => "Consumo de Agua (Pipas)",
                        "subtotal" => $agua[1]->total
                    ],
                ],
                "total" => $total_viaticos + $total_comidas +
                    $total_contrataciones + $total_combustible +
                    $agua[0]->total = $agua[1]->total
            ]);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los costos del proyecto");
        }
    }
}
