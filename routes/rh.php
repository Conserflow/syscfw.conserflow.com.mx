<?php

use Illuminate\Support\Facades\Route;

// Checador
Route::post("rh/checador/guardar", "RH\ChecadorController@Guardar");
Route::group(["middleware" => "auth"], function ()
{
    // FIXME: ???
    Route::get("vertodosempleados", "RH\EmpleadoController@vertodosempleados"); // Almacen

    /** ****************************
     * FIXME: NECESARIO PARA LAS RUTAS "/empleado"
     */
    Route::get("empleado", "RH\EmpleadoController@EmpleadoTemp");
    Route::get("rh/empleados/empleadospuesto/obtener", "RH\EmpleadoController@EmpleadosPuesto");

    // Datos bancarios
    Route::get("rh/empleados/datosbancarios/obtener", "RH\DatosBancariosController@ObtenerDatos");
    Route::get("rh/empleados/datosbancarios/descargar", "RH\DatosBancariosController@Descargar");

    // Contratos
    Route::get("rh/empleados/obtenerporempresa/{emp_id}", "RH\EmpleadoController@ObtenerEmpleadosEmpresa");
    Route::get("rh/contratos/obtenerempleados/{emp_id}", "RH\ContratosController@ObtenerEmpleados");
    Route::get("rh/contratos/obtener/{emp_id}", "RH\ContratosController@ObtenerContratos");
    Route::post("rh/contratos/finalizar", "RH\ContratosController@Finalizar");
    Route::post("rh/contratos/guardar", "RH\ContratosController@GuardarContrato");
    Route::get("rh/contratos/nuevocontrato/{c_id}", "RH\ContratosController@DescargarNuevo");
    Route::post("rh/empleados/fechaimss", "RH\ContratosController@ObtenerFecha");

    Route::get("rh/contratos/cargartestigos/{id}", "RH\ContratosController@ObtenerTestigos");
    Route::get("rh/sueldos/obtener/{c_id}", "RH\SueldoController@ObtenerSueldos");
    Route::post("rh/sueldos/guardar", "RH\SueldoController@GuardarSueldo");
    Route::post("rh/sueldos/actualizarsdi", "RH\SueldoController@ActualizarSDI");

    // Reporte general de empleados
    Route::get("rh/empleados/reportegeneral", "RH\EmpleadoController@ReporteGenral");
    Route::post("rh/empleados/actdesact", "RH\EmpleadoController@ActivarDesactivar");
    Route::get("rh/empleados/qr/{emp_id}", "RH\CodigosQRController@GenerarQR");
    Route::get("rh/empleados/formatoalta/{emp_id}", "RH\EmpleadoController@FormatoAlta");

    // Datos bancarios
    Route::get("rh/empleados/banco/obtener/{emp_id}", "RH\DatosBancariosEmpleadoController@ObtenerPorEmpleado");
    Route::post("rh/empleados/banco/guardar", "RH\DatosBancariosEmpleadoController@GuardarBanco");
    Route::post("rh/empleados/banco/eliminar", "RH\DatosBancariosEmpleadoController@Desactivar");

    // FIXME: Pasar a catálogos
    Route::get("/rh/catalogos/bancos", "RH\DatosBancariosEmpleadoController@ObtenerBancos");

    // FIXME: ???
    Route::resource("rh/empleados/bancos", "RH\DatosBancariosEmpleadoController");
    Route::get("rh/empleados/banco/obtener/{emp_id}", "RH\DatosBancariosEmpleadoController@ObtenerBancosEmpleado");
    // FIXME: Borrar
    Route::get("datosbanemp/{id}/datosbanemp", "RH\DatosBancariosEmpleadoController@ObtenerDatos");

    // Contacto
    Route::get("rh/empleados/contacto/obtener/{id}", "RH\ContactoController@Obtener");
    Route::post("rh/empleados/contacto/guardar", "RH\ContactoController@Guardar");

    // Familiares
    Route::get("rh/empleados/familiares/obtener/{emp_id}", "RH\FamiliareController@ObtenerFamiliares");
    Route::post("rh/empleados/familiares/guardar", "RH\FamiliareController@GuardarFamiliares");
    Route::post("rh/empleados/familiares/eliminar", "RH\FamiliareController@EliminarFamiliar");

    // Expdientes
    Route::get("rh/expediente/obtener/{e_id}", "RH\ExpedienteController@ObtenerExpediente");
    Route::post("rh/expediente/guardar", "RH\ExpedienteController@GuardarExpediente");

    // Estado civil
    Route::resource("rh/catalogos/estadosciviles", "RH\EdoCivilController");

    // Ubicacion
    Route::get("rh/catalogos/tipoubicacion", "RH\TipoUbicacionController@ObtenerUbicaciones");

    // Puestos
    Route::get("rh/catalogos/puestos", "RH\PuestoController@ObtenerPuestos");
    Route::get("rh/catalogos/puestosnombre", "RH\PuestoController@ObtenerPuestosNombre");
    Route::post("rh/catalogos/puestos/guardar", "RH\PuestoController@GuardarPuestos");
    Route::get("rh/catalogos/puestos/descargar", "RH\PuestoController@descargar");
    Route::get("rh/catalogos/puestos/{id}", "RH\PuestoController@show");

    // Departamentos
    Route::get("rh/catalogos/departamento/obtener", "RH\DepartamentoController@ObtenerDepartamentos");
    Route::post("rh/catalogos/departamento/guardar", "RH\DepartamentoController@GuardarDepartamentos");
    Route::post("rh/catalogos/departamento/desactivar", "RH\DepartamentoController@Desactivar");

    // Direccion admin
    Route::get("rh/catalogos/direccionesadministrativa/obtener", "RH\DireccionesAdministrativasController@Obtener");

    // Tipos de finalizacion
    Route::get("rh/catalogos/tiposrenuncia/obtener", "RH\TipoFinalizacionController@ObtenerTipos");

    // Tipos de contrato
    Route::get("rh/catalogos/tiposcontrato/obtener", "RH\TiposContratoController@ObtenerTipoContrato");

    // Tipo de nomina
    Route::get("rh/catalogos/tiponomina/obtener", "RH\TiposNominaController@Obtener");
    // Tipos horario
    Route::get("rh/catalogos/horarios/obtener", "RH\TiposHorarioController@ObtenerHorarios");
    // Edo Civil TODO: Namespace
    Route::resource('edocivil', 'EstadoCivilController');

    // Vacaiones
    Route::get("rh/vacacionesempleado/obtener/{emp}", "RH\VacacionesEmpleadoController@ObtenerVacacionesEmpleado");
    Route::post("rh/vacacionesempleado/guardar", "RH\VacacionesEmpleadoController@GuardarVacaciones");
    Route::post("rh/vacacionesempleado/periodos", "RH\VacacionesEmpleadoController@ObtenerPeriodos");
    Route::get("rh/vacacionesempleado/historial/{id}", "RH\VacacionesEmpleadoController@VerHistorial");
    Route::get("rh/vacacionesempleado/reporte/{data}", "RH\VacacionesEmpleadoController@Reporte");
    // TODO: Quitar
    // Route::get("rh/vacacionesempleado/generaperiodo", "RH\VacacionesEmpleadoController@Yolo");

    // Factores de riesgo
    Route::get("rh/factorriesgo/obtener", "RH\FactoresRiesgoController@ObtenerFactorRiesgo");
    Route::post("rh/factorriesgo/guardar", "RH\FactoresRiesgoController@GuardarFactorRiesgo");
    Route::post("rh/factorriesgo/subirdocumento", "RH\FactoresRiesgoController@GuardarEvidencia");
    Route::get("rh/factorriesgo/descargarevidencia/{id}", "RH\FactoresRiesgoController@DescargarEvidencia");
    Route::get("rh/factorriesgo/descargarcuestionario/{id}", "RH\FactoresRiesgoController@DescargarCuestionario");

    // Formato de Infraestructura
    Route::get("rh/cuestionarioinfra/obtener", "RH\CuestionarioController@ObtenerCuestionarioInfra");
    Route::get("rh/cuestionarioinfra/plantilla", "RH\CuestionarioController@DescargarPlantilla");
    Route::get("rh/cuestionarioinfra/descargarevidencia/{id}", "RH\CuestionarioController@DescargarEvidencia");
    Route::post("rh/cuestionarioinfra/guardar", "RH\CuestionarioController@GuardarCuestionarioInfra");
    Route::post("rh/cuestionarioinfra/subircuestionario", "RH\CuestionarioController@SubirCuestionarioInfra");
    Route::get("rh/empleado/obtenerhistorial/{id}", "RH\HistorialContratosController@ObtenerHistorial");

    //Cumpleaños
    Route::resource("rh/empleados/cumples", "RH\CumplesController");
    Route::get("rh/empleados/cumplemes/{mes}", "RH\CumplesController@DescargarCumple");

    // Asistencias 
    Route::get("rh/asistencias/buscarfechas/{datos}", "RH\AsistenciaController@BuscarAsistenciasFecha");
    Route::post("rh/asistencias/buscarempleado", "RH\AsistenciaController@BuscarEmpleado");
    Route::get("rh/asistencias/reporte/{data}", "RH\AsistenciaController@GeneraReporte");

    // Dias Festivos
    Route::post("rh/diasfestivos/guardar", "RH\DiasFestivosController@GuardarDiasFestivos");
    Route::get("rh/diasfestivos/obtener", "RH\DiasFestivosController@ObtenerDiasFestivos");

    // Aniversarios
    Route::get("rh/aniversario/obtenerempleados", "RH\AniversarioController@ObtenerEmpleados");

    // Empleados/
    Route::resource("rh/empleados", "RH\EmpleadoController");
});
