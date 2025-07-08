<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function ()
{
    Route::post("sgi/procedimientos/directorios", "SGI\ProcedimientosController@ObtenerDirectorios");
});
