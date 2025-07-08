<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function ()
{
    Route::get("generales/empleadoactivos", "General\GeneralController@ObtenerEmpleados");
    Route::get("generales/empleadosgenerales", "General\GeneralController@ObtenerTodosEmpleados");
    Route::get("generales/empleadospuestosactivos", "General\GeneralController@ObtenerEmpleadosPuestos");

    Route::get("generales/proyectos/{estado}", "General\GeneralController@ObtenerProyectos");

    Route::get("generales/vehiculos", "Vehiculos\CombustibleController@ObtenerUnidades");

    Route::get("generales/puestos", "General\GeneralController@ObtenerPuestos");
    Route::get("generales/proveedores/activos", "Compras\ProveedoresController@ProveedoresActivos");
    Route::get("generales/empleadoactual", "General\GeneralController@EmpleadoActual");
});
