<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Utilidades;
use Illuminate\Http\Request;
use App\ProyectoSubcategoria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProyectoSubcategoriasController extends Controller
{
    protected $rules = array(
        'nombre' => 'required|max:45',
    );

    public function index(Request $request)
    {
        // $proyectoSubcategoria = ProyectoSubcategoria::orderBy('id', 'desc')->get()->toArray();

        $proyectoSubcategoria = DB::table('proyecto_subcategorias')
            ->join('proyecto_categorias', 'proyecto_subcategorias.proyecto_categoria_id', '=', 'proyecto_categorias.id')
            ->select('proyecto_subcategorias.*', 'proyecto_categorias.nombre AS categoria')
            ->get();

        return response()->json($proyectoSubcategoria);
    }

    public function getList()
    {
        $proyectoSubcategoria = DB::table('proyecto_subcategorias')
            ->join('proyecto_categorias', 'proyecto_subcategorias.proyecto_categoria_id', '=', 'proyecto_categorias.id')
            ->select('proyecto_subcategorias.id', DB::raw("CONCAT(proyecto_categorias.nombre,' - ',proyecto_subcategorias.nombre) AS nombre"))->orderBy('nombre', 'asc')->get()->toArray();
        return response()->json($proyectoSubcategoria);
    }

    public function getListByCategoria($id)
    {
        $proyectoSubcategoria = DB::table('proyecto_subcategorias')
            ->where('proyecto_categoria_id', $id)->orderBy('nombre', 'asc')->get()->toArray();
        return response()->json($proyectoSubcategoria);
    }
}
