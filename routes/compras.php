<?php

use Illuminate\Support\Facades\Route;


Route::group(["middleware" => "auth"], function ()
{
    // Compras
    Route::get("compras/proyectos", "Compras\ProyectosController@GetProyectOC");
    Route::get("compras/ocscerradas/{p_id}", "Compras\ComprasController@ObtenerOCCorreccion");

    // Requis
    // FIXME: 
    Route::get("requisicionesrecibir", "Compras\ComprasController@Requisicionesrecibir");

    // FIXME: 
    Route::resource("compras", "Compras\ComprasController");
    Route::get("comprasobtener/{id}", "Compras\ComprasController@ConsultarTotal");
    Route::get("comprabusquedaimpuesto/{id}", "Compras\ComprasController@impuesto");
    Route::get("impuestoeliminar/{id}", "Compras\ComprasController@impuestoeliminar");

    // Ordenes de compras
    Route::get("compras/proveedores/todos", "Compras\ProveedoresController@ObtenerTodos");
    Route::post('compras/cerraroc', 'Compras\ComprasController@CerrarOC');
    // FIXME: 
    //Dashboard de compras
    // FIXME: Del
    Route::get("buscarocpf/proyecto/{id}", "Compras\ComprasController@buscarocpf");
    Route::post("buscar/articulo/oc", "Compras\ComprasController@compraArticulo");
    Route::post("buscar/articulo/lotes", "Compras\ComprasController@LoteArticulo");
    Route::post("buscar/articulo/requisicion", "Compras\ComprasController@requiArticulo");
    Route::post("buscar/articulo/salidas", "Compras\ComprasController@salidasArticulo");
    Route::post("buscar/proyecto/salidas", "Compras\ComprasController@salidasProyecto");

    // Reportes
    Route::get("compras/reporte/generalcompras/{id}", "Compras\ComprasController@ReportGeneral");

    // FIXME: 
    Route::resource("compras/{id}/compras", "Compras\ComprasController");
    Route::get("condicion_pago/ver", "Compras\ComprasController@condicionpago");
    Route::get("compras/busqueda/{id}", "Compras\ComprasController@busqueda");
    // Proveedores
    Route::resource("proveedores", "Compras\ProveedoresController");
    Route::post("compras/proveedores/activar", "Compras\ProveedoresController@Desactivar");
    Route::get("compras/proveedores/obtener/{anio}", "Compras\ProveedoresController@ObtenerProveedores");
    Route::get("compras/reportes/catalogoproveedores/{anio}", "Compras\ProveedoresController@DescargarReporte");
    Route::get("compras/reportes/proveedores2", "Compras\ProveedoresController@DescargarReporte2");
    Route::get("compras/proveedores/historial/{p_id}", "Compras\CambiosProveedoresController@ObtenerHistorial");
    Route::get("compras/proveedores/descargarhistorial/{h_id}", "Compras\CambiosProveedoresController@DescargarHistorial");
    Route::get("compras/ocsporanio/{anio}", "Compras\ProveedoresController@OCPorAnio");

    // Proveedores y Evaluación
    Route::get("proveedores/cargar", "Compras\ProveedoresController@Cargar");
    Route::get("compras/proveedores/bancarios/{id}", "Compras\ProveedoresController@getDataBankProveedor");

    // Evaluación de proveedores
    Route::get("compras/evlauacion/obtenerproveedores/{anio}", "Compras\EvaluacionProveedoresController@ObtenerProveedores");
    Route::get("compras/evaluacion/obtener/{id}", "Compras\EvaluacionProveedoresController@ObtenerEvaluacion");
    Route::post("compras/evaluacion/guardar", "Compras\EvaluacionProveedoresController@GuardarEvaluacion");
    Route::get("compras/evaluacion/descargar/{id}", "Compras\EvaluacionProveedoresController@DescargarEvaluacion");
    Route::get("compras/evaluacion/descargarreporte/{anio}", "Compras\EvaluacionProveedoresController@DescargarReporte");

    // Catálogo de compras
    Route::resource("estadocompra", "Compras\EstadoCompraController");
    Route::resource("catservicios", "Compras\CatServiciosController");
    Route::resource("catmantenimientovehiculos", "Compras\CatManVehiculosController");
    Route::get("catservicio/busqueda", "Compras\CatServiciosController@busqueda");
});
