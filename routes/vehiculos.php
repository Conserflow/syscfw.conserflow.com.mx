<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function ()
{
    // Unidades
    Route::resource("UnidadesStore", "Vehiculos\UnidadesController");
    Route::get("vehiculos/unidades/obtener/{emp}", "Vehiculos\UnidadesController@Obtener");
    Route::post("vehiculos/unidades/desactivar", "Vehiculos\UnidadesController@Eliminar");
    Route::post("vehiculos/unidades/prestamo", "Vehiculos\UnidadesController@Prestamo");
    Route::get("vehiculos/unidades/prestamos/detalles/{data}", "Vehiculos\UnidadesController@DetallesPrestamo");
    Route::get("vehiculos/unidades/catalogostrafico", "Vehiculos\UnidadesController@Trafico");
    Route::get("vehiculos/catalogos/clasetipo", "Vehiculos\UnidadesController@ObtenerClasetipo");
    Route::get("vehiculos/catalogos/combustibles", "Vehiculos\UnidadesController@ObtenerCombustibles");
    // Comprobar existencia
    Route::get("vehiculos/unidades/unidadesviaticos", "Vehiculos\UnidadesController@UnidadesViaticos");
    // Reporte inventario
    Route::get("vehiculos/unidades/inventario/{emp_id}", "Vehiculos\UnidadesController@DescargarReporte");

    // Conductores
    Route::get("vehiculos/conductores/obtener", "Vehiculos\ConductoresController@index");
    Route::post("vehiculos/conductores/guardar", "Vehiculos\ConductoresController@create");
    Route::post("vehiculos/conductores/eliminar", "Vehiculos\ConductoresController@Eliminar");
    Route::get("vehiculos/conductores/licencia/{nombre}", "Vehiculos\ConductoresController@descargar");
    Route::get("vehiculos/conductores/del_temp/{nombre}", "Vehiculos\ConductoresController@editar");

    // Mantenimiento
    Route::get("vehiculos/mttos/obtenermttos/{id}", "Vehiculos\MantenimientoUnidadesController@Mttos");
    Route::get("vehiculos/mttos/obtenermtto/{id}", "Vehiculos\MantenimientoUnidadesController@ObtenerMtto");
    Route::resource("mantenimientounidades", "Vehiculos\MantenimientoUnidadesController");
    Route::resource("serviciounidades", "Vehiculos\ServicioUnidadesController");

    // Solicutud
    Route::get("vehiculos/solicitud/clasetipo", "Vehiculos\UnidadesController@ObtenerClasetipo");
    Route::get("vehiculos/solicitud/obtener/{e_id}", "Vehiculos\SolicitudVehiculoController@Obtener");
    Route::post("vehiculos/solicitud/guardar", "Vehiculos\SolicitudVehiculoController@Guardar");
    Route::get("vehiculos/solicitud/obtenerresponsables/{id}", "Vehiculos\SolicitudVehiculoController@ObtenerResponsables");
    Route::get("vehiculos/solicitud/portipo/{data}", "Vehiculos\SolicitudVehiculoController@UnidadesPorTipo");
    Route::get("vehiculos/solicitud/descargar/{id}", "Vehiculos\SolicitudVehiculoController@GenerarReporte");

    // Vales de resguardo
    Route::get("vehiculos/valeresguardo/obtener/{emp}", "Vehiculos\ValeSoliVehiculoController@Obtener");
    Route::get("vehiculos/valeresguardo/obenersolicitudes/{emp}", "Vehiculos\ValeSoliVehiculoController@ObtenerSolicitudes");
    Route::get("vehiculos/valeresguardo/obenerpolizas/{u_id}", "Vehiculos\ValeSoliVehiculoController@ObtenerPolizas");
    Route::post("vehiculos/valeresguardo/guardar", "Vehiculos\ValeSoliVehiculoController@Guardar");
    Route::get("vehiculos/valeresguardo/descargar/{id}", "Vehiculos\ValeSoliVehiculoController@Descargar");

    // Mantenimiento anual
    Route::get("/vehiculos/mttoanual/obtener/{dts}", "Vehiculos\VehiculosMttoAnualControlller@ObtenerMtto");
    Route::get("/vehiculos/mttoanual/obteneranios/{empresa}", "Vehiculos\VehiculosMttoAnualControlller@ObtenerAnios");
    Route::get("/vehiculos/mttoanual/obtenerunidades/{e_id}", "Vehiculos\VehiculosMttoAnualControlller@ObtenerUnidades");
    Route::post("/vehiculos/mttoanual/guardar", "Vehiculos\VehiculosMttoAnualControlller@Guardar");
    Route::get("/vehiculos/mttoanual/descargar/{dts}", "Vehiculos\VehiculosMttoAnualControlller@Descargar");

    // Mantenimientos vehicular
    Route::get("mantenimiento/vehiculo/get", "Vehiculos\VehiculosMantenimientoController@getAll");
    Route::get("mantenimiento/vehiculo/get/{id}", "Vehiculos\VehiculosMantenimientoController@getById");
    Route::post("mantenimiento/vehiculo/guardar", "Vehiculos\VehiculosMantenimientoController@store");
    Route::put("mantenimiento/vehiculo/actualizar", "Vehiculos\VehiculosMantenimientoController@update");
    Route::get("mantenimiento/vehiculo/eliminar/{id}", "Vehiculos\VehiculosMantenimientoController@delete");
    Route::get("mantenimiento/vehiculo/descargar/{id}", "Vehiculos\VehiculosMantenimientoController@Descargar");

    // Polizas
    Route::resource("vehiculos/polizas/unidades", "Vehiculos\PolizaUnidadesController");
    Route::get("vehiculos/polizas/unidades/descargar/{id}", "Vehiculos\PolizaUnidadesController@descarga");
    Route::post("vehiculos/polizas/eliminar", "Vehiculos\PolizaUnidadesController@EliminarPoliza");
    Route::get("polizaeditar/{id}", "Vehiculos\PolizaUnidadesController@editar");

    // Verificaciones
    Route::resource("vehiculos/verificaciones", "Vehiculos\VerificacionUnidadesController");
    Route::get("vehiculos/verificaciones/descargar/{id}", "Vehiculos\VerificacionUnidadesController@descarga");
    Route::get("verificacioneditar/{id}", "Vehiculos\VerificacionUnidadesController@editar");

    // Tenencia
    Route::resource("vehiculos/tenencia", "Vehiculos\TenenciaUnidadesController");
    Route::get("tenenciaunidadescarga/{id}", "Vehiculos\TenenciaUnidadesController@descarga");
    Route::get("tenenciaeditar/{id}", "Vehiculos\TenenciaUnidadesController@editar");

    // Combustible
    Route::post('vehiculos/combustible/guardar', 'Vehiculos\CombustibleController@guardar');
    Route::put('vehiculos/combustible/actualizar', 'Vehiculos\CombustibleController@actualizar');
    Route::get('vehiculos/combustible/obtenerunidades', 'Vehiculos\UnidadesController@UnidadesParaCombustible');
    Route::get('vehiculos/combustible/eliminar/{id}', 'Vehiculos\CombustibleController@eliminar');
    Route::get('vehiculos/combustible/obtener/{emp}', 'Vehiculos\CombustibleController@Obtener');
    Route::get('vehiculos/combustible/obtenerimg/{id}', 'Vehiculos\CombustibleController@getImg');
    Route::get('vehiculos/combustible/borrarimg/{id}', 'Vehiculos\CombustibleController@deleteImg');
    Route::get("vehiculos/combustible/reporte/{data}", "Vehiculos\CombustibleController@Reporte");

    // Entrega/Recepción
    Route::post("entrega/vehiculos/guardar/", "Vehiculos\TraficoEntregaRecepcionController@Guardar");
    Route::put("entrega/vehiculos/actualizar/", "Vehiculos\TraficoEntregaRecepcionController@Actualizar");
    Route::post("recepcion/vehiculos/guardar/", "Vehiculos\TraficoEntregaRecepcionController@GuardarRep");
    Route::put("recepcion/vehiculos/actualizar/", "Vehiculos\TraficoEntregaRecepcionController@ActualizarRep");
    Route::get("entrega/vehiculos/get/", "Vehiculos\TraficoEntregaRecepcionController@get");
    Route::get("get/recepcion/trafico/{id}", "Vehiculos\TraficoEntregaRecepcionController@getRecepcion");
    Route::get("delete/imagenes/entrega/vehiculos/{id}", "Vehiculos\TraficoEntregaRecepcionController@deleteImg");
    Route::get("get/imagenes/entrega/vehiculos/{id}", "Vehiculos\TraficoEntregaRecepcionController@getImg");
    Route::get("trafico/descargar/entrega/recepcion/{id}", "Vehiculos\TraficoEntregaRecepcionController@Descargar");

    // Tipo de servicio
    Route::get("/tiposerviciotrafico", "Vehiculos\TipoServicioTraficoController@index");
    Route::post("/tiposerviciotrafico/registrar", "Vehiculos\TipoServicioTraficoController@store");
    Route::put("/tiposerviciotrafico/actualizar", "Vehiculos\TipoServicioTraficoController@update");

    // Proveedores
    Route::get("vehiculos/proveedor/obtener", "Vehiculos\ProveedorController@ObtenerProveedor");
    Route::post("vehiculos/proveedor/guardar", "Vehiculos\ProveedorController@GuardarProveedor");
});
