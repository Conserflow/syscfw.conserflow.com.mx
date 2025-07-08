<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Otros\Utils;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;

class CumplesController extends Controller
{

    public function index()
    {
        try
        {
            $mescumpleaños = DB::table('empleados')
                ->whereraw('month(fech_nac)=month(NOW())')
                ->where('condicion', '=', '1')
                ->select(DB::raw('empleados.*, DAY(fech_nac) AS Edad'))
                ->orderBy('Edad')
                ->get();

            return response()->json($mescumpleaños->toArray());
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los empleados");
        }
    }


    /**
     * Obtiene el diseno de los empleados activos que cumplen mes en el anio acual
     */
    public function DescargarCumple($mes_n)
    {
        try
        {
            if ($mes_n == 13) $mes_n = 1; // Diciembre : Enero
            $mes = Utils::NombreMesNumero($mes_n);
            $cumples = DB::table('empleados as e')
                ->select(
                    DB::raw("DAY(fech_nac) AS dia"),
                    DB::raw("CONCAT_WS(' ',e.nombre,e.ap_paterno,e.ap_materno) as empleado")
                )
                ->whereraw("month(e.fech_nac)=$mes_n")
                ->orderBy("dia")
                ->where('condicion', 1)
                ->get();

            $pdf = PDF::loadView('pdf.rh.cumpleanios', compact('cumples', "mes"));
            return $pdf->stream();
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
