<?php

use Illuminate\Support\Facades\Route;

Route::group(["middleware" => "auth"], function ()
{
    // Datos de empleados
    Route::get("enfermeria/empleados/obtener", "Enfermeria\DatosEmpleadosController@ObtenerEmpleados");

    // Catalogos
    Route::get("enfermeria/catalogos/motivoatencion/obtener", "Enfermeria\MotivosAtencionController@ObtenerMotivoAtencion");
    Route::post("enfermeria/catalogos/motivoatencion/guardar", "Enfermeria\MotivosAtencionController@GuardarMotivoAtencion");

    // Atencion Medica
    Route::get("enfermeria/atencionmedica/obtener", "Enfermeria\AtencionMedicaController@ObtenerAtenciones");
    Route::post("enfermeria/atencionmedica/guardar", "Enfermeria\AtencionMedicaController@GuardarAtencion");
    Route::get("enfermeria/yolo", "Enfermeria\AtencionMedicaController@Yolo");
    Route::get("enfermeria/reportes/fecha/{anio}/{mes}", "Enfermeria\ReportesController@ObtenerPorFecha");
    Route::get("enfermeria/reportes/obtenerdptos", "Enfermeria\ReportesController@ObtenerDeptos");
    Route::get("enfermeria/reportes/departamentos/{id}", "Enfermeria\ReportesController@ObtenerPorDepartamento");
    
    // Covid
    Route::post("enfermeria/registrocovid/guardar", "Enfermeria\RegistroCovidController@GuardarRegistroCovid");
    Route::get("enfermeria/registrocovid/obtener", "Enfermeria\RegistroCovidController@ObtenerRegistroCovid");
    Route::get("enfermeria/registrocovid/descargar", "Enfermeria\RegistroCovidController@GenerarReporte");

    // Incapacidad
    Route::post("enfermeria/incapacidad/guardar", "Enfermeria\IncapacidadController@GuardarIncapacidad");
    Route::get("enfermeria/incapacidad/obtener", "Enfermeria\IncapacidadController@ObtenerIncapacidad");
    Route::get("enfermeria/incapacidad/descargar", "Enfermeria\IncapacidadController@Descargar");
});
