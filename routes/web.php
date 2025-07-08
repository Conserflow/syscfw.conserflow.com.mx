<?php

use Illuminate\Support\Facades\Route;

Route::get('borrarentrada/{id}', "PartidaEntradaController@EliminarEntrada");
Route::post('/vehiculos/combustible/cargarfechas', "Vehiculos\SubirDatosController@RegistrarCancelados");
Route::post('/vehiculos/combustible/cargar', "Vehiculos\SubirDatosController@CargarCombustible");

// Reporte de almacen
Route::get('/reporteinventario_almacen', 'ReporteAlmacenController@Descargar');
Route::get('proyectossalidas', 'EntradaSalidaController@getProyectos');
Route::post('guardarproyectossalidas', 'EntradaSalidaController@Guardar');
Route::get('getSalidasRetorno/{proyectoId}', 'EntradaSalidaController@GetSalidas');
Route::get('partidassalida/{proyectoId}', 'EntradaSalidaController@GetPartidas');
Route::get('getpartidasretorno/{request}', 'EntradaSalidaController@GetPartidasRetorno');
Route::post('guardarRetorno', 'EntradaSalidaController@GuardarRetorno');
//Rutas ocupadas para romper la sesion al cerrar navegador/pestaña de la aplicación
Route::get('CerrarSesion', 'Auth\LoginController@cerrarnavegador');
Route::get('ActualizarVista', 'Auth\LoginController@actualizarnavegador');
Route::get('Salir', 'Auth\LoginController@inactividad');
Route::get('Exit', 'Auth\LoginController@logout');
Route::get('Filtro', 'Auth\LoginController@bloquearacceso');
//FIN
Route::get('Sistema', 'SistemaController@index')->name('Sistema');
//Login del sistema
Route::get('/', 'Auth\LoginController@showLoginForm');
Route::get('dashboard', 'DashboardController@index')->name('dashboard');
Route::post('login', 'Auth\LoginController@login')->name('login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
Route::post('privacidad', 'DashboardController@privacidad')->name('privacidad');
Route::post('ayuda', 'DashboardController@ayuda')->name('ayuda');
Route::get('/descargararchivos/{modulo?}/{menu?}/{nombre?}/{tipo?}', 'DashboardController@descargarArchivos');
Route::get('/traerarchivos/{modulo?}/{menu?}/{carpeta?}', 'DashboardController@traerArchivos');
/****************/
Route::group(['middleware' => 'auth'], function ()
{
  //Ruta de FechaActualController
  Route::resource('FechaActual', 'FechaActualController');
  //rutas para permisos de usuario
  Route::resource('PermisoUser', 'PermisoUserController');
  Route::put('PermisoUser/{id}/actualizar', 'PermisoUserController@actualizar');
  Route::put('PermisoUser/{id}/actualizarsub', 'PermisoUserController@actualizarsub');
  Route::get('todospermisos/{id}', 'PermisoUserController@allPermisos');
  Route::resource('permisou/{id}/permisou', 'PermisoUserController');
  Route::get('menusidemp/{id}', 'PermisoUserController@menusidemp');
  //Ruta para asignar permisos de CRUD
  Route::get('elementospormodulos/{id}', 'PermisosCrudController@elementospormodulos');
  Route::get('arreglos', 'PermisosCrudController@obtenerSubmenu');
  Route::get('/permisocrud', 'PermisosCrudController@Permisocrud');
  Route::put('PermisosCrud/{id}/actualizar', 'PermisosCrudController@actualizar');
  Route::post('menusidempc', 'PermisosCrudController@menusidempc');
  Route::post('permisocrud/todos', 'PermisosCrudController@todos');
  //Ruta para validar permisos del CRUD
  Route::resource('CRUD', 'ControlesSistemaController');
  Route::get('elementospormodulo/{id}', 'ElementosMenuController@elementosPorModulo');
  Route::get('elementosmenupormodulo/{id}', 'ElementosMenuController@elementosMenuPorModulo');
  Route::put('eliminarmenu', 'ElementosMenuController@eliminarMenu');
  Route::put('eliminarsubmenu', 'ElementosMenuController@eliminarSubmenu');
  Route::post('agregarmenu', 'ElementosMenuController@agregarMenu');
  Route::put('actualizarmenu', 'ElementosMenuController@actualizarMenu');
  Route::post('agregarsubmenu', 'ElementosMenuController@agregarSubmenu');
  Route::put('actualizarsubmenu', 'ElementosMenuController@actualizarSubmenu');
  //Rutas del ElementosDashboardController para utilizar en ElementosDashboard.vue
  Route::get('/elementosdashboard', 'ElementosDashboardController@index');
  Route::put('/elementosdashboard/actualizar', 'ElementosDashboardController@update');
  Route::put('/elementosdashboard/eliminar', 'ElementosDashboardController@eliminar');
  //Rutas del PermisosDashboardController para utilizar en PermisosDashboard.vue
  Route::get('/permisosdashboard', 'PermisosDashboardController@index');
  Route::post('/permisosdashboard/porusuariomodulo', 'PermisosDashboardController@permisosDashboardPorUsuarioModulo');
  //Ruta para generar el pdf de contratos
  Route::resource('proyecto', 'ProyectoController');
  Route::get('proyecto-listar', 'ProyectoController@listar');
  Route::get('proyecto-pausar/{id}', 'ProyectoController@pausar');
  Route::get('proyecto-rechazar/{id}', 'ProyectoController@rechazar');
  Route::get('proyectos/buscar', 'ProyectoController@buscar');
  Route::get('proyectos/buscarcompras', 'ProyectoController@buscarcompras');
  Route::resource('usuario', 'UserController');
  //Rutas del BeneficiarioController para utilizar en Beneficiarios.vue
  Route::resource('beneficiario', 'BeneficiarioController');
  Route::resource('beneficiario/{id}/beneficiario', 'BeneficiarioController');
  //Rutas del BancoController para utilizar en Banco.vue
  Route::resource('banco', 'BancoController');
  //Rutas del DireccionesEmpleadosController para utilizar en Direccion-Empleado.vue
  Route::resource('direccionempleado', 'DireccionEmpleadoController');
  Route::resource('direccionempleado/{id}/direccionempleado', 'DireccionEmpleadoController');
  Route::get('descargar-vacaciones/{id}', 'VacacionesPdfController@pdf');
  Route::resource('existencias', 'ExistenciasController');
  Route::get('nivel', 'ExistenciasController@nivelIndex');
  Route::get('stand', 'ExistenciasController@standIndex');
  Route::get('ubicacion', 'ExistenciasController@ubicacionIndex');
  Route::post('existencia', 'ExistenciasController@busquedaExistencias');
  Route::get('descargar-existencia/{id}', 'ExistenciasController@excel');
  Route::get('descargar-existencia-mes/{id}', 'ExistenciasController@excelMes');
  Route::get('descargar-existencia-proyecto/{id}', 'ExistenciasController@excelProyecto');
  Route::get('descargar-existencia-general', 'ExistenciasController@excelGeneral');
  Route::get('/existencia/getlist', 'ExistenciasController@getList');
  Route::get('/existencia/getlist/stand/{id}', 'ExistenciasController@getListStand');
  Route::get('/existencia/getlist/nivele/{id}', 'ExistenciasController@getListNivel');
  Route::get('busquedaE/{id}', 'BusquedaExistenciaController@buscar');
  //Ruta para usar en ExistenciasPrincipal.vue
  Route::resource('articuloexistencia', 'ExistenciasPrincipalController');
  //Rutas del CatServiciosController para utilizar en .vue
  Route::resource('catvehiculos', 'CatVehiculosController');
  //Rutas del EntradaController para utilizar en Entrada.vue
  Route::resource('entrada', 'EntradaController');
  Route::get('entradas/partidas/oc/{id}', 'EntradaController@GetPartidas');
  Route::get('entradas/partidas/oc/actualiza/{id}', 'EntradaController@ActPartidas');
  Route::get('busqueda/entradainterna', 'EntradaController@busquedaEntradaInterna');
  Route::get('descargar-entrada/{id}', 'EntradaPdfController@pdf');
  Route::get('descargar-entrada-nuevo-formato/{id}', 'EntradaPdfController@pdfnew');
  Route::get('verordenescompras', 'EntradaController@verordenescompras');
  Route::get('entradas/revisionfactura/{id}', 'EntradaController@revisionfactura');
  //Rutas del TipoEntradaController para utilizar en Entrada.vue
  Route::resource('tipoentrada', 'TipoEntradaController');
  Route::post('/tipoentrada/registrar', 'TipoEntradaController@store');
  Route::put('/tipoentrada/actualizar', 'TipoEntradaController@update');
  //Rutas del TipoAdquisicionController para utilizar en Entrada.vue
  Route::resource('tipoadquisicion', 'TipoAdquisicionController');
  Route::resource('salida', 'SalidaController');
  Route::get('salida/{id}/sitios', 'SalidaController@sitios');
  Route::get('salida/{id}/resguardo', 'SalidaController@resguardo');
  Route::get('descargar-salida/{id}', 'SalidaPdfController@pdf');
  Route::get('descargar-salida-new/{id}', 'SalidaPdfController@pdfnew');
  Route::get('descargar-salidasitio/{id}', 'SalidaPdfController@pdfsitio');
  Route::get('descargar-salidasitio-new/{id}', 'SalidaPdfController@pdfsitionew');
  Route::get('partidasalida/{id}/ver', 'PartidasSalidasController@ver');
  Route::resource('partidasalida', 'PartidasSalidasController');
  Route::get('getlotetemporal/{id}', 'PartidasSalidasController@getLoteTemporal');
  Route::post('buscarlotenombre', 'PartidasSalidasController@buscarlotenombre');
  Route::get('obtenerarticulog/{id}', 'PartidasSalidasController@obtenerarticulog');
  Route::get('obtenerarticuloa/{id}', 'PartidasSalidasController@obtenerarticuloa');
  Route::get('requisicionbusqueda/{id}', 'PartidasSalidasController@requisicionbusqueda');
  Route::get('eliminar/partida/salida/{id}', 'PartidasSalidasController@eliminarPartida');
  Route::resource('tiposalida', 'TipoSalidaController');
  Route::resource('partidasalidaapartados', 'PartidasSalidasApartadosController');
  Route::resource('documentosrequeridosarticulos', 'DocumentoProveedorController');
  Route::post('/certificadosdocumentos/cargar', 'DocumentoProveedorController@load');
  Route::post('guardarqvalidadocumentos', 'DocumentoProveedorController@guardarqvalidadocumentos');
  Route::get('requisicionesdocumentospendientes', 'DocumentoProveedorController@requisicionesdocumentospendientes');
  Route::get('documentospendientes/{id}', 'DocumentoProveedorController@documentospendientes');
  Route::get('descargardocumentos/{id}', 'DocumentoProveedorController@descargardocumentos');
  Route::get('requisicioncompserart/{id}', 'PartidasReController@requisicioncompserart');
  // Inventario de almacén
  Route::get("almacen/inventariogral/buscar/{codigo}", "Almacen\InventarioController@BuscarCodigo");
  Route::get("almacen/inventariogral/buscarpordesc/{desc}", "Almacen\InventarioController@BuscarDesc");
  Route::get("almacen/inventariogral/obtener/{proeycto_id}", "Almacen\InventarioController@Obtener");
  Route::post("almacen/inventariogral/guardarinv/", "Almacen\InventarioController@Guardar");
  Route::get("almacen/inventariogral/reporte/{proyecto_id}", "Almacen\InventarioController@Reporte");
  Route::get("almacen/inventario/grupo/obtener", "Almacen\GrupoInventarioController@Obtener");
  Route::get("almacen/inventario/grupo/obtenerporgrupo/{g_id}", "Almacen\GrupoInventarioController@ObtenerPorGrupo");
  Route::get("almacen/inventario/grupo/obtenerendientes", "Almacen\GrupoInventarioController@ObtenerPendientes");
  Route::post("almacen/inventario/grupo/guardar", "Almacen\GrupoInventarioController@Guardar");
  Route::post("almacen/inventario/grupo/agrupararticulos", "Almacen\GrupoInventarioController@Agrupar");
  //Rutas del Controller para utilizar en .vueformatos almacen
  Route::resource('tipocompra', 'TipoCompraController');
  //Ruta para Compras.vue
  Route::get('descargar-compran/{id}', 'CompraPdfController@pdfnew');
  Route::get('descargar-resguardo/{id}', 'ResguardoPdfController@pdf');

  //Ruta para requisiciones
  Route::resource('requisicioncompra', 'RequisicionComprasController');
  Route::post('requisicioncompra/apartados', 'RequisicionComprasController@apartadosGuardar');
  Route::post('comprarequivalente', 'RequisicionComprasController@comprarequivalente');
  //Ruta para partidas.vue
  Route::resource('partidare', 'PartidasReController');
  Route::get('/partidare/apartados/{id}', 'PartidasReController@getApartados');
  Route::get('partidare/{id}/activ', 'PartidasReController@activ');
  Route::put('partidare/{id}/updatedoc', 'PartidasReController@updatedoc');
  Route::resource('partidaentrada', 'PartidaEntradaController');
  Route::get('eliminar/partida/entrada/{id}', 'PartidaEntradaController@eliminarPartidaEntrada');
  Route::put('partidaentrada/update/almacen/{id}', 'PartidaEntradaController@updatepr');
  Route::put('partidaentrada/update/factura/{id}', 'PartidaEntradaController@update');
  Route::post('guardarpartidainterna', 'PartidaEntradaController@guardarPartidaInterna');
  // Route::post('partidaentrada/eliminarpartidaentrada', 'PartidaEntradaController@eliminarPartidaEntrada');
  Route::post('partidaentrada/calidad', 'PartidaEntradaController@calidad');
  Route::get('partidaentrada/{id}/calidadsalida', 'PartidaEntradaController@calidadsalida');
  Route::post('partidaentradainterna/actualizar', 'PartidaEntradaController@actualizarPrecioEntradaInterna');
  Route::post('partidaentradainterna/otenercomentario', 'PartidaEntradaController@obtenerComentarioLoteAlmacen');
  Route::get('verordencompra/{id}', 'PartidaEntradaController@verordencompra');
  Route::get('/catpendiente/busqueda', 'PartidaEntradaController@busqueda');
  Route::post('entradas/guardar/actualizacion/almacen', 'PartidaEntradaController@almacenActualizacion');
  Route::resource('nomina', 'NominaController');
  Route::resource('nomina/{id}/nomina', 'NominaController');
  // Route::get('nominaejemplo/{id}', 'NominaEjemploController@ver');
  //Ruta para el Organigrama
  Route::resource('organigramageneral', 'OrganigramaController');
  //Ruta para utilizar en ModulosSistema.vue
  Route::resource('ModulosSistema', 'ModuloSistemaController');
  // Cargan los modulos del usuario autenticado desde los permisos
  Route::get('/modulos/por-usuario', 'ModuloController@getModulosByUAuthUser');
  Route::post('/modulos/cargar', 'ModuloController@loadModulos');
  // Cargan el menu y submenu desde los permisos del usuario autenticado
  Route::post('/elementosmenu', 'ElementosMenuController@index');
  //Rutas del AlmaceneController para utilizar en Categoria.vue
  Route::get('/almacen/ver', 'AlmaceneController@ver');
  Route::get('/almacen/verstand/{id}', 'AlmaceneController@verstand');
  Route::get('/almacen/vernivel/{id}', 'AlmaceneController@vernivel');
  Route::get('/almacen', 'AlmaceneController@index');
  Route::get('/almacen/inventario', 'AlmaceneController@inventario');
  Route::post('/almacen/registrar', 'AlmaceneController@store');
  Route::get('/descargar-inventario/{id}', 'AlmaceneController@excel');
  Route::get('descargar-inventarios-general', 'AlmaceneController@excelGeneral');
  Route::get('descargar-inventarios-por-proyecto', 'AlmaceneController@excelProyecto');
  Route::get('/almacen/inventario/buscar/{id}', 'AlmaceneController@buscarInventario');
  Route::put('/almacen/actualizar', 'AlmaceneController@update');
  Route::put('/almacen/desactivar', 'AlmaceneController@desactivar');
  Route::put('/almacen/activar', 'AlmaceneController@activar');
  Route::post('/almacen/registrar/stand', 'AlmaceneController@storeStand');
  Route::put('/almacen/actualizar/stand', 'AlmaceneController@updateStand');
  Route::post('/almacen/registrar/nivel', 'AlmaceneController@storeNivel');
  Route::put('/almacen/actualizar/nivel', 'AlmaceneController@updateNivel');
  Route::get('/almacen/getlist', 'AlmaceneController@getList');
  Route::get('/almacen/getlist/stand/{id}', 'AlmaceneController@getListStand');
  //rutas para utilizr en el dashboard de Almacen
  Route::get('requisicionesalmacen', 'AlmaceneController@requisicionesalmacen');
  Route::get('requisicionesalmacenpendientes', 'AlmaceneController@requisicionesalmacenpendientes');
  //Rutas del GrupoController para utilizar en Categoria.vue
  Route::get('/grupo', 'GrupoController@index');
  Route::post('/grupo/registrar', 'GrupoController@store');
  Route::put('/grupo/actualizar', 'GrupoController@update');
  Route::put('/grupo/desactivar', 'GrupoController@desactivar');
  Route::put('/grupo/activar', 'GrupoController@activar');
  Route::get('/grupo/getlist/{id}', 'GrupoController@getList');
  Route::get('/grupo/find/{id}', 'GrupoController@find');
  //Rutas del LoteController para utilizar en Lote.vue
  Route::get('/lote', 'LoteController@index');
  Route::post('/lote/registrar', 'LoteController@store');
  Route::put('/lote/actualizar', 'LoteController@update');
  Route::put('/lote/desactivar', 'LoteController@desactivar');
  Route::put('/lote/activar', 'LoteController@activar');
  Route::post('/lote/articulo', 'LoteController@getByArticulo');
  //Rutas del CategoriaController para utilizar en Categoria.vue
  Route::get('/categoria', 'CategoriaController@index');
  Route::post('/categoria/registrar', 'CategoriaController@store');
  Route::put('/categoria/actualizar', 'CategoriaController@update');
  Route::put('/categoria/desactivar', 'CategoriaController@desactivar');
  Route::put('/categoria/activar', 'CategoriaController@activar');
  Route::get('/categoria/getlist', 'CategoriaController@getList');
  //Rutas del ModuloController para utilizar en Usuario.vue
  Route::get('/modulo', 'ModuloController@index');
  Route::post('/modulo/registrar', 'ModuloController@store');
  Route::put('/modulo/actualizar', 'ModuloController@update');
  Route::put('/modulo/desactivar', 'ModuloController@desactivar');
  Route::put('/modulo/activar', 'ModuloController@activar');
  //Rutas del Articulocontroller para utilizar en Articulo.vue
  Route::resource('/articulos', 'ArticuloController');
  Route::get('/articulo', 'ArticuloController@index');
  Route::post('/articulo/registrar', 'ArticuloController@store');
  Route::put('/articulo/desactivar', 'ArticuloController@desactivar');
  Route::put('/articulo/activar', 'ArticuloController@activar');
  Route::put('/articulo/upload', 'ArticuloController@uploadArticulos');
  Route::get('/articulo/busqueda', 'ArticuloController@busqueda');
  Route::post('/articulo/existencias', 'ArticuloController@existencias');
  Route::get('/movimientos/kardex', 'ArticuloController@kardex');
  Route::get('/articulo/maximos', 'ArticuloController@maximos');
  Route::get('/articulo/minimos', 'ArticuloController@minimos');
  Route::get('/articulo/proximoscaducar', 'ArticuloController@proximosCaducar');
  Route::get('/articulo/caducados', 'ArticuloController@caducados');
  //Rutas del proyecto para utilizar en proyecto.vue
  Route::get('/proyecto', 'ProyectoController@index');
  Route::get('/proyectos/reporte/descargar', 'ProyectoController@Descargar');
  Route::post('/proyecto/registrar', 'ProyectoController@store');
  Route::put('/proyecto/actualizar/{id}', 'ProyectoController@update');
  Route::put('/proyecto/eliminar/{id}', 'ProyectoController@eliminar');
  Route::put('/proyecto/desactivar', 'ProyectoController@desactivar');
  Route::put('/proyecto/activar', 'ProyectoController@activar');
  Route::get('/proyecto-todos', 'ProyectoController@todos');
  Route::get('/proyecto-listar-todos', 'ProyectoController@listarTodos');
  Route::get("/proyectos/obtener/conoc", "ProyectoController@ObtenerProyectos");
  Route::get('/proyectosmaster', 'ProyectoController@proyectosMaster');
  Route::post('proyecto-obtenerarchivos/', 'ProyectoController@obtenerarchivos');
  Route::post('/proyecto/subir/documento/', 'ProyectoController@subirDocumento');
  Route::get('/proyecto-obtener-documentos/{id}', 'ProyectoController@getDocumentosProyectos');
  Route::get('delete/documento/proyecto/{id}', 'ProyectoController@deleteDocumentosProyectos');
  Route::get('proyecto-delete-temporal/{id}', 'ProyectoController@deleteDocTempProyectos');
  //Rutas del StockController para utilizar en Stock.vue
  Route::get('/stock', 'StockController@index');
  Route::post('/stock/registrar', 'StockController@store');
  Route::put('/stock/actualizar', 'StockController@update');
  Route::put('/stock/desactivar', 'StockController@desactivar');
  Route::put('/stock/activar', 'StockController@activar');
  Route::get('/stock/getlist', 'StockController@getList');
  Route::get('/beneficiario', 'BeneficiarioController@index');
  Route::post('/beneficiario/registrar', 'BeneficiarioController@store');
  Route::put('/beneficiario/actualizar', 'BeneficiarioController@update');
  Route::get('/beneficiario/getlist', 'BeneficiarioController@getList');
  Route::get('/beneficiario/activar', 'BeneficiarioController@activar');
  Route::get('/beneficiario/desactivar', 'BeneficiarioController@desactivar');
  Route::get('descargar-requisicionew/{id}', 'RequisicionPdfController@pdfnew');
  Route::post('/errors', 'ErrorsController@index');
  //Rutas de TipoCalidadController pars utilizar en TipoCalidad.vue
  Route::resource('tipocalidad', 'TipoCalidadController');
  Route::post('/tipocalidad/registrar', 'TipoCalidadController@store');
  Route::put('/tipocalidad/actualizar', 'TipoCalidadController@update');
  //Rutas de TipoResguardoController para usar en TipoResguardo.vue
  Route::resource('tipoResguardo', 'TipoResguardoController');
  Route::post('/tipoResguardo/registrar', 'TipoResguardoController@store');
  Route::put('/tipoResguardo/actualizar', 'TipoResguardoController@update');
  Route::resource('/preventivos', 'PreventivosController');
  Route::resource('/correctivos', 'CorrectivosController');
  Route::get('nominasbusqueda/{id}', 'NominaSemanalBController@busqueda');
  Route::get('nominaqbusqueda/{id}', 'NominaQuincenalBController@busqueda');
  Route::get('nominagbusqueda/{id}', 'NominaGeneralBController@busqueda');
  Route::resource('/registroresguardo', 'RegistroResguardoController');
  Route::resource('salidasporproyecto', 'PreciosController');
  Route::get('descargar-materiales/{id}', 'PreciosController@descargar');
  Route::resource('consultadashp', 'ConsultaDashProyectosController');
  Route::post('agregar/correciones/partidas', 'ConsultaDashProyectosController@comentarioPartidas');
  Route::get('documentosdashproyectos/{id}', 'ConsultaDashProyectosController@documentosdashproyectos');
  Route::get('retornopartidasporproyecto/{id}', 'RetornoController@partidasPorProyecto');
  Route::get('retornoporproyecto/{id}', 'RetornoController@getRetornoProyecto');
  Route::post('guardarpartidaretorno', 'RetornoController@guardarPartidaRetorno');
  //Rutas del ProyectoCategoriasController para utilizar en ProyectoCategoria.vue
  Route::get('/procategoria', 'ProyectoCategoriasController@index');
  Route::get('/procategoria/getlist', 'ProyectoCategoriasController@getList');
  //Rutas del ProyectoSubcategoriasController para utilizar en ProyectoCategoria.vue
  Route::get('/prosubcategoria', 'ProyectoSubcategoriasController@index');
  Route::get('/prosubcategoria/getlist', 'ProyectoSubcategoriasController@getList');
  Route::get('/prosubcategoria/getlistbycat/{id}', 'ProyectoSubcategoriasController@getListByCategoria');
  Route::get('/usuariocategoria', 'UsuarioCategoriaController@index');
  //rutas para utilizar en viaticos
  Route::resource('viaticos', 'ViaticosController');
  Route::get('verviaticos/{id}', 'ViaticosController@verviaticos');
  Route::get('conceptosviaticos', 'CatalogoConceptosViaticosController@listaConceptos');
  Route::resource('solicitudviaticos', 'SolicitudViaticosController');
  Route::get('solicitud/viaticos/csct', 'SolicitudViaticosController@getCSCT');
  Route::get('solicitud/viaticos/conserflow/{emp}', 'SolicitudViaticosController@getConserflow');
  Route::get('solicitud/viaticos/detalles/{id}', 'SolicitudViaticosController@detalles');
  Route::post('estadosviaticos', 'SolicitudViaticosController@estados');
  Route::get('proyectos/viaticos/{id}', 'SolicitudViaticosController@proyectos');
  Route::get('eliminar/solicitud/viaticos/{id}', 'SolicitudViaticosController@eliminar');
  //Rutas del SatCatFormapagoController para utilizar en SatCatFormaPago.vue
  Route::get('/satcatformpago', 'SatCatFormapagoController@index');
  Route::post('/satcatformpago/registrar', 'SatCatFormapagoController@store');
  Route::put('/satcatformpago/actualizar', 'SatCatFormapagoController@update');
  //Rutas del SatCatProdserController para utilizar en SatCatProdser.vue
  Route::get('/satcatprodser1', 'SatCatProdserController@index');
  Route::post('/satcatprodser/registrar', 'SatCatProdserController@store');
  Route::put('/satcatprodser/actualizar', 'SatCatProdserController@update');
  //Rutas del SatCatUsoCfdi para utilizar en ...
  Route::get('/datosgenerales', 'CatalogoSatFacturaController@datosgenerales');
  Route::get('/satcatusocfdi', 'CatalogoSatFacturaController@usocfdi');
  Route::get('/satcatmonedas', 'CatalogoSatFacturaController@satcatalogomonedas');
  Route::get('/satcatmetodopago', 'CatalogoSatFacturaController@satcatmetodopago');
  Route::get('/satcattipofactura', 'CatalogoSatFacturaController@satcattipofactura');
  Route::get('/satcatprodser', 'CatalogoSatFacturaController@satcatprodser');
  Route::get('/satcatunidades', 'CatalogoSatFacturaController@satcatunidades');
  Route::get('/satcattiporelacion', 'CatalogoSatFacturaController@satcattiporelacion');
  Route::get('/catfactura', 'CatalogoSatFacturaController@catfactura');
  Route::resource('/partidafactura', 'PartidasFacturaController');
  Route::get('/sellartimbrarfactura/{id}', 'FacturaSellarTimbrarController@sellartimbrar');
  Route::get('/descargarfactura/{id}', 'FacturaSellarTimbrarController@descargarfactura');
  Route::get('/descargarprefactura/{id}', 'FacturaSellarTimbrarController@descargarprefactura');
  Route::get('/partidafactura/cancelarfactura/{id}', 'FacturaSellarTimbrarController@cancelarfactura');
  //Ratas para utiizar en facturas
  Route::resource('factura', 'FacturaController');
  Route::get('verfacturauno/{id}', 'FacturaController@verfacturauno');
  Route::get('clientextranjero', 'FacturaController@clientextranjero');
  Route::resource('datosgeneral', 'DatosGeneralesController');
  Route::resource('partidafacturapagos', 'PartidasFacturasPagosController');
  Route::get('timbresrestantes', 'FacturaController@timbresrestantes');
  Route::get('descargarfacturareportexml/{id}', 'FacturaController@descargarfacturaxml');
  Route::delete('eliminarfacturareportexml/{id}', 'FacturaController@destroyxml');
  //Rutas para Control de Nómina
  Route::get('/informe/{id}', 'InformeController@index');
  Route::get('descargarformatoviatico/{id}', 'ViaticosController@descargarViaticos');
  Route::get('descargarnformatofij/{id}', 'ViaticosController@descargarnFij');

  //Rutas para modulo Capacitación en RH
  Route::resource('infEmp', 'InfCapacitacionController');
  Route::resource('infEmpresa', 'InfCapEmpresaController');
  //ordenes de compras sin requisiciones
  Route::resource('partidacomprasinrequisicion', 'CompraSinRequiController');
  Route::get('partidacomprasinrequisicion/eliminar/{id}', 'CompraSinRequiController@eliminar');
  //Rutas para Supervisor de empleado
  Route::get('supervisorAsigna/', 'SupervisorController@index');
  Route::resource('supervisor', 'SupervisorController');
  //Ruta para guardar proyecto agrupador
  Route::post('guardarAgrupador', 'ProyectoAgrupadorController@guardaAgrupador');
  Route::get('listaAgrupador/', 'ProyectoAgrupadorController@listaProyectos');
  Route::get('/consulta/articulos', 'RequisicionComprasController@articulosconsulta');
  Route::get('/listaasignacion', 'AsignacionVehiculoController@listadoEmpleado');
  Route::get('/listapoliza', 'AsignacionVehiculoController@resguardovehiculo');
  Route::get('descargar-valeresguardoT/{id}', 'AsignacionVehiculoController@pdfcne');

  Route::put('/partidasfacturasexcel/upload', 'PartidasFacturaController@subirExcel');
  Route::post('reportegastosmenoresviaticos', 'ViaticosController@guardarComprobacion');
  Route::get('eliminareporte/gastosmenores/{id}', 'ViaticosController@eliminararch');

  //Rutas para las requisiciones espejo
  Route::get('listadorequisicionporcompra/{id}', 'RequisicionporCompraController@listadoCompras');
  //Rutas para el calculos de gastos, sueldos y movimientos de proyectos
  Route::put('guardaractualizapartida', 'RequisicionComprasController@actualizarpartidacompra');
  Route::get('get/barras/art/{id}', 'LotesBarrasSalidasController@getN');
  Route::get('get/lote/art/{id}', 'LotesBarrasSalidasController@getNL');
  Route::get('get/barras/art/retorno/{id}', 'LotesBarrasSalidasController@getNR');
  Route::get('get/lote/art/retorno/{id}', 'LotesBarrasSalidasController@getNLR');
  Route::post('get/barras/art/guardar', 'LotesBarrasSalidasController@store');
  Route::get('buscar/salidas/dia/barras', 'LotesBarrasSalidasController@getSalidasDia');
  Route::get('descargar/codigo/barras/{id}', 'LotesBarrasSalidasController@getBarras');
  Route::get('/generar/codigos/{id}', 'LotesBarrasSalidasController@GenerarCodigos');
  //rutas para gastos empresa
  Route::get('descargar-entrada-salida-vale/{id}', 'AlmaceneController@ValeRetorno');
  // Reporte de existencias en almacen
  Route::get('/reportes/existencias/almacenes', 'PartidaEntradaController@Almacenes');
  Route::get('/reportes/existencias/categorias', 'PartidaEntradaController@Categorias');
  Route::get('buscararticuloexistencia/{id}', 'ExistenciasPrincipalController@Existencia');
  Route::get('arreglar/relacionados', 'FacturaController@agregar');
  Route::get('relaciones/eliminar/{id}', 'FacturaController@eliminarAsignacion');
  Route::get('get/relacionados/{id}', 'FacturaController@getAsignacion');
  Route::get('/sellartimbrarfactura/prueba/{id}', 'FacturaSellarTimbrarController@sellartimbrarprueba');
  // Route::get('/descargarfactura/{id}','FacturaSellarTimbrarController@descargarfactura');
  Route::get('/descargarfactura/prueba/{id}', 'FacturaSellarTimbrarController@descargarfacturaprueba');
  Route::get('cancelar/prueba/{id}', 'FacturaSellarTimbrarController@cancelarPrueba');
  Route::get('lista/asistencia/seguridad/{id}', 'SeguridadController@getLista');
  // FIXME: Obtener de Proyectos
  Route::get('seguridad/listado/proyectos', 'SeguridadController@getListaProyectos');
  Route::get('get/lista/catalogo/analisis', 'SeguridadController@getCatalogo');
  Route::post('guardar/lista/catalogo/analisis', 'SeguridadController@guardarCatalogo');
  Route::post('eliminar/lista/catalogo/analisis', 'SeguridadController@EliminarCatalogo');
  Route::put('actualizar/lista/catalogo/analisis', 'SeguridadController@actualizarCatalogo');
  Route::get('get/lista/catalogo/residuos', 'SeguridadController@getCatalogoR');
  Route::post('guardar/lista/catalogo/residuos', 'SeguridadController@guardarCatalogoR');
  Route::put('actualizar/lista/catalogo/residuos', 'SeguridadController@actualizarCatalogoR');
  Route::post('abc', 'SeguridadController@abc');
  Route::post('/guardar/analisis/seguridad', 'SeguridadController@guardar');
  Route::put('/actualizar/analisis/seguridad', 'SeguridadController@actualizar');
  Route::get('get/analisis/seguridad', 'SeguridadController@index');
  Route::get('eliminar/analisis/seguridad/{id}', 'SeguridadController@eliminarAnalisis');
  Route::post("seguridad/analisis/eliminar", "SeguridadController@EliminarRiesgo");
  Route::get('get/lista/evaluacion/riesgo/{id}', 'SeguridadController@getRiesgos');
  Route::get('get/participantes/analisis/seguridad/{id}', 'SeguridadController@getParticipantes');
  Route::post('guardar/participantes/analisis/seguridad', 'SeguridadController@guardarParticipantes');
  Route::get('eliminar/participantes/analisis/seguridad/{id}', 'SeguridadController@eliminarParticipante');
  Route::get('descargar/analisis/seguridad/{id}', 'SeguridadController@descargar');
  Route::post('seguridad/permiso/general/guardar', 'SeguridadController@guardarPermiso');
  Route::put('seguridad/permiso/general/actualizar', 'SeguridadController@actualizarPermiso');
  Route::get('seguridad/permiso/general/descargar/{id}', 'SeguridadController@descargarPermiso');
  Route::get('get/permisos/seguridad', 'SeguridadController@getPermiso');
  Route::get('seguridad/permiso/general/eliminar/{id}', 'SeguridadController@eliminarPermiso');
  Route::get('descargar/trazabilidad/almacen/{id}', 'AlmaceneController@trazabilidad');
  Route::get('/almacen/unidadesm/obtener', 'UnidadesMedidaController@Obtener');
  Route::get('/almacen/unidadesm/obtenerdesc', 'UnidadesMedidaController@ObtenerDescripcion');
  Route::post('almacen/unidadesm/registrar', 'UnidadesMedidaController@Guardar');
  Route::put('almacen/unidadesm/actualizar', 'UnidadesMedidaController@Actualizar');
  //Solictud de viaticos
  // Documentos SGI
  Route::get('/procedsgi/archivos/{ruta}', 'SGIController@ObtenerArchivos');
  Route::get('/procedsgi/descargar/{nombre}', 'SGIController@Descargar');
  // Cambio de ubicación de Almacén
  Route::get('/lote/ubicacion/obtener', 'LoteController@Obtener');
  Route::get('/lote/ubicacion/buscar/{busacar}', 'LoteController@Buscar');
  Route::get('/lote/ubicacion/buscar/proyecto/{busacar}', 'LoteController@BuscarProyecto');
  Route::get('/lote/ubicacion/obtenerubicacion/{id}', 'LoteController@ObtenerActual');
  Route::put('/lote/ubicacion/cambiar', 'LoteController@Cambiar');
  Route::post('alm/guardar/entrada/interna', 'EntradaInternaController@store');
  Route::post('seguridad/residuos/guardar/bitacora', 'BitacoraResiduosController@store');
  Route::put('seguridad/residuos/actualizar/bitacora', 'BitacoraResiduosController@update');
  Route::post('seguridad/residuos/guardar/bitacora/gral', 'BitacoraResiduosController@storeGral');
  Route::put('seguridad/residuos/actualizar/bitacora/gral', 'BitacoraResiduosController@updateGral');
  Route::get('seguridad/residuos/get/bitacora/gral', 'BitacoraResiduosController@getGral');
  Route::get('seguridad/residuos/get/bitacora/{id}', 'BitacoraResiduosController@get');
  Route::get('descargar/bitacora/descargar/{id}', 'BitacoraResiduosController@descargar');
  Route::get('descargar/bitacora/nuevoresiduos/{id}', 'BitacoraResiduosController@Descargarnuevo');
  Route::get('eliminar/bitacora/residuos/{id}', 'BitacoraResiduosController@eliminar');
  Route::get('/residuos/urbanos/seguridad/get', 'ResiduosUrbanosController@get');
  Route::post('/residuos/urbanos/seguridad/guardar', 'ResiduosUrbanosController@guardar');
  Route::put('/residuos/urbanos/seguridad/actualizar', 'ResiduosUrbanosController@actualizar');
  Route::get('residuos/urbanos/seguridad/descargar/{anio}', 'ResiduosUrbanosController@descargar');
  Route::post('/buscar/articulos/', 'ArticuloController@getArt');
  Route::post('almacen/entradas/pendientes/guardar/', 'EntradasPendientesController@guardar');
  Route::get('almacen/entradas/pendientes/get/', 'EntradasPendientesController@get');
  Route::put('almacen/entradas/almacen/put/', 'EntradasPendientesController@changeAlmacen');
  Route::get('almacen/entradas/pendientes/delete/{id}', 'EntradasPendientesController@delete');
  Route::get('get/partidas/oc/pendientes/{id}', 'EntradasPendientesController@asociar');
  Route::post('guardar/partidas/oc/pendientes/asociado', 'EntradasPendientesController@guardarAsc');
  Route::post('actualizar/entrada/pendiente', 'EntradasPendientesController@actualizarPartida');
  Route::get('/vale/epp/seguridad/emp', 'ValeEppController@get');
  Route::get('/vale/epp/seguridad/emp/eliminar/{id}', 'ValeEppController@eliminar');
  Route::get('/vale/epp/seguridad/emp/detalle/{id}', 'ValeEppController@getDetalle');
  Route::post('get/articulos/descripcion/', 'ValeEppController@getArt');
  Route::post('/vale/epp/seguridad/guardar', 'ValeEppController@guardar');
  Route::get('/vale/epp/seguridad/salidas', 'ValeEppController@ObtenerSalidas');
  Route::get('/vale/epp/seguridad/emp/descargar/{id}', 'ValeEppController@descargar');
  Route::post('/guardar/act/partida/vale/epp', 'ValeEppController@Actualizar');
  Route::get('/get/partidas/epp/almacen', 'ValeEppController@getAlmacen');
  Route::post('entrega/almacen/vale/epp/', 'ValeEppController@guardarEntrega');
  Route::post('get/partida/vale/epp/asignacion', 'ValeEppController@getPartidas');
  Route::get('/vale/epp/seguridad/ver/articulos', 'ValeEppController@getArticulos');
  Route::get('buscar/historico/art/vale/epp/{id}', 'ValeEppController@getArticulosEpp');
  Route::get('exportar/historico/art/vale/epp/{id}', 'ValeEppController@ExportarArtEpp');
  Route::get('get/data/inicial/cb', 'SeguridadController@getCB');
  Route::post('guardar/data/seg/cb', 'SeguridadController@guardarCB');
  Route::get('get/data/seg/cb/fechas/{id}', 'SeguridadController@getFechasCB');
  Route::get('get/data/seg/cb/fechas/excel/{id}', 'SeguridadController@excel');
  //////////////////////////////////////
  Route::post('get/lotes/salida/resguardo', 'SalidaResguardoController@getLotes');
  Route::post('salida/reguardo/guardar/data', 'SalidaResguardoController@Guardar');
  Route::get('get/encabezados/salida/resguardo', 'SalidaResguardoController@getEncabezados');
  Route::get('partidas/salida/resguardo/{id}', 'SalidaResguardoController@getPartidas');
  Route::get('partidas/salida/resguardo/eliminar/{id}', 'SalidaResguardoController@EliminarP');
  Route::post('guardar/retorno/reguardo', 'SalidaResguardoController@RetornoResguardo');
  Route::get('descargar/salida/resguardo/{id}', 'SalidaResguardoController@Descargar');
  //Credenciales RH
  Route::get('/rh/credenciales/generar/{ids}', 'RHCredencialesController@Generar');
  Route::post('/rh/credenciales/guardarimagen', 'RHCredencialesController@GuardarImagenes');
  ///rUTAS PARA NUEVO PROCESO REQUISICION
  Route::get('obtener/proyectos', 'ProyectoController@getAll');
  Route::get('descargar/salidas/excel/{id}', 'SalidaController@descargarExcel');
  //RUTAS PARA NUEVO PROCESO DE VIATICOS
  /** Nuevo análisis de seguridad **/
  // catalogos
  Route::get("/seguridad/catalogos/secuencia/cargar", "CatalogoSeguridadController@ObtenerSecuencias");
  Route::post("/seguridad/catalogos/secuencia/registrar", "CatalogoSeguridadController@RegistrarSecuencia");
  Route::get("/seguridad/catalogos/riesgo/cargar", "CatalogoSeguridadController@ObtenerRiesgos");
  Route::get("/seguridad/catalogos/riesgo/porsecuencia/{s_id}", "CatalogoSeguridadController@RiesgosDeSecuencia");
  Route::post("/seguridad/catalogos/riesgo/registrar", "CatalogoSeguridadController@RegistrarRiesgo");
  Route::get("/seguridad/catalogos/recomendacion/cargar", "CatalogoSeguridadController@ObtenerRecomendaciones");
  Route::get("/seguridad/catalogos/recomendacion/porriesgo/{r_id}", "CatalogoSeguridadController@RecomendacionesDeRiesgo");
  Route::post("/seguridad/catalogos/recomendacion/registrar", "CatalogoSeguridadController@RegistrarRecomendacion");
  //analisis
  Route::get("/seguridad/catalogos/analisis/obtener", "CatalogoSeguridadController@ObtenerAnalisis");
  Route::post("/seguridad/catalogos/analisis/guardar", "CatalogoSeguridadController@GuardarAnalisis");
  Route::get("seguridad/nuevoanalisis/obtenersecuencias", "NuevoSeguridadController@ObtenerSecuencias");
  Route::get("seguridad/nuevoanalisis/obtener", "NuevoSeguridadController@ObtenerAnalisis");
  Route::get("seguridad/nuevoanalisis/obtenerrecomedaciones/{s_id}", "NuevoSeguridadController@ObtenerRecomendaciones");
  Route::post("seguridad/nuevoanalisis/guardar", "NuevoSeguridadController@Guardar");
  Route::get("seguridad/nuevoanalisis/obtenerpartidas/{a_id}", "NuevoSeguridadController@ObtenerPartidas");
  Route::post("seguridad/nuevoanalisis/eliminarpartida", "NuevoSeguridadController@EliminarPartidas");
  Route::post("seguridad/nuevoanalisis/eliminar", "NuevoSeguridadController@EliminarAnalisis");
  Route::get("seguridad/nuevoanalisis/descargar/{a_id}", "NuevoSeguridadController@DescargarFormato");
  Route::get("seguridad/nuevoanalisis/participantes/obtener/{a_id}", "NuevoSeguridadController@ObtenerParticipantes");
  Route::post("seguridad/nuevoanalisis/participantes/guardar", "NuevoSeguridadController@GuardarParticipantes");
  Route::post("seguridad/nuevoanalisis/participantes/eliminar", "NuevoSeguridadController@EliminarParticipantes");
  Route::get('requisicioncomp/serart/{id}', 'PartidasReController@requisicioncompserart');

  /** VEHICULOS **/
  Route::get('/proyecto/salidas/get', 'SalidaController@getProyectos');
  Route::post('alm/cambiar/entrada', 'LoteAlmacenController@cambiarEntrada');
  Route::get('alm/eliminar/entrada/{id}', 'LoteAlmacenController@eliminarPartidaEntrada');
  // Route::get('viaticos1/cargar/{id}', 'SolicitudViaticosController@cargarDatos');
});
