<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ElementosDashboardController extends Controller
{
    protected $rules = array(
        'nombre' => 'required|max:50',
    );

    public function index()
    {
        $puestos = DB::table('elementos_dashboards')
            ->join('modulos', 'elementos_dashboards.modulo_id', '=', 'modulos.id')
            ->select('elementos_dashboards.*', 'modulos.nombre AS modulo')
            ->get();

        return response()->json(
            $puestos->toArray()
        );
    }
}
