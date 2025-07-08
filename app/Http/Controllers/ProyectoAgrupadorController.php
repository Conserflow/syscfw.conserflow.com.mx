<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Utilidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProyectoAgrupadorController extends Controller
{
    //

    public function guardaAgrupador(Request $request)
    {
        try
        {
            $guardar = new \App\ProyectoAgrupador();
            $guardar->nombre = $request->nombre;
            Utilidades::auditar($guardar, $guardar->id);
            $guardar->save();

            return response()->json(array('status' => true));
        }
        catch (\Throwable $e)
        {
            Utilidades::errors($e);
        }
    }

    public function listaProyectos()
    {
        $lista = DB::table('proyecto_agrupador')
            ->select('proyecto_agrupador.*')
            ->get();
        return response()->json($lista);
    }
}
