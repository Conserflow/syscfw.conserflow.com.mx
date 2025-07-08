<?php

use Illuminate\Support\Facades\Route;

Route::group(["middleware" => "auth"], function ()
{
    // Articulos
    Route::get("almacen/articulos/obtener", "ArticuloController@ObtenerArticulosServer");
    Route::get("almacen/entradas/ocpendientes", "Almacen\EntradasRestantesController@OCPendientes");

    // Reportes
    Route::get("almacen/reportes/retornosgeneral", "EntradaSalidaController@ReporteRetornos");
    Route::get("almacen/ep/reporte", "EntradasPendientesController@Reporte");

    // Partidas Salida
    Route::get("almacen/salidas/partidasserver/{id}", "PartidasSalidasController@ObtenerPartidasServer");

    // Reportes Contabilidad
    Route::get("alamcen/reportes/inventariocontabilidad/{anio}/{mes}", "Almacen\ReportesContabilidadController@Salidas");
    Route::get("alamcen/reportes/existenciascontabilidad/{anio}/{mes}", "Almacen\ReportesContabilidadController@Existencias");

    // Entradas
    Route::get("almacen/entradas/{id}", "EntradaController@obtenerocs");
});
