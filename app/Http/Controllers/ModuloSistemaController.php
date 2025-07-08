<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Utilidades;
use Illuminate\Http\Request;
use App\ModuloSistema;
use Illuminate\Support\Facades\Validator;

class ModuloSistemaController extends Controller
{
    protected $rules = array(
        'nombre' => 'required|max:45',
    );

    public function index(Request $request)
    {
        $modulos = ModuloSistema::orderBy('id', 'ASC')->get()->toArray();
        return response()->json($modulos);
    }

    public function store(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');

            $this->rules['nombre'] = 'required|unique:modulos,nombre,0,id|max:45';
            $validator = Validator::make($request->all(), $this->rules);

            if ($validator->fails())
            {
                return response()->json(array(
                    'status' => false,
                    'errors' => $validator->errors()->all()
                ));
            }

            $modulo = new ModuloSistema();
            $modulo->nombre = $request->nombre;
            $modulo->color = $request->color;
            $modulo->icono = $request->icono;
            $modulo->submenu = $request->submenu;
            $modulo->condicion = '1';
            $modulo->page = $request->page;
            Utilidades::auditar($modulo, $modulo->id);
            $modulo->save();

            return response()->json(array(
                'status' => true
            ));
        }
        catch (\Throwable $e)
        {
            Utilidades::errors($e);
        }
    }

    public function update(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');

            $this->rules['nombre'] = 'required|unique:modulos,nombre,' . $request->id . ',id|max:45';
            $validator = Validator::make($request->all(), $this->rules);

            if ($validator->fails())
            {
                return response()->json(array(
                    'status' => false,
                    'errors' => $validator->errors()->all()
                ));
            }

            $modulo = ModuloSistema::findOrFail($request->id);
            $modulo->fill($request->all());
            /*$modulo->nombre = $request->nombre;
            $modulo->color = $request->color;
            $modulo->icono = $request->icono;
            $modulo->submenu = $request->submenu;
            $modulo->condicion = '1';
            $modulo->page = '/';*/
            Utilidades::auditar($modulo, $modulo->id);
            $modulo->save();

            return response()->json(array(
                'status' => true
            ));
        }
        catch (\Throwable $e)
        {
            Utilidades::errors($e);
        }
    }
}
