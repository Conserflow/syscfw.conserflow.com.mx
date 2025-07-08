<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function ()
{
    Route::get("tesoreria/catalogos/unidades/obtener", "Tesoreria\SatCatUnidadesController@ObtenerSatCatUnidades");
    Route::post("tesoreria/catalogos/unidades/guardar", "Tesoreria\SatCatUnidadesController@GuardarSatCatUnidades");

    Route::post("facturacion/cdfi4/timbrar", "Tesoreria\FacturacionV4Controller@Timbrar");
    Route::post("facturacion/cdfi4/descargarxmlprueba", "Tesoreria\FacturacionV4Controller@DescargarXMLPrueba");
    Route::post("facturacion/cdfi4/cancelar", "Tesoreria\FacturacionV4Controller@Cancelar");
    Route::get("facturacion/cdfi4/descargar/{id}", "Tesoreria\FacturacionV4Controller@DescargarFactura");

    // Observaciones de factura
    Route::post("facturacion/partidas/observaciones", "PartidasFacturaController@CambiarObservaciones");
});
