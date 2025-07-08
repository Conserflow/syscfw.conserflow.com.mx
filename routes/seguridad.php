<?php

use Illuminate\Support\Facades\Route;

Route::get("seguridad/entregaepp/reporteconsumo", "Seguridad\ReporteConsumoController@Reporte");
Route::group(["middleware" => "auth"], function ()
{
    // Platicas
    Route::get("seguridad/platicas/obtenerplaticas", "Seguridad\PlaticasController@ObtenerPlaticas");
    Route::get("seguridad/platicas/descargarplantilla/{id}", "Seguridad\PlaticasController@DescargarPlantilla");
    Route::get("seguridad/platicas/descargarevidencia/{id}", "Seguridad\PlaticasController@DescargarEvidencia");
    Route::post("seguridad/platicas/guardar", "Seguridad\PlaticasController@Guardar");
    Route::post("seguridad/platicas/subirevidencia", "Seguridad\PlaticasController@SubirEvidencia");
    // Pruebas de acoholimetría
    Route::get("seguridad/pruebasalcohol/obtenerpruebas", "Seguridad\AlcoholimetriaController@ObtenerPruebas");
    Route::post("seguridad/pruebasalcohol/guardar", "Seguridad\AlcoholimetriaController@Guardar");
    Route::post("seguridad/pruebasalcohol/subirevidencia", "Seguridad\AlcoholimetriaController@SubirEvidencia");
    Route::get("seguridad/pruebasalcohol/descargarevidencia/{id}", "Seguridad\AlcoholimetriaController@DescargarEvidencia");
    Route::get("seguridad/pruebasalcohol/descargarplantilla/{id}", "Seguridad\AlcoholimetriaController@DescargarPlantilla");

    // Inspección de EPP
    Route::get("seguridad/inspeccionepp/obtener", "Seguridad\InspeccionEppController@Obtener");
    Route::get("seguridad/inspeccionepp/participantes/obtener/{i_id}", "Seguridad\InspeccionEppController@ObtenerParticipantes");
    Route::post("seguridad/inspeccionepp/guardar", "Seguridad\InspeccionEppController@Guardar");
    Route::post("seguridad/inspeccionepp/participantes/guardar", "Seguridad\InspeccionEppController@GuardarParticipante");
    Route::get("seguridad/inspeccionepp/descargar/{id}", "Seguridad\InspeccionEppController@Descargar");

    // Inspección botiquines
    Route::get("seguridad/inspeccionbotiquin/obtener", "Seguridad\InspeccionBotiquinController@Obtener");
    Route::post("seguridad/inspeccionbotiquin/guardar", "Seguridad\InspeccionBotiquinController@Guardar");
    Route::get("seguridad/inspeccionbotiquin/botiquines/obtener/{i_id}", "Seguridad\InspeccionBotiquinController@ObtenerBotiquines");
    Route::post("seguridad/inspeccionbotiquin/botiquines/guardar", "Seguridad\InspeccionBotiquinController@GuardarBotiquin");
    Route::get("seguridad/inspeccionbotiquin/descargar/{i_id}", "Seguridad\InspeccionBotiquinController@Descargar");

    // Entrega de EPP
    Route::post("seguridad/entregaepp/autorizar", "ValeEppController@Autorizar");
    Route::post("seguridad/entregaepp/autorizar", "ValeEppController@Autorizar");

    // Proyectos para permisos
    Route::get("seguridad/permisotrabajo/obtenerproyectos", "SeguridadController@ObtenerProyectosFolios");
    Route::get("seguridad/permisotrabajo/obtenerfolios", "SeguridadController@ObtenerFoliosPermisos");

    // Folios Proyectos
    Route::get("seguridad/folios_permisos/obtener", "Seguridad\FoliosProyectosController@ObtenerFolios");
    Route::post("seguridad/folios_permisos/guardar", "Seguridad\FoliosProyectosController@GuardarFolio");
});
