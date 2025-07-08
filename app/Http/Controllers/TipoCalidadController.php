<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Utilidades;
use Illuminate\Http\Request;
use App\TipoCalidad;
use Illuminate\Support\Facades\Validator;

class TipoCalidadController extends Controller
{
    protected $rules = array(
        'nombre' => 'required|max:45',
    );

    public function index(Request $request)
    {
        $tiposCalidad = TipoCalidad::orderBy('id', 'desc')->get()->toArray();
        return response()->json($tiposCalidad);
    }
    public function show($id)
    {
        // code...
        $tiposCalidad = TipoCalidad::where('id', '=', $id)->first();
        return response()->json($tiposCalidad);
    }
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $this->rules['nombre'] = 'required|unique:tipo_calidad,nombre,0,id|max:45';
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails())
        {
            return response()->json(array(
                'status' => false,
                'errors' => $validator->errors()->all()
            ));
        }

        $tipoCalidad = new TipoCalidad();
        $tipoCalidad->nombre = $request->nombre;
        $tipoCalidad->descripcion = $request->descripcion;
        Utilidades::auditar($tipoCalidad, $tipoCalidad->id);
        $tipoCalidad->save();

        return response()->json(array(
            'status' => true
        ));
    }

    public function update(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');

            $this->rules['nombre'] = 'required|unique:tipo_calidad,nombre,' . $request->id . ',id|max:45';
            $validator = Validator::make($request->all(), $this->rules);

            if ($validator->fails())
            {
                return response()->json(array(
                    'status' => false,
                    'errors' => $validator->errors()->all()
                ));
            }

            $tipoCalidad = TipoCalidad::findOrFail($request->id);
            $tipoCalidad->nombre = $request->nombre;
            $tipoCalidad->descripcion = $request->descripcion;
            Utilidades::auditar($tipoCalidad, $tipoCalidad->id);
            $tipoCalidad->save();

            return response()->json(array(
                'status' => true
            ));
        }
        catch (\Throwable $e)
        {
            Utilidades::errors($e);
        }
    }

    public function getList(Request $request)
    {
        $tiposCalidad = TipoCalidad::select('id', 'nombre')->orderBy('id', 'desc')->get()->toArray();
        return response()->json($tiposCalidad);
    }
}
