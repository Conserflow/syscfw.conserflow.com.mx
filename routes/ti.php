<?php

use Illuminate\Support\Facades\Route;

Route::group(["middleware" => "auth"], function ()
{

    //FIXME: Cambiar 
    Route::post('get/material/ti/descripcion/programa', 'TI\TIController@getPorTipoPrograma');
    // Accesorios
    Route::get("ti/inventario/accesorios/obtener", "TI\TiAccesoriosController@Obtener");
    Route::get("ti/inventario/accesorios/obtener/activos", "TI\TiAccesoriosController@ObtenerActivos");
    Route::post("ti/inventario/accesorios/guardar", "TI\TiAccesoriosController@Guardar");
    Route::post("ti/inventario/accesorios/activar", "TI\TiAccesoriosController@Guardar");
    Route::get("ti/inventario/accesorios/eliminar/{id}", "TI\TiAccesoriosController@Eliminar");

    // Computo
    Route::get("/ti/inv/equipos/obtener/{id}", "TI\TiComputoController@Obtener");
    Route::post("/ti/inv/equipos/registrar", "TI\TiComputoController@Registrar");
    Route::post("/ti/inv/equipos/actualizar", "TI\TiComputoController@Actualizar");
    Route::post("/ti/inv/equipos/cambiarestado", "TI\TiComputoController@Activar");
    Route::get("ti/inv/equipos/descargar/inv/comp/{id}", "TI\TiComputoController@Descargar");
    Route::get("ti/inv/equipos/eliminar/{id}", "TI\TiComputoController@Eliminar");

    // Impresoras
    Route::get("ti/inventario/impresoras/obtener", "TI\TiImpresionController@Obtener");
    Route::post("ti/inventario/impresoras/guardar", "TI\TiImpresionController@Guardar");
    Route::post("ti/inventario/impresoras/activar", "TI\TiImpresionController@Activar");
    Route::get("ti/inventario/impresoras/eliminar/{id}", "TI\TiImpresionController@Eliminar");

    // Video
    Route::get("ti/inventario/video/obtener", "TI\TiVideoController@Obtener");
    Route::post("ti/inventario/video/guardar", "TI\TiVideoController@Guardar");
    Route::post("ti/inventario/video/activar", "TI\TiVideoController@Activar");
    Route::post("ti/inventario/video/eliminar", "TI\TiVideoController@Eliminar");

    // Red
    Route::get('ti/inventario/red/obtener', 'TI\TiRedController@Obtener');
    Route::post('ti/inventario/red/guardar', 'TI\TiRedController@Guardar');
    Route::post('ti/inventario/red/activar', 'TI\TiRedController@Activar');
    Route::get('ti/inventario/red/eliminar/{id}', 'TI\TiRedController@Eliminar');

    // Historico
    Route::get("ti/historico/obtener", "TI\HistoricoServicioTIController@Obtener");
    Route::post("ti/historico/guardar", "TI\HistoricoServicioTIController@Guardar");
    Route::post("ti/historico/eliminar", "TI\HistoricoServicioTIController@Eliminar");
    Route::get("ti/historico/descargar/{anio}", "TI\HistoricoServicioTIController@Descargar");

    // Requisitos por puesto
    Route::get("ti/matrizrequisitos/obtener", "TI\MatrizRequisitosController@ObtenerMatrizRequisitos");
    Route::post("ti/matrizrequisitos/guardar", "TI\MatrizRequisitosController@GuardarMatrizRequisitos");
    Route::get("ti/matrizrequisitos/descargar", "TI\MatrizRequisitosController@Descargar");

    // Vales de resguardo
    Route::get("ti/resguardos/obtener/{empresa}", "TI\ValesResguardoController@ObtenerVales");
    Route::get("ti/resguardos/obtenerequipos/{tipo_equipo}", "TI\ValesResguardoController@ObtenerEquipos");
    Route::get("ti/resguardos/obteneraccesorios", "TI\ValesResguardoController@ObtenerAccesorios");
    Route::post("ti/resguardos/guardar", "TI\ValesResguardoController@GuardarResguardo");
    Route::post("ti/resguardos/actualizar", "TI\ValesResguardoController@ActualizarResguardo");
    Route::get("ti/resguardos/descagar/{id}", "TI\ValesResguardoController@DescargarValeResguardo");
    Route::post("ti/resguardos/regresar", "TI\ValesResguardoController@RegresarResguardo");
    Route::post("ti/resguardo/autorizar", "TI\ValesResguardoController@Autorizar");
    Route::get("ti/vales/descargar", "TI\ValesResguardoController@DescargarTodos");

    // Vale de sitio
    Route::get("ti/sitio/obtener/{id}", "TI\ValesSitioController@ObtenerSalidasSitio");
    Route::get("ti/sitio/obtenerpartidas/{id}", "TI\ValesSitioController@ObtenerEquiposSitio");
    Route::post("ti/sitio/guardar", "TI\ValesSitioController@GuardarValeSitio");
    Route::get("ti/sitio/obtenerpendeintes/{id}", "TI\ValesSitioController@ObtenerPendientes");
    Route::get("ti/sitio/regresarpartida/{id}", "TI\ValesSitioController@RegresarPartidaSitio");
    Route::get("ti/sitio/descargar/{id}", "TI\ValesSitioController@DescargarValeSitio");

    // Programa anual de mtto
    Route::get("ti/programanamtto/obtenerprogramas/{id}", "TI\ProgramaTIController@getInicial");
    Route::get("ti/programanamtto/obtenerequipos{id}", "TI\ProgramaTIController@getDetalle");
    Route::get("ti/programanamtto/eliminar/{id}", "TI\ProgramaTIController@delete");
    Route::post("ti/programanamtto/guardar", "TI\ProgramaTIController@guardar");
    Route::put("ti/programanamtto/actualizar", "TI\ProgramaTIController@actualizar");
    Route::get("ti/programanamtto/descargar/{id}", "TI\ProgramaTIController@descargar");
    Route::post('ti/programanamtto/obtenerequipo', 'TI\ProgramaTIController@getPorTipoPrograma');

    // Mtto Impresoras
    Route::get("ti/mtto/impresoras/obtener", "TI\Mtto\ImpresorasController@ObtenerImpresoras");
    Route::post("ti/mtto/impresoras/guardar", "TI\Mtto\ImpresorasController@GuardarMtto");
    Route::get("ti/mtto/impresoras/historial/{id}", "TI\Mtto\ImpresorasController@Historial");

    // Respaldo de información
    Route::get('get/data/bitacora/resguardo/info', 'TI\ReguardoInfoTIController@get');
    Route::post('guardar/data/bitacora/resguardo/info', 'TI\ReguardoInfoTIController@guardar');
    Route::put('actualizar/data/bitacora/resguardo/info', 'TI\ReguardoInfoTIController@actualizar');
    Route::get('descargar/data/bitacora/resguardo/info/{anio}', 'TI\ReguardoInfoTIController@descargar');

    // Mantenimiento de TI
    Route::get("ti/mtto/preventivo/obtenerconsumibles", "TI\TiMantenimientoController@ObtenerConsumibles");
    Route::get("ti/mtto/preventivo/obtenerequipos/{t_id}", "TI\TiMantenimientoController@ObtenerEquipos");
    Route::get("ti/mtto/preventivo/obtenerpersonal", "TI\TiMantenimientoController@ObtenerPersonal");
    Route::get("ti/mtto/preventivo/obtener", "TI\TiMantenimientoController@ObtenerMttos");
    Route::get("ti/mtto/preventivo/reporte/{id}", "TI\TiMantenimientoController@GenerarReporte");
    Route::post("ti/mtto/preventivo/guardar", "TI\TiMantenimientoController@Guardar");
    Route::post("ti/mtto/preventivoregistrarconsumible", "TI\TiMantenimientoController@RegistarConsumible");

    // Propuesta Equipo TI
    Route::get("/ti/propuestaequipo/obtener", "TI\PropuestaEquipoController@Obtener");
    Route::get("/ti/propuestaequipo/empleados", "TI\PropuestaEquipoController@ObtenerEmpleados");
    Route::post("/ti/propuestaequipo/guadar", "TI\PropuestaEquipoController@Guardar");
});
