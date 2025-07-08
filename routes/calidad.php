<?php

use Illuminate\Support\Facades\Route;

Route::group(["middleware" => "auth"], function ()
{
    // Equpos de calibración
    // Almacen
    Route::get("calidad/calib/obtener", "Calidad\EquiposCalibracionController@index");
    Route::post("calidad/calib/guardar", "Calidad\EquiposCalibracionController@guardar");
    Route::put("calidad/calib/actualizar", "Calidad\EquiposCalibracionController@actualizar");
    Route::get("calidad/calib/pendientes", "Calidad\EquiposCalibracion2Controller@Dashboard");
    Route::get("calidad/calib/resguardos/{a_id}", "Calidad\EquiposCalibracionController@ObtenerResguardos");
    Route::post("calidad/calib/eliminar", "Calidad\EquiposCalibracionController@Eliminar");

    // Calidad
    Route::get("calidad/calib/equipos", "Calidad\EquiposCalibracionController@Obtener");
    Route::get("calidad/calib/descreporte", "Calidad\EquiposCalibracionController@DescargarReporte");

    // Equipos de calibracion 2
    Route::get("calidad/calibracion/equipos","Calidad\EquiposCalibracion2Controller@ObtenerEquipos");
    Route::post("calidad/calibracion/equipos","Calidad\EquiposCalibracion2Controller@GuardarEquipos");
    Route::post("calidad/calibracion/calibracion","Calidad\EquiposCalibracion2Controller@GuardarCalibraciones");
    Route::get("calidad/calibracion/certificado/{nombre}","Calidad\EquiposCalibracion2Controller@DescargarCertificado");
    Route::get("calidad/calibracion/equipos/descargar/{tipos}","Calidad\EquiposCalibracion2Controller@Reporte");
});
