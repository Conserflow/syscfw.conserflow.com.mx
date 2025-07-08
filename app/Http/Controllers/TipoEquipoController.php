<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TipoEquipo;
use \App\Http\Helpers\Utilidades;
use Illuminate\Support\Facades\Validator;

class TipoEquipoController extends Controller
{
    protected $rules = array(
        'nombre' => 'required|max:255',
    );

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        $tipoEquipo = TipoEquipo::select('tipo_equipo.*')
            ->orderBy('tipo_equipo.id', 'ASC')->get();
        return response()->json($tipoEquipo);
    }

    public function store(Request $request)
    {
        try
        {


            if (!$request->ajax()) return redirect('/');

            $this->rules['nombre'] = 'required|unique:accesorio,tipo,0,id|max:255';
            $validator = Validator::make($request->all(), $this->rules);

            if ($validator->fails())
            {
                return response()->json(array(
                    'status' => false,
                    'errors' => $validator->errors()->all()
                ));
            }

            $tipoEquipo = new TipoEquipo();
            $tipoEquipo->nombre = $request->nombre;
            Utilidades::auditar($tipoEquipo, $tipoEquipo->id);
            $tipoEquipo->save();

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

            $validator = Validator::make($request->all(), $this->rules);

            if ($validator->fails())
            {
                return response()->json(array(
                    'status' => false,
                    'errors' => $validator->errors()->all()
                ));
            }

            $tipoEquipo = TipoEquipo::findOrFail($request->id);
            $tipoEquipo->nombre = $request->nombre;
            //Utilidades::auditar($accesorio, $empresas->id.','.$empresas->nombre);
            Utilidades::auditar($tipoEquipo, $tipoEquipo->id);
            $tipoEquipo->save();

            return response()->json(array(
                'status' => true,
            ));
        }
        catch (\Throwable $e)
        {
            Utilidades::errors($e);
        }
    }
    protected function busqueda_filter($data, $query, $fields)
    {
        return $data->where(function ($q) use ($query, $fields)
        {
            foreach ($fields as $index => $field)
            {
                $method = $index ? 'orWhere' : 'where';
                $q->{$method}($field, 'LIKE', "%{$query}%");
            }
        });
    }
}
