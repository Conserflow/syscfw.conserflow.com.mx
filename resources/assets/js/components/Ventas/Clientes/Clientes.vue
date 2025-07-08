<template>
<main class="main">
    <div class="container-fluid">
        <br>
        <div class="card" v-show="!detallecontacto">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Clientes
                <button v-show="PermisosCRUD.Create" type="button" @click="abrirModal()" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <!-- Inicio ejemplo de tabla Listado -->
            <div class="card-body">
                <vue-element-loading :active="isClientesLoading" />

                <v-client-table ref="myTable" :columns="columns" :data="listClientes" :options="options">
                    <template slot="id" slot-scope="props">
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group dropup" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i> Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                    <button v-show="PermisosCRUD.Update" type="button" @click="abrirModal(false,props.row)" class="dropdown-item">
                                        <i class="icon-pencil"></i>&nbsp; Actualizar cliente.
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                </v-client-table>
            </div>
        </div>
        <!-- Fin ejemplo de tabla Listado -->

        <!--Inicio del modal agregar/actualizar-->
        <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-dark modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <vue-element-loading :active="isLoading" />
                        <div class="container">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label> Nombre</label>
                                    <input type="text" class="form-control" v-validate="'required'" v-model="clientes.nombre" data-vv-name="nombre" placeholder="Nombre">
                                    <span class="text-danger">{{ errors.first('nombre') }}</span>
                                </div>
                                <div class="form-group col-md-6">
                                    <label> RFC</label>
                                    <input type="text" class="form-control" v-validate="'required'" v-model="clientes.rfc" data-vv-name="RFC" placeholder="RFC">
                                    <span class="text-danger">{{ errors.first('RFC') }}</span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Calle </label>
                                    <input type="text" class="form-control" v-validate="'required'" v-model="clientes.calle" data-vv-name="Calle" placeholder="Calle">
                                    <span class="text-danger">{{ errors.first('Calle') }}</span>
                                </div>

                                <div class="form-group col-md-3">
                                    <label>&nbsp;No. Exterior </label>
                                    <input type="text" class="form-control" v-validate="'required'" v-model="clientes.numero_exterior" data-vv-name="No. exterior" placeholder="No. exterior">
                                    <span class="text-danger">{{ errors.first('No. exterior') }}</span>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>&nbsp;No. Interior </label>
                                    <input type="text" class="form-control" v-model="clientes.numero_interior" data-vv-name="No. interior" placeholder="No. interior">
                                    <span class="text-danger">{{ errors.first('No. interior') }}</span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Régimen Fiscal</label>
                                    <select class="form-control" v-validate="'required'" v-model="clientes.regimen_fiscal" data-vv-name="Régimen Fiscal">
                                        <option v-for="(r,i) in list_regimen" :key="i" :value="r.clave">{{r.clave}} - {{r.nombre}}</option>
                                    </select>
                                    <span class="text-danger">{{ errors.first('Régimen Fiscal') }}</span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Código Postal </label>
                                    <input type="text" class="form-control" v-validate="'required'" v-model="clientes.codigo_postal" data-vv-name="Código postal" placeholder="Código postal">
                                    <span class="text-danger">{{ errors.first('Código postal') }}</span>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Colonia </label>
                                    <input type="text" class="form-control" v-validate="'required'" v-model="clientes.colonia" data-vv-name="Colonia" placeholder="Colonia">
                                    <span class="text-danger">{{ errors.first('Colonia') }}</span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Municipio </label>
                                    <input type="text" class="form-control" v-validate="'required'" v-model="clientes.municipio" data-vv-name="Municipio" placeholder="Municipio">
                                    <span class="text-danger">{{ errors.first('Municipio') }}</span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>&nbsp;Entidad federativa </label>
                                    <input type="text" class="form-control" v-validate="'required'" v-model="clientes.entidad_federativa" data-vv-name="Entidad federativa" placeholder="Entidad federativa">
                                    <span class="text-danger">{{ errors.first('Entidad federativa') }}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label> Domicilio alterno</label>
                                <input type="text" class="form-control" data-vv-name="Domicilio Alterno" v-validate="'required'" v-model="clientes.domicilio_alterno" placeholder="Domicilio Alterno">
                                <span class="text-danger">{{ errors.first('Domicilio Alterno') }}</span>

                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputCity">Contacto</label>
                                    <select class="form-control" id="contacto" data-vv-name="contacto" name="contacto" v-model="clientes.contacto">
                                        <option v-for="item in listaContacto" :value="item.nombre_contacto" :key="item.id">{{ item.nombre_contacto }}</option>
                                    </select>

                                </div>
                                <div class="form-group col-md-6">
                                    <label> Teléfono</label>
                                    <input type="text" class="form-control" v-validate="'required'" v-model="clientes.telefono" data-vv-name="Telefono" placeholder="Teléfono">
                                    <span class="text-danger">{{ errors.first('Telefono') }}</span>

                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputCity">Ejecutivo asignado</label>
                                    <select class="form-control" id="empleado_id" data-vv-name="ejecutivo asignado" name="empleado_id" v-model="clientes.ejecutivo_asignado_id">
                                        <option v-for="item in listaEmpleados" :value="item.id" :key="item.id">{{item.nombre}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isLoading" />
                        <div>
                            <button class="btn btn-outline-dark" @click="cerrarModal()"><i class="fas fa-times"></i>&nbsp;Cerrar</button>
                            <button v-if="tipoAccion==1" class="btn btn-secondary" @click="guardar(1)"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                            <button v-if="tipoAccion==2" class="btn btn-secondary" @click="guardar(0)"><i class="fas fa-save"></i>&nbsp;Actualizar</button>
                        </div>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</main>
</template>

<script>

import Utilerias from '../../Herramientas/utilerias.js';
var config = require('../../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            detallecontacto: false,
            PermisosCRUD:
            {},
            tituloModal: '',
            isClientesLoading: false,
            modal: 0,
            columns: [
                'id',
                'nombre',
                'rfc',
                'contacto',
                'telefono',
                'nombre_empleado',
                "regimen_fiscal"
            ],
            listClientes: [],
            tipoAccion: 0,
            optionsvs: [],
            isLoading: false,
            listaEmpleados: [],
            listaContacto: [],
            clientes:
            {
                id: 0,
                nombre: '',
                rfc: '',
                domicilio_fiscal: '',
                domicilio_alterno: 'NA',
                contacto: '',
                telefono: '',
                ejecutivo_asignado_id: '',
                calle: '',
                numero_interior: '',
                numero_exterior: '',
                codigo_postal: '',
                colonia: '',
                municipio: '',
                regimen_fiscal: "",
                entidad_federativa: '',
            },
            options:
            {
                headings:
                {
                    id: 'Acciones',
                    rfc: 'RFC',
                    domicilio_fiscal: 'Domicilio fiscal',
                    domicilio_alterno: 'Domicilio alterno',
                    nombre_empleado: 'Ejecutivo Asignado',
                    telefono: 'Teléfono',
                    cn: 'Contacto',
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            },
            list_regimen: [],
        }
    },

    components:
    {
    },
    methods:
    {
        getData()
        {
            this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
            this.ObtenerClientes();
        },

        /**
         * Obtiene los clientes registrados en la DB
         */
        ObtenerClientes()
        {
            this.isClientesLoading = true;
            axios.get('/clientes').then(res =>
            {
                this.isClientesLoading = false;
                if (res.data.status)
                {
                    this.listClientes = res.data.clientes;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Obtener los empleados registrados para ejecutivo asignado
         */
        ObtenerEmpleados()
        {
            axios.get('generales/empleadoactivos').then(res =>
                {
                    if (res.data.status)
                        this.listaEmpleados = res.data.empleados;
                    else
                        toastr.error(res.data.mensaje);
                })
                .catch(error =>
                {
                    console.log(error);
                });
        },

        /**
         * Mostrar modal de registro/actualización
         */
        abrirModal(nuevo = true, data = {})
        {
            this.ObtenerEmpleados();
            this.ObtenerRegimen();
            this.modal = 1;
            if (nuevo)
            {
                this.tituloModal = 'Registrar Cliente';
                this.tipoAccion = 1;
                this.clientes.domicilio_alterno = "N/D";
            }
            else
            {
                this.tituloModal = 'Actualizar Cliente';
                this.tipoAccion = 2;
                this.clientes.id = data['id'];
                this.clientes.nombre = data['nombre'];
                this.clientes.rfc = data['rfc'];
                this.clientes.domicilio_fiscal = data['domicilio_fiscal'];
                this.clientes.domicilio_alterno = data['domicilio_alterno'];
                this.clientes.contacto = data['contacto'];
                this.clientes.telefono = data['telefono'];
                this.clientes.calle = data['calle'];
                this.clientes.numero_interior = data['numero_interior'];
                this.clientes.numero_exterior = data['numero_exterior'];
                this.clientes.codigo_postal = data['codigo_postal'];
                this.clientes.colonia = data['colonia'];
                this.clientes.municipio = data['municipio'];
                this.clientes.entidad_federativa = data['entidad_federativa'];
                this.clientes.ejecutivo_asignado_id = data['ejecutivo_asignado_id'];
                this.clientes.regimen_fiscal = data['regimen_fiscal'];
            }
        },

        cerrarModal()
        {
            this.modal = 0;
            Utilerias.resetObject(this.clientes);
        },

        /**
         * Registra o actualiza un cliente
         */
        guardar(nuevo)
        {
            this.$validator.validate().then(result =>
            {
                if (result)
                {
                    this.isLoading = true;
                    let aux_alterno = this.clientes.domicilio_alterno;
                    if (aux_alterno == "") aux_alterno = "NA";

                    axios(
                    {
                        method: nuevo ? 'POST' : 'PUT',
                        url: nuevo ? '/clientes' : '/clientes/' + this.clientes.id,
                        data:
                        {
                            'nombre': this.clientes.nombre.toUpperCase(),
                            'rfc': this.clientes.rfc.toUpperCase(),
                            'domicilio_alterno': aux_alterno.toUpperCase(),
                            'contacto': this.clientes.contacto,
                            'regimen_fiscal': this.clientes.regimen_fiscal,
                            'telefono': this.clientes.telefono,
                            // 'ejecutivo_asignado_id': this.clientes.ejecutivo_asignado_id,
                            'calle': this.clientes.calle,
                            'numero_interior': this.clientes.numero_interior,
                            'numero_exterior': this.clientes.numero_exterior,
                            'codigo_postal': this.clientes.codigo_postal,
                            'colonia': this.clientes.colonia,
                            'municipio': this.clientes.municipio,
                            'entidad_federativa': this.clientes.entidad_federativa,
                        }
                    }).then(res =>
                    {
                        this.isLoading = false;
                        if (res.data.status)
                        {
                            this.cerrarModal();
                            this.ObtenerClientes();
                            if (nuevo)
                                toastr.success('Cliente Registrado Correctamente');
                            else
                                toastr.success('Cliente Actualizado Correctamente');
                        }
                        else
                        {
                            toastr.error(res.data.mensaje);
                        }
                    }).catch(error =>
                    {
                        console.log(error);
                    });
                }
            });
        },
        maestro()
        {
            this.detallecontacto = false;
        },
        ObtenerRegimen()
        {
            this.isObtener_loading = true;
            axios.get("ventas/catalogos/regimenfiscal").then(res =>
            {
                if (res.data.status)
                {
                    this.list_regimen = res.data.list_regimen;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });

        }
    },
    mounted()
    {
        this.getData();
    }
}
</script>
