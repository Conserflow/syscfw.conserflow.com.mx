<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\PermisosDashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PermisosDashboardController extends Controller
{
    protected $rules = array(
        'elemento_dashboard_id' => 'required',
    );

    public function index()
    {
        $permisosDashboard = DB::table('permisos_dashboards')
            ->join('elementos_dashboards', 'permisos_dashboards.elemento_dashboard_id', '=', 'elementos_dashboards.id')
            ->join('modulos', 'elementos_dashboards.modulo_id', '=', 'modulos.id')
            ->join('users', 'permisos_dashboards.user_id', '=', 'users.id')
            ->select('permisos_dashboards.*', 'modulos.nombre AS modulo', 'elementos_dashboards.nombre AS elemento', 'users.name AS usuario')
            ->get();

        return response()->json(
            $permisosDashboard->toArray()
        );
    }

    public function permisosDashboardPorUsuarioModulo(Request $request)
    {
        $user = Auth::user();
        $permisosDashboard = DB::table('permisos_dashboards')
            ->join('elementos_dashboards', 'permisos_dashboards.elemento_dashboard_id', '=', 'elementos_dashboards.id')
            ->where('permisos_dashboards.user_id', '=', $user->id)
            ->where('elementos_dashboards.modulo_id', '=', $request->modulo_id)
            ->select('elementos_dashboards.nombre')
            ->get();

        return response()->json(
            array_column($permisosDashboard->toArray(), 'nombre')
        );
    }
}
