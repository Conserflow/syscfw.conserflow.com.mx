<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth', "prefix" => "salidassgi"], function ()
{
    Route::get("salidanc/departamentos", "SGI\SalidasSGIController@DepartamentosSalidas");
    Route::get("salidanc/descargar/{id}", "SGI\SalidasSGIController@Descargar");
    Route::get("salidanc/bitacora/{anio}", "SGI\SalidasSGIController@DescargarBitacora");
    Route::get("salidanc/{anio}", "SGI\SalidasSGIController@Obtener");
    Route::apiResource("salidanc", "SGI\SalidasSGIController");
});
