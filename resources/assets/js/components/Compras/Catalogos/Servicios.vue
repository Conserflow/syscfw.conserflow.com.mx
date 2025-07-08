<template>
<main class="main">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Servicios
                <button type="button" @click="abrirModal()" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus"></i>Nuevo
                </button>
            </div>
            <vue-element-loading :active="isLoading_servicios" />
            <div class="card-body">
                <v-client-table :columns="columns" :data="list_servicios" :options="options" ref="myTable">
                    <template slot="id" slot-scope="props">
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group dropup" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i> Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <button type="button" @click="abrirModal(false,props.row)" class="dropdown-item">
                                        <i class="fas fa-edit"></i>Actualizar servicio.
                                    </button>
                                </div>
                            </div>
                        </div>

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
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isLoading" />
                        <div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Nombre del Servicio</label>
                                <div class="col-md-9">
                                    <input type="text" data-vv-name="Nombre" v-validate="'required'" name="nombre_servicio" v-model="servicio.nombre_servicio" class="form-control" placeholder="Nombre del Servicio" autocomplete="off">
                                    <span class="text-danger">{{ errors.first('Nombre') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Marca/Proveedor</label>
                                <div class="col-md-9">
                                    <input type="text" v-validate="'required'" data-vv-name="Marca/Proveedor" v-model="servicio.proveedor_marca" class="form-control" placeholder="Marca/Proveedor" autocomplete="off">
                                    <span class="text-danger">{{ errors.first('Marca/Proveedor') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Unidad de Medida</label>
                                <div class="col-md-9">
                                    <input disabled type="text" v-validate="'required'" name="unidad_medida" v-model="servicio.unidad_medida" class="form-control" placeholder="Unidad de Medida" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <!-- </form> -->
                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isLoading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="cerrarModal()"><i class="fas fa-window-close"></i>Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-secondary" @click="guardarServicio(1)"><i class="fas fa-save"></i>Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-secondary" @click="guardarServicio(0)"><i class="fas fa-save"></i>Actualizar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->
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
            url: '/catservicios',
            desabilitado: 0,
            servicio:
            {
                id: 0,
                nombre_servicio: '',
                proveedor_marca: '',
                unidad_medida: 'Servicio',
            },

            modal: 0,
            tituloModal: '',
            tipoAccion: 0,
            isLoading: false,
            isLoading_servicios: false,
            columns: ['id', 'nombre_servicio', 'proveedor_marca', 'unidad_medida'],
            list_servicios: [],
            options:
            {
                headings:
                {
                    'id': 'Acción',
                    'nombre_servicio': 'Nombre del Servicio',
                    'proveedor_marca': 'Marca/Servicio',
                    'unidad_medida': 'Unidad de Medida',

                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            },
        }
    },
    computed:
    {},
    methods:
    {
        /**
         * Obtener todos los servicios
         */
        ObtenerServicios()
        {
            this.isLoading_servicios = true;
            axios.get(this.url).then(res =>
            {
                this.isLoading_servicios = false;
                if (res.data.status)
                {
                    this.list_servicios = res.data.servicios;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },
        /**
         * Registrar o actualizar el servicio
         */
        guardarServicio(nuevo)
        {
            this.$validator.validate().then(result =>
            {
                if (result)
                {
                    this.isLoading = true;
                    axios(
                    {
                        method: nuevo ? 'POST' : 'PUT',
                        url: nuevo ? this.url : this.url + '/' + this.id,
                        data:
                        {
                            'id': this.servicio.id,
                            'nombre_servicio': this.servicio.nombre_servicio,
                            'proveedor_marca': this.servicio.proveedor_marca,
                            'unidad_medida': this.servicio.unidad_medida,
                        }
                    }).then(res =>
                    {
                        this.isLoading = false;
                        if (res.data.status)
                        {
                            this.cerrarModal();
                            this.ObtenerServicios();
                            if (nuevo)
                            {
                                toastr.success('Servicio Agregado Correctamente');
                            }
                            else
                            {
                                toastr.success('Servicio Actualizado Correctamente');
                            }
                        }
                        else
                        {
                            toastr.error(res.data.mensaje);
                        }
                    });
                }
            });
        },

        /**
         * Cerrar modal
         */
        cerrarModal()
        {
            this.modal = 0;
            this.tituloModal = '';
            Utilerias.resetObject(this.servicio);
        },

        /**
         * Abrir modal para registro/actualización del servicio
         */
        abrirModal(nuevo = true, data = [])
        {
            this.modal = 1;
            if (nuevo)
            {
                this.tipoAccion = 1;
                this.tituloModal = 'Registrar Servicio';
                this.servicio.unidad_medida = 'Servicio';
            }
            else
            {
                this.tipoAccion = 2;
                this.tituloModal = 'Actualizar Servicio';
                this.servicio.id = data['id'];
                this.servicio.nombre_servicio = data['nombre_servicio'];
                this.servicio.proveedor_marca = data['proveedor_marca'];
                this.servicio.unidad_medida = data['unidad_medida'];
            }
        }
    },

    mounted()
    {
        this.ObtenerServicios();
    }
}
</script>
