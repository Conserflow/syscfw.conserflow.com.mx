<template>
<main class="main">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Registro de Proveedores - {{anio}}
                <template v-if="true">
                    <div class="dropdown float-sm-right mx-1">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Año
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <button class="dropdown-item" type="button" @click="anio = 2020;ObtenerProveedores();">2020</button>
                            <button class="dropdown-item" type="button" @click="anio = 2021;ObtenerProveedores();">2021</button>
                            <button class="dropdown-item" type="button" @click="anio = 2022;ObtenerProveedores();">2022</button>
                            <button class="dropdown-item" type="button" @click="anio = 2023;ObtenerProveedores();">2023</button>
                            <button class="dropdown-item" type="button" @click="anio = 2024;ObtenerProveedores();">2024</button>
                            <button class="dropdown-item" type="button" @click="anio = 2025;ObtenerProveedores();">2025</button>
                        </div>
                    </div>
                    <button v-show="PermisosCrud.Create" type="button" @click="abrirModal()" class="btn btn-dark  float-sm-right">
                    <i class="fas fa-plus mr-1"></i>Nuevo
                </button>
                <button v-show="PermisosCrud.Download" type="button" @click="DescargarReporte()" class="btn btn-success  float-sm-right mr-1">
                        <i class="fas fa-file-excel"></i> Descargar
                </button>
                </template>
               
            </div>
            <div class="card-body">
                <vue-element-loading :active="isLoading_proveedores" />

                <v-client-table :columns="columns" :data="list_proveedores" :options="options" ref="myTable">
                    <template slot="id" slot-scope="props">
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group dropup" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <button v-show="PermisosCrud.Update" type="button" @click="abrirModal(false,props.row)" class="dropdown-item">
                                        <i class="fas fa-edit"></i>Actualizar proveedor
                                    </button>
                                    <button v-if="props.row.condicion" v-show="PermisosCrud.Delete" type="button" class="dropdown-item" @click="actdesact(props.row.id, 0)">
                                        <i class="fas fa-ban"></i>Desactivar proveedor
                                    </button>
                                    <button v-else type="button" class="dropdown-item" @click="actdesact(props.row.id, 1)">
                                        <i class="fas fa-check"></i>Activar proveedor
                                    </button>
                                    <button type="button" class="dropdown-item" @click="Historial(props.row)">
                                        <i class="fas fa-list-alt"></i>Historial
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template slot="condicion" slot-scope="props">
                        <span v-if="props.row.condicion == 1" class="btn btn-outline-success">Activo</span>
                        <span v-else class="btn btn-outline-danger">Desactivado</span>
                    </template>

                    <template slot="categoria" slot-scope="props">
                        <p v-if="props.row.total_evaluacion == null">N/D</p>
                        <span v-if="props.row.total_evaluacion >= 54" class="text-success">APROBADO</span>
                        <span v-if="props.row.total_evaluacion >= 36 && props.row.total_evaluacion <= 53" class="text-warning">CONDICIONADO</span>
                        <span v-if="props.row.total_evaluacion >= 18 && props.row.total_evaluacion <= 35" class="text-danger">NO APTO</span>
                    </template>

                    <template slot="total_evaluacion" slot-scope="props">
                        <span v-if="props.row.total_evaluacion == null">0</span>
                        <span v-else>{{props.row.total_evaluacion}}</span>
                    </template>

                </v-client-table>
            </div>
        </div>
        <!-- Fin ejemplo de tabla Listado -->
    </div>

    <!--Inicio del modal agregar/actualizar-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <vue-element-loading :active="isLoading" />
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label" for="razon_social">Razón Social</label>
                            <div class="col-md-10">
                                <input type="text" name="razon_social" v-model="proveedor.razon_social" class="form-control" placeholder="Razón Social" autocomplete="off" id="razon_social" data-vv-name="Razon Social" v-validate="'required'">
                                <span class="text-danger">{{ errors.first('razon_social') }}</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 form-control-label" for="nombre">Nombre Comercial</label>
                            <div class="col-md-8">
                                <input type="text" name="nombre" v-model="proveedor.nombre" class="form-control" placeholder="Nombre" autocomplete="off" id="nombre" data-vv-name="Nombre" v-validate="'required'">
                                <span class="text-danger">{{ errors.first('nombre') }}</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 form-control-label" for="giro">Suministro/giro</label>
                            <div class="col-md-6">
                                <!--<input type="text" v-validate="'required'" name="giro" v-model="proveedor.giro" class="form-control" placeholder="Giro" autocomplete="off" id="giro" data-vv-name="Giro">-->
                                <select id="giro" name="giro" class="form-control" v-validate="'required'" v-model="proveedor.giro" data-vv-name="Giro">
                                    <option disabled value="">Seleccione ...</option>
                                    <option v-for="Suministro in list_giro_suministro" 
                                   :key="Suministro" :value="Suministro" >{{ Suministro }}</option>
                                </select>
                                <span class="text-danger">{{ errors.first('giro') }}</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 form-control-label" for="tipo">Tipo</label>
                            <div class="col-md-6">
                                <select class="form-control" v-model="tipo_proveedor">
                                    <option value="1">Nacional</option>
                                    <option value="2">Extranjero</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <template v-if="tipo_proveedor==1">
                                <label class="col-md-2 form-control-label" for="rfc">RFC</label>
                                <div class="col-md-4">
                                    <input @input="ValidarRFC()" type="text" v-validate="'required'" v-model="proveedor.rfc" class="form-control" placeholder="RFC" autocomplete="off" data-vv-name="RFC">
                                    <span class="text-danger">{{ errors.first("RFC") }}</span>
                                    <span v-if="rfc_valido" class="text-success">RFC Válido</span>
                                    <span v-else class="text-danger">RFC Inválido</span>
                                </div>
                            </template>
                            <template v-if="tipo_proveedor==2">
                                <label class="col-md-2 form-control-label" for="taxid">TAX ID</label>
                                <div class="col-md-4">
                                    <input type="text" v-validate="'required'" v-model="proveedor.taxid" class="form-control" placeholder="TAX ID" autocomplete="off" data-vv-name="TAX ID">
                                    <span class="text-danger">{{ errors.first("TAX ID") }}</span>
                                </div>
                            </template>
                            <label class="col-md-2 form-control-label">Nacionalidad</label>
                            <div class="col-md-4">
                                <input type="text" maxlength="75" minlength="3" v-validate="'required'" v-model="proveedor.nacionalidad" class="form-control" data-vv-name="Nacionalidad" autocomplete="off" />
                                <span class="text-danger">{{ errors.first('Nacionalidad') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label" for="regimen">Tipo</label>
                            <div class="col-md-6">
                                <input type="text" v-validate="'required'" name="regimen" v-model="proveedor.regimen_fiscal" class="form-control" placeholder="Tipo" autocomplete="off" id="regimen" data-vv-name="Regimen Fiscal" />
                                <span class="text-danger">{{ errors.first('Regimen Fiscal') }}</span>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 form-control-label" for="regimen">Límite de Crédito</label>
                            <div class="col-md-6">
                                <input type="text" v-validate="'required'" v-model="proveedor.limite_credito" class="form-control" placeholder="" autocomplete="off" data-vv-name="Límite de Crédito" />
                                <span class="text-danger">{{ errors.first('Límite de Crédito') }}</span>

                            </div>
                        </div>
                        <hr>
                        <p class="font-weight-bold h6">Dirección</p>
                        <br>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Calle</label>
                            <div class="col-md-5">
                                <input type="text" maxlength="75" minlength="3" v-validate="'required'" v-model="proveedor.calle" class="form-control" data-vv-name="Calle" autocomplete="off" />
                                <span class="text-danger">{{ errors.first('Calle') }}</span>
                            </div>
                            <label class="col-md-1 form-control-label">Colonia</label>
                            <div class="col-md-4">
                                <input type="text" maxlength="75" minlength="3" v-validate="'required'" v-model="proveedor.colonia" class="form-control" data-vv-name="Colonia" autocomplete="off" />
                                <span class="text-danger">{{ errors.first('Colonia') }}</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">No. Exterior</label>
                            <div class="col-md-2">
                                <input type="text" maxlength="10" v-validate="'required'" v-model="proveedor.no_exterior" class="form-control" data-vv-name="No. Exterior" autocomplete="off" />
                                <span class="text-danger">{{ errors.first('No. Exterior') }}</span>
                            </div>
                            <label class="col-md-1 form-control-label">No. Interior</label>
                            <div class="col-md-2">
                                <input type="text" maxlength="10" v-validate="'required'" v-model="proveedor.no_interior" class="form-control" data-vv-name="No.  Interior" autocomplete="off" />
                                <span class="text-danger">{{ errors.first('No.  Interior') }}</span>
                            </div>
                            <label class="col-md-1 form-control-label">C.P.</label>
                            <div class="col-md-2">
                                <input type="text" maxlength="5" minlength="5" v-validate="'required'" v-model="proveedor.cp" class="form-control" data-vv-name="C.P." autocomplete="off" />
                                <span class="text-danger">{{ errors.first('C.P.') }}</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Municipio</label>
                            <div class="col-md-4">
                                <input type="text" maxlength="75" minlength="3" v-validate="'required'" v-model="proveedor.municipio" class="form-control" data-vv-name="Municipio" autocomplete="off" />
                                <span class="text-danger">{{ errors.first('Municipio') }}</span>
                            </div>
                            <label class="col-md-1 form-control-label">Estado</label>
                            <div class="col-md-5">
                                <input type="text" maxlength="75" minlength="3" v-validate="'required'" v-model="proveedor.estado" class="form-control" data-vv-name="Estado" autocomplete="off" />
                                <span class="text-danger">{{ errors.first('Estado') }}</span>
                            </div>
                        </div>
                        <template v-if="tipoAccion==1">
                            <hr>
                            <p class="font-weight-bold h6">Datos Bancarios</p>
                            <br>
                            <div class="form-group row">
                                <label class="col-md-2 form-control-label" for="banco_transferencia">Banco</label>
                                <div class="col-md-4">
                                    <input v-validate="'required'" type="text" maxlength="50" name="banco_transferencia" v-model="temp2_proveedor_banco" class="form-control" placeholder="Banco de transferencia" autocomplete="off" id="banco_transferencia" data-vv-name="banco transferencia">
                                    <span class="text-danger">{{ errors.first('banco transferencia') }}</span>
                                </div>
                                <label class="col-md-1 form-control-label" for="numero_cuenta">Cuenta</label>
                                <div class="col-md-5">
                                    <input type="text" v-validate="'required'" maxlength="50" pattern="^[0-9]+" name="numero_cuenta" v-model="temp2_proveedor_cuenta" class="form-control" placeholder="Número de cuenta" autocomplete="off" id="numero_cuenta" data-vv-name="número cuenta">
                                    <span class="text-danger">{{ errors.first('número cuenta') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 form-control-label" for="clabe">Clabe</label>
                                <div class="col-md-4">
                                    <input type="text" v-validate="'required'" maxlength="50" pattern="^[0-9]+" name="clabe" v-model="temp2_proveedor_clabe" class="form-control" placeholder="Clabe" autocomplete="off" id="clabe" data-vv-name="clabe">
                                    <span class="text-danger">{{ errors.first('clabe') }}</span>
                                </div>
                                <label class="col-md-1 form-control-label" for="clabe">Moneda</label>
                                <div class="col-md-3">
                                    <select v-validate="'required'" v-model="temp2_proveedor_moneda" class="form-control" data-vv-name="moneda">
                                        <option value="MXN">MXN</option>
                                        <option value="USD">USD</option>
                                        <option value="EUR">EUR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 form-control-label" for="clabe">Condiciones de pago</label>
                                <div class="col-md-4">
                                    <input v-validate="'required'" type="text" maxlength="50" v-model="temp2_proveedor_condiciones" class="form-control" placeholder="Condiciones de pago" autocomplete="off" data-vv-name="Condiciones">
                                    <span class="text-danger">{{ errors.first('Condiciones') }}</span>
                                </div>
                            </div>
                        </template>
                        <hr>
                        <p class="font-weight-bold h6">Contacto</p>
                        <br>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label" for="pagina">Página Web</label>
                            <div class="col-md-6">
                                <input maxlength="100" type="text" v-validate="'required'" v-model="proveedor.pagina" class="form-control" placeholder="Página" autocomplete="off" data-vv-name="Página Web">
                                <span class="text-danger">{{ errors.first('Página Web') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Contacto de Ventas</label>
                            <div class="col-md-5">
                                <input type="text" maxlength="45" minlength="3" v-validate="'required'" v-model="proveedor.ventas_contacto" class="form-control" data-vv-name="Contacto de Ventas" autocomplete="off" />
                                <span class="text-danger">{{ errors.first('Contacto de Ventas') }}</span>
                            </div>
                            <label class="col-md-1 form-control-label">Teléfono</label>
                            <div class="col-md-4">
                                <input type="text" maxlength="10" v-validate="'required'" v-model="proveedor.ventas_telefono" class="form-control" data-vv-name="Teléfono de Ventas" autocomplete="off" />
                                <span class="text-danger">{{ errors.first('Teléfono de Ventas') }}</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Correo</label>
                            <div class="col-md-5">
                                <input type="text" maxlength="45" minlength="3" v-validate="'required'" v-model="proveedor.ventas_correo" class="form-control" data-vv-name="Correo de Ventas" autocomplete="off" />
                                <span class="text-danger">{{ errors.first('Correo de Ventas') }}</span>
                            </div>
                            <label class="col-md-1 form-control-label">Celular</label>
                            <div class="col-md-4">
                                <input type="text" maxlength="10" v-validate="'required'" v-model="proveedor.ventas_celular" class="form-control" data-vv-name="Celular de Ventas" autocomplete="off" />
                                <span class="text-danger">{{ errors.first('Celular de Ventas') }}</span>
                            </div>
                        </div>
                        <br>

                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Contacto Facturación</label>
                            <div class="col-md-5">
                                <input type="text" maxlength="45" minlength="3" v-validate="'required'" v-model="proveedor.facturacion_contacto" class="form-control" data-vv-name="Contacto de Facturación" autocomplete="off" />
                                <span class="text-danger">{{ errors.first('Contacto de Facturación') }}</span>
                            </div>
                            <label class="col-md-1 form-control-label">Teléfono</label>
                            <div class="col-md-4">
                                <input type="text" maxlength="10" v-validate="'required'" v-model="proveedor.facturacion_telefono" class="form-control" data-vv-name="Teléfono de Facturación" autocomplete="off" />
                                <span class="text-danger">{{ errors.first('Teléfono de Facturación') }}</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Correo</label>
                            <div class="col-md-5">
                                <input type="text" maxlength="45" v-validate="'required'" v-model="proveedor.facturacion_correo" class="form-control" data-vv-name="Correo de Facturación" autocomplete="off" />
                                <span class="text-danger">{{ errors.first('Correo de Facturación') }}</span>
                            </div>

                            <label class="col-md-1 form-control-label">Celular</label>
                            <div class="col-md-4">
                                <input type="text" maxlength="10" v-validate="'required'" v-model="proveedor.facturacion_celular" class="form-control" data-vv-name="Celular de Facturación" autocomplete="off" />
                                <span class="text-danger">{{ errors.first('Celular de Facturación') }}</span>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Modificación</label>
                            <div class="col-md-9 ml-4">
                                <!--
                                <textarea rows="3" maxlength="300" v-model="proveedor.modificacion" class="form-control"></textarea>
                                -->
                                <div v-for="(item, index) in list_modificaciones" :key="index" class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                    :id="'mod-' + index"
                                    :value="item"
                                    v-model="list_modificaciones_db"
                                    />
                                    <label class="form-check-label" :for="'mod-' + index">{{ item }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Documentos</label>
                            <div class="col-md-9 ml-4">
                                <div v-for="(item, index) in list_tipos_documentos" :key="index" class="form-check">
                                    <input class="form-check-input" type="checkbox" :id="'doc-' + index"
                                        :value="item" v-model="list_tipos_documentos_db" />
                                    <label class="form-check-label" :for="'doc-' + index">{{ item }}</label>
                                </div>
                            </div>
                        </div>                        

                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Anexos</label>
                            <div class="col-md-9">
                                <textarea rows="3" maxlength="300" v-model="proveedor.anexos" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="form-group row" v-show="tipoAccion==2">

                            <div class="col-md-11 mx-auto">
                                <div class="form-row">
                                    <div class="col mx-auto">
                                        <label class="font-weight-bold">Datos bancarios</label>
                                    </div>

                                    <div class="col">
                                        <button type="button" @click="AbrirModalBancos(1)" class="btn btn-dark  mb-3 float-sm-right">
                                            <i class="fas fa-plus"></i>Agregar
                                        </button>
                                    </div>
                                </div>
                                <v-client-table :columns="columnsProvedores" :data="ListBancos" :options="optionsProveedores">
                                </v-client-table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" @click="cerrarModal()"><i class="fas fa-window-close mr-1"></i>Cerrar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-secondary" @click="GuardarProveedor(true)"><i class="fas fa-save mr-1"></i>Guardar</button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-secondary" @click="GuardarProveedor(false)"><i class="fas fa-save mr-1"></i>Actualizar</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->

    <!-- Modal registro de bancos -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modalProveedor}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Bancos del Proveedor</h4>
                    <button type="button" class="close" @click="CerrarModalProveedores()" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-2 form-control-label" for="banco_transferencia">Banco</label>
                        <div class="col-md-9">
                            <input type="text" maxlength="50" name="banco_transferencia" v-model="temp_proveedor_banco" class="form-control" placeholder="Banco de transferencia" autocomplete="off" id="banco_transferencia" data-vv-name="banco transferencia">
                            <span class="text-danger">{{ errors.first('banco_transferencia') }}</span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 form-control-label" for="numero_cuenta">Cuenta</label>
                        <div class="col-md-9">
                            <input type="text" maxlength="50" pattern="^[0-9]+" name="numero_cuenta" v-model="temp_proveedor_cuenta" class="form-control" placeholder="Número de cuenta" autocomplete="off" id="numero_cuenta" data-vv-name="número cuenta">
                            <span class="text-danger">{{ errors.first('numero_cuenta') }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 form-control-label" for="clabe">Clabe</label>
                        <div class="col-md-9">
                            <input type="text" maxlength="50" pattern="^[0-9]+" name="clabe" v-model="temp_proveedor_clabe" class="form-control" placeholder="Clabe" autocomplete="off" id="clabe" data-vv-name="clabe">
                            <span class="text-danger">{{ errors.first('clabe') }}</span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 form-control-label" for="clabe">Condiciones de pago</label>
                        <div class="col-md-9">
                            <input type="text" maxlength="50" v-model="temp_proveedor_condiciones" class="form-control" placeholder="Condiciones de pago" autocomplete="off" data-vv-name="condiciones">
                            <span class="text-danger">{{ errors.first('Condiciones') }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 form-control-label" for="clabe">Moneda</label>
                        <div class="col-md-3">
                            <select v-model="temp_proveedor_moneda" class="form-control" data-vv-name="moneda">
                                <option value="MXN">MXN</option>
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" @click="CerrarModalProveedores()"><i class="fas fa-window-close mr-1"></i>Cancelar</button>
                    <button v-if="tipo_guardar==1" type="button" class="btn btn-secondary" @click="guardarProveedoresTemp(1)"><i class="fas fa-save mr-1"></i>Guardar</button>
                    <button v-else type="button" class="btn btn-secondary" @click="guardarProveedoresTemp(2)"><i class="fas fa-save mr-1"></i>Actualizar</button>
                </div>
            </div>

            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <!-- Modal Historial -->
    <div class="modal fade" tabindex="-1" :class="{ mostrar: ver_modal_historial }" role="dialog" aria-labelledby="myModalLabel" style="display: none" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title">{{nombre_proveedor}}</h4>
                        <button type="button" class="close" @click="CerrarModalHistorial()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isObtenerHistorial_loading" />
                        <div class="container">
                            <div v-if="list_historial.length>0">
                                <table class="table table-sm">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Fecha</th>
                                            <th scope="col">Tipo de Movimiento</th>
                                            <th scope="col">Modificación</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr :key="i" v-for="(h,i) in list_historial">
                                            <th scope="row">{{i+1}}</th>
                                            <td>{{h.fecha}}</td>
                                            <td>{{h.tipo_movimiento}}</td>
                                            <td>{{h.modificacion}}</td>
                                            <td>
                                                <button class="btn btn-sm btn-dark" @click="DescargarHistorial(h.id)">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else>
                                <h5 class="text-center">Sin Cambios</h5>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" @click="CerrarModalHistorial()">
                            <i class="fas fa-window-close"></i>
                            &nbsp;Cerrar
                        </button>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</main>
</template>

<style>
.form-check-label {
    padding-left: 0.1rem;
}
</style>

<script>
import Utilerias from '../../Herramientas/utilerias.js';
var config = require('../../Herramientas/config-vuetables-client').call(this);
export default
{
    data()
    {
        return {
            anio: 2024,
            n_temp: 0,
            banco_edit:
            {},
            tipo_guardar: 1,
            ListBancos_temp: [],
            ListBancos: [],
            temp2_proveedor_cuenta: '',
            temp2_proveedor_clabe: '',
            temp2_proveedor_condiciones: "",
            temp2_proveedor_moneda: "MXN",
            temp2_proveedor_banco: '',

            temp_proveedor_cuenta: '',
            temp_proveedor_clabe: '',
            temp_proveedor_condiciones: "",
            temp_proveedor_moneda: "MXN",
            temp_proveedor_banco: '',
            rfc_valido: false,
            isLoading_proveedores: false,
            url: '/proveedores',
            columnsProvedores: ["banco", "cuenta", "clabe", "moneda", "condiciones"],
            tableDataProveedores: [],
            optionsProveedores:
            {
                headings:
                {
                    clabe: "Clabe",
                    condiciones: "Condición de pago",
                    cuenta: "No. de Cuenta",
                },
                perPage: 20,
                perPageValues: [],
                skin: config.skin,
                texts: config.texts,
                filterable: false,
            },
            modalProveedor: 0,
            tipo_proveedor: 1,
            PermisosCrud:
            {},
            proveedor:
            {

            },
            listaProveedores: [],
            modal: 0,
            tituloModal: '',
            tipoAccion: 0,
            isLoading: false,
            columns: [
                'id',
                'idControl',
                'nombre',
                'razon_social',
                "rfc",
                "contacto",
                'categoria',
                "condicion"
            ],
            list_proveedores: [],
            options:
            {
                headings:
                {
                    id: 'Acciones',
                    idControl: 'ID',
                    nombre: 'Nombre Comercial',
                    razon_social: 'Razón Social',
                    direccion: 'Dirección',
                    condicion: 'Estado',
                },
                perPage: 20,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            },
            /**Modal historial */
            list_historial: [],
            nombre_proveedor: "",
            ver_modal_historial: false,
            isObtenerHistorial_loading: false,
            /* Listas de opciones */
            list_giro_suministro: [],
            list_modificaciones: [],
            list_modificaciones_db:[],
            list_tipos_documentos: [],
            list_tipos_documentos_db: [],
        }
    },
    computed:
    {},
    methods:
    {
        /**
         * Obtener todos los proveedores
         */
        ObtenerProveedores()
        {
            this.isLoading_proveedores = true;
            axios.get("/compras/proveedores/obtener/" + this.anio).then(res =>
            {
                this.isLoading_proveedores = false;
                if (res.data.status)
                {
                    this.list_proveedores = res.data.proveedores;
                }
                else
                    toastr.error(res.data.mensaje);
            });
        },

        /**
         * Registra o actualiza el proveedore actual
         */
        async GuardarProveedor(nuevo)
        {
            let isValid = await this.$validator.validate();
            if (!isValid) return;
            if (this.tipo_proveedor == 1 && !this.rfc_valido) // Solo valida rfc en nacionales
            {
                toastr.warning("Ingrese un RFC válido");
                return;
            }
            let data = new FormData();
            this.isLoading = true;

            this.proveedor.tipos_modificacion = this.list_modificaciones_db.join(',');
            this.proveedor.tipos_documentos = this.list_tipos_documentos_db.join(',');

            if (!nuevo)
                data.append("id", this.proveedor.id);
            data.append("nombre", this.proveedor.nombre);
            data.append("direccion", this.proveedor.direccion);
            data.append("razon_social", this.proveedor.razon_social);
            data.append("giro", this.proveedor.giro);
            if (this.tipo_proveedor == 1)
                data.append("rfc", this.proveedor.rfc);
            else
                data.append("taxid", this.proveedor.taxid);
            data.append("pagina", this.proveedor.pagina);
            data.append("lista_bancos", JSON.stringify(this.ListBancos.filter(b => b.temp)));
            data.append("regimen_fiscal", this.proveedor.regimen_fiscal);
            data.append("limite_credito", this.proveedor.limite_credito);
            data.append("calle", this.proveedor.calle);
            data.append("no_exterior", this.proveedor.no_exterior);
            data.append("no_interior", this.proveedor.no_interior);
            data.append("estado", this.proveedor.estado);
            data.append("cp", this.proveedor.cp);
            data.append("nacionalidad", this.proveedor.nacionalidad);
            data.append("colonia", this.proveedor.colonia);
            data.append("municipio", this.proveedor.municipio);
            data.append("ventas_contacto", this.proveedor.ventas_contacto);
            data.append("ventas_telefono", this.proveedor.ventas_telefono);
            data.append("ventas_celular", this.proveedor.ventas_celular);
            data.append("ventas_correo", this.proveedor.ventas_correo);
            data.append("facturacion_contacto", this.proveedor.facturacion_contacto);
            data.append("facturacion_telefono", this.proveedor.facturacion_telefono);
            data.append("facturacion_celular", this.proveedor.facturacion_celular);
            data.append("facturacion_correo", this.proveedor.facturacion_correo);
            data.append("modificacion", this.proveedor.modificacion);
            data.append("anexos", this.proveedor.anexos);
            data.append("tipos_modificacion", this.proveedor.tipos_modificacion);
            data.append("tipos_documentos", this.proveedor.tipos_documentos);
            // Banco inicial
            data.append("temp2_proveedor_cuenta", this.temp2_proveedor_cuenta);
            data.append("temp2_proveedor_clabe", this.temp2_proveedor_clabe);
            data.append("temp2_proveedor_condiciones", this.temp2_proveedor_condiciones);
            data.append("temp2_proveedor_moneda", this.temp2_proveedor_moneda);
            data.append("temp2_proveedor_banco", this.temp2_proveedor_banco);

            axios.post(this.url, data).then(res =>
            {
                this.isLoading = false;
                if (res.data.status)
                {
                    this.cerrarModal();
                    this.ObtenerProveedores();
                    if (nuevo)
                    {
                        toastr.success('Proveedor Registrado Correctamente');
                    }
                    else
                    {
                        toastr.success('Proveedor Actualizado Correctamente');
                    }
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Activar o desactivar el proveedor seleccionado
         */
        actdesact(id, activar)
        {
            let data = new FormData();
            data.append("id", id);
            data.append("condicion", activar);
            axios.post("compras/proveedores/activar", data).then(res =>
            {
                if (res.data.status)
                {
                    toastr.success("Actualizado correctamente");
                    this.ObtenerProveedores();
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Cerrar modal de proveedores
         */
        cerrarModal()
        {
            this.ListBancos = [];
            this.modal = 0;
            this.tituloModal = '';
            this.n_temp = 0;
            let tempBancos = this.ListBancos.filter(b => b.id > 100);

            tempBancos.forEach(b =>
            {
                let s = this.ListBancos.indexOf(b);
                const index = this.ListBancos.indexOf(b);
                this.ListBancos.splice(index, 1);

            });
            this.proveedor = {};
        },

        LimpiarProveedor()
        {
            this.proveedor = {
                razon_social: "",
                nombre: "N/D",
                giro: "N/D",
                // rfc: "XAXX010101000",
                rfc: "",
                regimen_fiscal: "N/D",
                nacionalidad: "N/D",
                regimen: "N/D",
                calle: "N/D",
                colonia: "N/D",
                no_exterior: "N/D",
                no_interior: "N/D",
                cp: "00000",
                municipio: "N/D",
                limite_credito: 0,
                estado: "N/D",
                pagina: "N/D",
                ventas_contacto: "N/D",
                ventas_telefono: "N/D",
                ventas_correo: "N/D",
                ventas_celular: "-",
                facturacion_contacto: "N/D",
                facturacion_telefono: "-",
                facturacion_correo: "-",
                facturacion_celular: "-",
                modificacion: "-",
                anexos: "-",
                tiposdocumentos:"",
                tipos_modificacion:"",
                tipos_documentos: "",
            };
            this.temp2_proveedor_cuenta = '000000';
            this.temp2_proveedor_clabe = '000000';
            this.temp2_proveedor_condiciones = "N/D";
            this.temp2_proveedor_moneda = "MXN";
            this.temp2_proveedor_banco = 'N/D';
            this.temp_proveedor_cuenta = 'N/D';
            this.temp_proveedor_clabe = 'N/D';
            this.temp_proveedor_condiciones = "N/D";
            this.temp_proveedor_moneda = "MXN";
            this.temp_proveedor_banco = 'N/D';
            this.list_giro_suministro = [];
            this.list_modificaciones = [];
            this.list_tipos_documentos= [];
        },

        /**
         * Abrir modal para el registro/actualización del proveedore
         */
        abrirModal(nuevo = true, data = {})
        {
            this.LimpiarProveedor();
            this.modal = 1;
            this.n_temp = 100;
            if (nuevo) // Registar
            {
                this.ListBancos = [];
                this.tituloModal = 'Registrar Nuevo proveedor';
                this.tipoAccion = 1;
                this.list_modificaciones_db = [];
                this.list_tipos_documentos_db = [];
                this.CargarSuministrosGiros("", true);
                this.CargarModificaciones("",true);
                this.CargarTipoDocumento("", true);
            }
            else
            {
                // Actualizar
                this.ListBancos = data["bancos"];
                this.tituloModal = 'Actualizar proveedor';
                this.tipoAccion = 2;
                this.proveedor = {
                    ...data,
                    anexos: "-",
                    modificacion: "ACTUALIZACIÓN DE DATOS"
                };
                this.ValidarRFC();
                this.CargarSuministrosGiros(data.giro, false);
                this.CargarModificaciones(this.proveedor.tipos_modificacion,false);
                this.CargarTipoDocumento(this.proveedor.tipos_documentos, false);
            }

            
        },

        /**
         * Abrir modal de registro de bancos de proveedor
         */
        AbrirModalBancos(nuevo, model)
        {
            this.modalProveedor = 1;
            this.temp_proveedor_condiciones = "-"
            this.temp_proveedor_moneda = "MXN";
            if (nuevo == 1) //Crear nuevo
            {
                this.tipo_guardar = 1;
            }
            else //actualizar
            {
                this.tipo_guardar = 2;
                this.banco_edit = model;
                this.temp_proveedor_banco = model.banco;
                this.temp_proveedor_clabe = model.clabe;
                this.temp_proveedor_cuenta = model.cuenta;
            }
        },

        /**
         * Cerrar modal de registro de bancos
         */
        CerrarModalProveedores()
        {
            this.modalProveedor = 0;
            this.ListBancos_temp = [];
            // this.ListBancos = [];
        },

        /**
         * Guardar temporalmente el banco creado
         */
        guardarProveedoresTemp(tipo)
        {
            let t = this;
            if (t.temp_proveedor_banco == '')
            {
                toastr.warning("Ingrese un banco");
                return;
            }
            if (t.temp_proveedor_cuenta == '')
            {
                toastr.warning("Ingrese una cuenta");
                return;
            }
            if (t.temp_proveedor_clabe == '')
            {
                toastr.warning("Ingrese una clabe");
                return;
            }

            if (tipo == 1) //nuevo
            {
                // Guardar temporal
                let nuevo = {
                    id: t.n_temp,
                    banco: t.temp_proveedor_banco,
                    cuenta: t.temp_proveedor_cuenta,
                    clabe: t.temp_proveedor_clabe,
                    condiciones: t.temp_proveedor_condiciones,
                    moneda: t.temp_proveedor_moneda,
                    temp: true
                };
                this.ListBancos.push(nuevo);
                t.temp_proveedor_banco = "";
                t.temp_proveedor_cuenta = "";
                t.temp_proveedor_clabe = "";
                this.CerrarModalProveedores();
                toastr.info("Banco registrado temporalmente");
                toastr.success("Presione Actualizar par guardar el banco");
                t.n_temp += 1;
            }
            else // actualizar
            {
                let anterior = t.banco_edit;
                let nuevo = {
                    id: t.banco_edit.id,
                    banco: t.temp_proveedor_banco,
                    cuenta: t.temp_proveedor_cuenta,
                    clabe: t.temp_proveedor_clabe,
                    proveedor_id: t.banco_edit.proveedor_id,
                };
                const index = this.ListBancos.findIndex(b => b.id == t.banco_edit.id);
                if (index >= 0)
                {
                    this.ListBancos.splice(index, 1, nuevo);
                }
                else
                {
                    toastr.error("Datos bancarios no encontrados");
                }
                t.temp_proveedor_banco = "";
                t.temp_proveedor_cuenta = "";
                t.temp_proveedor_clabe = "";
                this.CerrarModalProveedores();
            }

        },

        /**
         * Desactivar el banco seleccionado
         */
        DesactivarBanco(model)
        {
            if (model.temp)
            {
                //temporal
                const index = this.ListBancos.indexOf(model);
                this.ListBancos.splice(index, 1);
                toastr.success("Banco temporal eliminado");
                this.CerrarModalProveedores();
            }
        },

        /**
         * Descargar el reporte de los proveedores
         */
        DescargarReporte()
        {
            window.open("compras/reportes/catalogoproveedores/" + this.anio, '_blank');
        },

        /**
         * Indica di el RC es valido o no
         */
        ValidarRFC()
        {
            let rfc = this.proveedor.rfc;
            this.rfc_valido = this.AuxValidarRFC(rfc, false);
        },

        /**
         * Validar RFC
         */
        AuxValidarRFC(rfc, aceptarGenerico = true)
        {
            return true;
            const re = /^([A-ZÑ&]{3,4}) ?(?:- ?)?(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])) ?(?:- ?)?([A-Z\d]{2})([A\d])$/;
            var validado = rfc.match(re);

            if (!validado) //Coincide con el formato general del regex?
                return false;

            //Separar el dígito verificador del resto del RFC
            const digitoVerificador = validado.pop(),
                rfcSinDigito = validado.slice(1).join(''),
                len = rfcSinDigito.length,

                //Obtener el digito esperado
                diccionario = "0123456789ABCDEFGHIJKLMN&OPQRSTUVWXYZ Ñ",
                indice = len + 1;
            var suma,
                digitoEsperado;

            if (len == 12) suma = 0
            else suma = 481; //Ajuste para persona moral

            for (var i = 0; i < len; i++)
                suma += diccionario.indexOf(rfcSinDigito.charAt(i)) * (indice - i);
            digitoEsperado = 11 - suma % 11;
            if (digitoEsperado == 11) digitoEsperado = 0;
            else if (digitoEsperado == 10) digitoEsperado = "A";

            //El dígito verificador coincide con el esperado?
            // o es un RFC Genérico (ventas a público general)?
            if ((digitoVerificador != digitoEsperado) &&
                (!aceptarGenerico || rfcSinDigito + digitoVerificador != "XAXX010101000"))
                return false;
            else if (!aceptarGenerico && rfcSinDigito + digitoVerificador == "XEXX010101000")
                return false;
            return true
        },

        /**
         * Obtener el historial de los cambios del proveedore seleccioando
         */
        Historial(proveedor)
        {
            this.nombre_proveedor = proveedor.nombre;
            let id = proveedor.id;
            this.ver_modal_historial = true;
            this.isObtenerHistorial_loading = true;
            axios.get("compras/proveedores/historial/" + id).then(res =>
            {
                this.isObtenerHistorial_loading = false;
                if (res.data.status)
                {
                    this.list_historial = res.data.historial;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        CerrarModalHistorial()
        {
            this.ver_modal_historial = false;
            this.list_historial = [];
        },

        /**
         * Descargar el formato de alta/modificacion
         */
        DescargarHistorial(id)
        {
            window.open("compras/proveedores/descargarhistorial/" + id);
        },

        DescargarReporteTemp()
        {
            window.open("compras/reportes/proveedores2");
        },
        CargarSuministrosGiros(giroRegistrado, nuevo=false){

            let suministros =["ALIMENTOS","CALIBRACIÓN DE EQUIPOS","CERTIFICACIÓN","CERTIFICADOS MÉDICOS"
            ,"COMBUSTIBLE","CONSUMIBLES / HERRAMIENTAS","CURSOS","EPP","EQUIPOS DE SEGURIDAD Y/O EMERGENCIA"
            ,"FARMACIA","FLETE / PAQUETERÍA / LOGISTICA","HIDRATACIÓN","HOSPEDAJE","INSTRUMENTOS","LABORATORIO DE ENSAYO"
            ,"LIMPIEZA","MANTENIMIENTO EQUIPOS Y HERRAMIENTAS","MATERIAL CIVIL","MATERIAL ELÉCTRICO","MATERIAL ESTRUCTURAL"
            ,"MATERIAL MECÁNICO","MATERIAL NEUMÁTICO","MATERIAL PARA EMBALAJE","PAPELERÍA / INFORMÁTICA / IMPRENTA","PINTURA"
            ,"PROGRAMA DE PROTECCIÓN CIVIL","PRUEBAS DE CALIDAD (PNDS)","RECOLECCIÓN DE RESIDUOS","SANITARIOS PORTÁTILES"
            ,"SERVICIO GRÚAS / MONTACARGAS","SERVICIOS MAQUINADO","SERVICIOS PARA LIBRANZA","VEHÍCULOS"]
        
            if (!nuevo) {
                if (giroRegistrado && !suministros.some(s => s === giroRegistrado.toUpperCase()))
                {
                    suministros.push(giroRegistrado);
                }
            }

            this.list_giro_suministro = suministros.sort();
          
        },
        CargarModificaciones(smodificaciones, nuevo = false){

            let motivoModificaciones = ["Cambio de domicilio","Cambio datos bancarios"
            ,"Cambio de datos de contacto de ventas","Cambio de datos de contacto de facturación"]

            if (!nuevo) {

                this.list_modificaciones_db = smodificaciones ? smodificaciones.split(',') : []
            }

            this.list_modificaciones = motivoModificaciones
        },
        CargarTipoDocumento(sdocumentos, nuevo = false) {

            let tiposDocumentos = ["Cambio de datos de contacto de ventas", "Caratula bancaría"
                , "Aplicables conforme a los criterios adicionales (PCO-02/F-05)"]

            if (!nuevo) {
                this.list_tipos_documentos_db = sdocumentos ? sdocumentos.split(',') : []
            }

            this.list_tipos_documentos = tiposDocumentos

        },
    },
    mounted()
    {
        this.PermisosCrud = Utilerias.getCRUD(this.$route.path);
        this.ObtenerProveedores();
    }
}
</script>
