<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Utilidades;
use Illuminate\Http\Request;
use App\SatCatFormaPago;
use Illuminate\Support\Facades\Validator;

class SatCatFormapagoController extends Controller
{
    /**
     * [Definición de las reglas para ser utilizadas en la actualización y la inserción]
     */
    protected $rules = array(
        'clave' => 'required|max:2',
        'descripcion' => 'required|max:150',
    );

    /**
     * [Consulta en BD los registros de la tabla sat_cat_formapago]
     * @param  Request  $request [Url del GET]
     * @return Response          [Array de tipo JSON]
     */
    public function index(Request $request)
    {
        $formaPago = SatCatFormaPago::orderBy('id', 'desc')->get()->toArray();
        return response()->json($formaPago);
    }

    /**
     * [Guarda en BD los registros en la tabla sat_cat_formapago respetando las reglas establecidas]
     * @param  Request  $request [Objeto de datos del POST]
     * @return Response          [Array con estatus true]
     */
    public function store(Request $request)
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

        $formaPago = new SatCatFormaPago();
        $formaPago->clave = $request->clave;
        $formaPago->descripcion = $request->descripcion;
        Utilidades::auditar($formaPago, $formaPago->id);
        $formaPago->save();

        return response()->json(array(
            'status' => true
        ));
    }

    /**
     * [Actualiza en BD los registros de la tabla sat_cat_formapago respetando las reglas establecidas]
     * @param  Request  $request [Objeto de datos del PUT]
     * @return Response          [Array con estatus true]
     */
    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $formaPago = SatCatFormaPago::findOrFail($request->id);
        $formaPago->clave = $request->clave;
        $formaPago->descripcion = $request->descripcion;
        Utilidades::auditar($formaPago, $formaPago->id);
        $formaPago->save();

        return response()->json(array(
            'status' => true
        ));
    }
}
