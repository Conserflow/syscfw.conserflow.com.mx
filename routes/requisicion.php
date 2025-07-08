<?php

use Illuminate\Support\Facades\Route;

Route::group(["middleware" => "auth"], function ()
{
    Route::prefix("requisiciones")->group(function ()
    {
        // General
        Route::get("descargar/{id}", "Requisiciones\DescargarController@descargar");
        Route::get("personalaprueba", "Requisiciones\PersonalApruebaController@index");
        Route::post("requisicion/eliminar", "Requisiciones\RequisicionesController@eliminar");
        Route::post("requisicion/cerrar", "Requisiciones\RequisicionesController@cerrar");
        Route::resource("requisicion", "Requisiciones\RequisicionesController");
        Route::resource("tipos", "Requisiciones\TipoRequisicionController");

        // Material principal
        Route::resource("materiales", "Requisiciones\PartidasMaterialesController");
        Route::resource("materiales/partidas", "Requisiciones\PartidasMaterialesController");

        // Unidades de medida
        Route::get("unidadesmedida", "UnidadesMedidaController@index");

        Route::prefix("almacen")->group(function ()
        {
            Route::get("/", "Requisiciones\AlmacenController@index");
            Route::put("/{id}", "Requisiciones\AlmacenController@update");
            Route::get("/{id}", "Requisiciones\AlmacenController@show");
            Route::post("rechazar", "Requisiciones\AlmacenController@rechazar");
            Route::post("aprobar", "Requisiciones\AlmacenController@aprobar");
        });

        Route::prefix("proyectos")->group(function ()
        {
            Route::get("/", "Requisiciones\ProyectosController@index");
            Route::put("/{id}", "Requisiciones\ProyectosController@update");
            Route::get("/{id}", "Requisiciones\ProyectosController@show");
            Route::post("rechazar", "Requisiciones\ProyectosController@rechazar");
            Route::post("aprobar", "Requisiciones\ProyectosController@aprobar");
        });
    });
});
