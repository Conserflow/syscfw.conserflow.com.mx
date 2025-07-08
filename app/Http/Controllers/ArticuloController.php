<?php

namespace App\Http\Controllers;

use \App\Http\Helpers\Utilidades;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Articulo;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ArticuloController extends Controller
{
    /**
     * [Consulta en BD los registro de la tabla articulo con sus respectivas tablas recacionadas]
     * @return Response [Array de tipo JSON]
     */
    public function index()
    {
        $articulos = Articulo::select(
            'articulos.id',
            'articulos.id as item',
            'articulos.nombre',
            'articulos.codigo',
            'articulos.descripcion',
            'nombreproveedor',
            'articulos.marca',
            'articulos.unidad',
            'articulos.comentarios',
            'articulos.minimo',
            'articulos.maximo',
            'articulos.ficha_tecnica',
            'articulos.fotografia',
            'grupos.nombre as grupo',
            'categorias.nombre as categoria',
            'articulos.condicion',
            'categorias.id AS categoria_id',
            'grupos.id AS grupo_id',
            'tipo_calidad.id AS calidad_id',
            'tipo_calidad.nombre AS calidad',
            'tipo_calidad.descripcion as descal',
            'tipo_resguardo.id AS trid',
            'tipo_resguardo.nombre AS trnom',
            'articulos.centro_costo_id',
            "um_id"
        )
            ->leftJoin('grupos', 'grupos.id', '=', 'articulos.grupo_id')
            ->leftJoin('categorias', 'categorias.id', '=', 'grupos.categoria_id')
            ->leftJoin('tipo_calidad', 'tipo_calidad.id', '=', 'articulos.calidad_id')
            ->leftJoin('tipo_resguardo', 'tipo_resguardo.id', '=', 'articulos.tipo_resguardo_id')
            ->orderBy('articulos.id', 'asc')->get()->toArray();

        return response()->json($articulos);
    }

    /**
     * Aux Obtener los articulos para server-table
     */
    private function ObtenerArticulos()
    {
        $articulos = Articulo::select("articulos.*", "articulos.id as a_id");
        return $articulos;
    }

    /**
     * Aux Obtener los articulos para server-table
     */
    public function ObtenerArticulosServer()
    {
        extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn']));

        $data = $this->ObtenerArticulos();
        // $data = $this->listarTodosCompras();
        if (isset($query) && $query)
        {
            $data = $byColumn == 1 ?
                $this->busqueda_filterByColumn($data, $query) :
                $this->busqueda_filter($data, $query, $fields);
        }

        $count = $data->count();

        $data->limit($limit)
            ->skip($limit * ($page - 1));

        if (isset($orderBy))
        {
            $direction = $ascending == 1 ? 'ASC' : 'DESC';
            $data->orderBy($orderBy, $direction);
        }
        $results = $data->get();
        // $count = sizeof($results);

        return [
            'data' => $results,
            'count' => $count,
        ];
    }

    /**
     * [Guarda en BD los encabezados en la tabla articulo respetando las condiciones y reglas
     *  establecidas]
     * @param Request   $request [Objeto de datos del POST]
     * @return Response          [Array con estatus true]
     */
    public function store(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');
            if ($request->metodo == "Nuevo")
            {
                $articulo = new Articulo();
                $articulo->grupo_id = $request->grupo_id;
                $articulo->tipo_resguardo_id = $request->trid;
                $articulo->calidad_id = $request->calidad_id;
                $articulo->codigo = $request->codigo;
                $articulo->nombre = $request->nombre;
                $articulo->descripcion = $request->descripcion;
                // $articulo->nombreproveedor = $request->nombreproveedor;
                $articulo->marca = $request->marca;
                $articulo->unidad = $request->unidad;
                $articulo->um_id = $request->um_id;
                $articulo->comentarios = $request->comentarios;
                $articulo->minimo = $request->minimo;
                $articulo->maximo = $request->maximo;
                $articulo->condicion = '1';
                $articulo->centro_costo_id = $request->centro_costo_id;
                $articulo->empleado_registra_id = Auth::user()->empleado_id;
                Utilidades::auditar($articulo, $articulo->id);
                // $articulo->codigo_producto=$request->codigoProductoSat;
                $articulo->save();
                Auditoria::AuditarCambios($articulo);
            }
            else
            {
                $articulo = Articulo::findOrFail($request->id);
                $articulo->fill($request->all());
                Auditoria::AuditarCambios($articulo);
                $articulo->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los articulos");
        }
    }

    /**
     * [Consulta el archivo almacenado en el servidor]
     * @param  Int      $id [id del GET]
     * @return Response     [Array en formato JSON]
     */
    public function show($id)
    {
        $archivo = Utilidades::ftpSolucion($id);
        //--Devuelve la respuesta y descarga el archivo--//
        Storage::disk('descarga')->put($id, $archivo);

        return response()->download(storage_path() . '/app/descargas/' . $id);
    }

    /**
     * [Elimina el archivo almacenado en el servidor configurado]
     * @param  String $id [Nombre del archivo]
     * @return         [objeto de tipo descarga con la url del archivo]
     */
    public function edit($id)
    {
        //elimina de la ruta local el archivo descargado
        Storage::disk('descarga')->delete($id);
        Storage::disk('local')->delete($id);
    }

    /**
     * [activar Actualiza el campo condición a 1 de un registro en el modelo Articulo]
     * @param  Request $request [description]
     * @return Response           [description]
     */
    public function desactivar(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');
            $articulo = Articulo::findOrFail($request->id);
            $articulo->condicion = '0';
            Utilidades::auditar($articulo, $articulo->id);
            Auditoria::AuditarCambios($articulo);
            $articulo->update();
        }
        catch (\Throwable $e)
        {
            Utilidades::errors($e);
        }
    }
    /**
     * [desactivar Actualiza el campo condición a 1 de un registro en el modelo Articulo]
     * @param  Request $request [description]
     * @return Response           [description]
     */
    public function activar(Request $request)
    {
        try
        {
            if (!$request->ajax()) return redirect('/');
            $articulo = Articulo::findOrFail($request->id);
            $articulo->condicion = '1';
            Utilidades::auditar($articulo, $articulo->id);
            Auditoria::AuditarCambios($articulo);
            $articulo->update();
        }
        catch (\Throwable $e)
        {
            Utilidades::errors($e);
        }
    }
    /**
     * [Query del lado del servidor de el modelo Articulo]
     * @return Array [Array que contiene data y count]
     */
    public function busqueda()
    {
        extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn']));

        $data = Articulo::select(
            [
                'id', 'codigo', 'nombre', 'descripcion', 'marca', 'calidad_id', 'unidad', 'comentarios', 'minimo', 'maximo', 'tipo_resguardo_id', 'ficha_tecnica', 'fotografia', 'grupo_id', 'condicion', 'grupo_id', 'calidad_id'
            ]
        )->where('condicion', '=', '1')
            ->where(function ($q)
            {
                // $q->WhereNotNull("created_at")->whereRaw("date(created_at)>='2023-09-09'");
            });

        if (isset($query) && $query)
        {
            $data = $byColumn == 1 ?
                $this->busqueda_filterByColumn($data, $query) :
                $this->busqueda_filter($data, $query, $fields);
        }

        $count = $data->count();

        $data->limit($limit)
            ->skip($limit * ($page - 1));

        if (isset($orderBy))
        {
            $direction = $ascending == 1 ? 'ASC' : 'DESC';
            $data->orderBy($orderBy, $direction);
        }
        // leftJoin('tipo_calidad AS TC','TC.id','=','articulos.calidad_id')
        // ->
        $results = $this->tipoCalidad($data->get());

        return [
            'data' => $results,
            'count' => $count,
        ];
    }
    /**
     * [Consulta en la BD el nombre y descripcion del tipo de calidad]
     * @param Array  $data [Array recibido en la función]
     * @param String $arreglo_final  [Cadena concatenada]
     */
    public function tipoCalidad($data)
    {
        $arreglo_final = [];
        foreach ($data as $key => $value)
        {
            $tipo_calidad = \App\TipoCalidad::where('id', '=', $value->calidad_id)->first();
            $arreglo_final[] = array_merge($value->toArray(), [
                'calidad' => $tipo_calidad == null ? '' : $tipo_calidad->nombre,
                'descal' => $tipo_calidad == null ? '' : $tipo_calidad->descripcion
            ]);
        }
        return $arreglo_final;
    }

    protected function busqueda_filterByColumn($data, $queries)
    {
        $queries = json_decode($queries, true);

        foreach ($queries as $field => $query)
        {
            $_field = str_replace("a_", "", $field);
            $data->where($_field, 'LIKE', "%{$query}%");
        }
        return $data;
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

    /**
     * [Consulta en la BD las existencias por lote y stock]
     * @param  Request  $request [Objeto de datos del PUT]
     * @return Response          [ exitencias,lotes y stock]
     */
    public function existencias(Request $request)
    {
        $existencias = DB::table('existencias')
            ->select('existencias.*', 'almacenes.nombre AS almacen', 'stands.nombre AS stand', 'niveles.nombre AS nivel')
            ->join('almacenes', 'existencias.almacene_id', '=', 'almacenes.id')
            ->leftJoin('stands', 'existencias.stand_id', '=', 'stands.id')
            ->leftJoin('niveles', 'existencias.nivel_id', '=', 'niveles.id')
            ->where([
                ['articulo_id', '=', $request->articulo_id],
                ['cantidad', '>', '0']
            ])->get();

        $loteuno = DB::table('lote_almacen')
            ->where([
                ['articulo_id', '=', $request->articulo_id],
                ['cantidad', '>', '0']
            ])
            ->get();
        // tunderbird
        $lotedos = DB::table('lote_temporal')
            ->leftJoin('lote_almacen AS LA', 'LA.id', '=', 'lote_temporal.lote_almacen_id')
            ->select('LA.id', 'LA.caducidad', 'lote_temporal.comentario', 'lote_temporal.cantidad')
            ->where([
                ['lote_temporal.articulo_id', '=', $request->articulo_id],
                ['lote_temporal.cantidad', '>', '0']
            ])
            ->get();

        $lotes = $loteuno->merge($lotedos);

        //Obtener las articulos defectuosos en almacén
        $defectuosos = [];
        $defectuosos = DB::select(
            "
            select la.id,la.articulo_id ,la.id as lote, pr.cantidad_defectuoso as cantidad
            from lote_almacen la
            join partidas_retorno pr
            on pr.articulo_id =la.articulo_id
            where la.articulo_id =$request->articulo_id and pr.cantidad_defectuoso >0 "
        );


        $stocks = DB::table('stock_articulos')
            ->join('stocks', 'stock_articulos.stocke_id', '=', 'stocks.id')
            ->where([
                ['articulo_id', '=', $request->articulo_id],
                ['cantidad', '>', '0']
            ])
            ->get();

        return response()->json([
            'existencias' => $existencias->toArray(),
            'lotes' => $lotes->toArray(),
            'stocks' => $stocks->toArray(),
            "defectuosos"   => $defectuosos,
        ]);
    }

    /**
     * [Consulta los movimientos de los articulos]
     * @param  Request  $request [Objeto de datos del PUT]
     * @return Response          [Movimientos]
     */
    public function kardex(Request $request)
    {
        $movimientos = DB::table('movimientos')
            ->select('movimientos.*', 'almacenes.nombre AS almacen', 'lotes.nombre AS lote', 'stocks.nombre AS stock')
            ->leftJoin('almacenes', 'movimientos.almacene_id', '=', 'almacenes.id')
            ->leftJoin('lotes', 'movimientos.lote_id', '=', 'lotes.id')
            ->leftJoin('stocks', 'movimientos.stocke_id', '=', 'stocks.id')
            ->where('movimientos.articulo_id', '=', $request->get('articulo_id'))
            ->orderBy('movimientos.id', 'DESC')
            ->paginate(5);
        return response()->json([
            'movimientos' => $movimientos,
        ]);
    }
    /**
     * [Consulta los maximos]
     * @param  Request  $request [Objeto de datos del PUT]
     * @return Response          [resultado]
     */
    public function maximos(Request $request)
    {
        $resultado = DB::select('select * from (select id, nombre, codigo, descripcion, maximo from articulos where maximo is not null and maximo > 0 and condicion = 1) as t1 join (select articulo_id, sum(cantidad) as existencia from existencias group by articulo_id HAVING existencia > 0) as t2 on t1.id = t2.articulo_id WHERE existencia > maximo');
        return response()->json([
            'registros' => $resultado,
        ]);
    }
    /**
     * [Consulta los minimos]
     * @param  Request  $request [Objeto de datos del PUT]
     * @return Response          [resultado]
     */
    public function minimos(Request $request)
    {
        $resultado = DB::select('select * from (select id, nombre, codigo, descripcion, minimo from articulos where minimo is not null and minimo > 0 and condicion = 1) as t1 join (select articulo_id, sum(cantidad) as existencia from existencias group by articulo_id HAVING existencia > 0) as t2 on t1.id = t2.articulo_id WHERE existencia < minimo');
        return response()->json([
            'registros' => $resultado,
        ]);
    }
    /**
     * [Consulta los articulos proximos a caducar]
     * @param  Request  $request [Objeto de datos del PUT]
     * @return Response          [resultado,fecha y nueva fecha]
     */
    public function proximosCaducar(Request $request)
    {
        $fecha = date('Y-m-d');
        $nuevafecha = strtotime('+30 day', strtotime($fecha));
        $nuevafecha = date('Y-m-d', $nuevafecha);

        $resultado = DB::select("SELECT l.id, a.codigo, a.nombre, a.descripcion, l.cantidad, l.caducidad FROM lote_almacen AS l join articulos AS a ON l.articulo_id=a.id WHERE CANTIDAD > 0 AND (caducidad >= '$fecha' AND caducidad < '$nuevafecha')");

        return response()->json([
            'fecha_actual' => $fecha,
            'fecha_limite' => $nuevafecha,
            'registros' => $resultado,
        ]);
    }
    /**
     * [Consulta los articulos caducados]
     * @param  Request  $request [Objeto de datos del PUT]
     * @return Response          [resultado y fecha]
     */
    public function caducados(Request $request)
    {
        $fecha = date('Y-m-d');

        $resultado = DB::select("SELECT l.id, a.codigo, a.nombre, a.descripcion, l.cantidad, l.caducidad FROM lote_almacen AS l join articulos AS a ON l.articulo_id=a.id WHERE CANTIDAD > 0 AND caducidad < '$fecha'");

        return response()->json([
            'fecha_actual' => $fecha,
            'registros' => $resultado,
        ]);
    }

    public function getArt(Request $request)
    {
        try
        {
            $articulo = DB::table('articulos AS a')
                ->where('a.descripcion', 'LIKE', '%' . $request->articulo . '%')
                ->get();

            return response()->json($articulo);
        }
        catch (\Throwable $e)
        {
            Utilidades::errors($e);
        }
    }
}
