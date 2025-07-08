<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Utilidades;
use Illuminate\Http\Request;
use App\ProyectoCategoria;
use Illuminate\Support\Facades\Validator;

class ProyectoCategoriasController extends Controller
{
    protected $rules = array(
        'nombre' => 'required|max:45',
    );

    public function index(Request $request)
    {
        $proyectoCategoria = ProyectoCategoria::orderBy('id', 'desc')->get()->toArray();
        return response()->json($proyectoCategoria);
    }

    public function getList(Request $request)
    {
        $proyectoCategoria = ProyectoCategoria::select('id', 'nombre')->orderBy('id', 'desc')->get()->toArray();
        return response()->json($proyectoCategoria);
    }
}
