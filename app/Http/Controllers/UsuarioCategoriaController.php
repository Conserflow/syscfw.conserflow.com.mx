<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\UsuarioCategoria;
use Illuminate\Support\Facades\Validator;

class UsuarioCategoriaController extends Controller
{
    protected $rules = array(
        'proyecto_categoria_id' => 'required',
        'proyecto_subcategoria_id' => 'required',
    );

    public function index()
    {
        $usuarioCategorias = DB::table('usuario_categoria')
            ->join('proyecto_categorias', 'usuario_categoria.proyecto_categoria_id', '=', 'proyecto_categorias.id')
            ->join('proyecto_subcategorias', 'usuario_categoria.proyecto_subcategoria_id', '=', 'proyecto_subcategorias.id')
            ->join('users', 'usuario_categoria.user_id', '=', 'users.id')
            ->select('usuario_categoria.*', 'proyecto_categorias.nombre AS categoria', 'proyecto_subcategorias.nombre AS subcategoria', 'users.name AS usuario')
            ->get();

        return response()->json(
            $usuarioCategorias->toArray()
        );
    }
}
