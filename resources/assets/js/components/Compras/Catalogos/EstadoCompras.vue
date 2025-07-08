<template>
<main class="main">
    <div class="container-fluid">
        <br>
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Estado de compras
                <button type="button" @click="abrirModal()" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus"></i>Nuevo
                </button>
            </div>
            <div class="card-body">
                        <vue-element-loading :active="isLoading_obtener" />

                <v-client-table :columns="columns_estados" :data="list_estados" :options="options_estados" ref="myTable">
                    <template slot="id" slot-scope="props">
                        <div class="btn-group" role="group">
                            <div class="btn-group dropup" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i> Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <button type="button" @click="abrirModal(false,props.row)" class="dropdown-item">
                                        <i class="fas fa-edit"></i>Actualizar estado.
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
                                <label class="col-md-3 form-control-label" for="Nombre">Nombre</label>
                                <div class="col-md-9">
                                    <input type="text" v-validate="'required'" name="Nombre" v-model="edoCompra.nombre" class="form-control" placeholder="Nombre" autocomplete="off" id="Nombre">
                                    <span class="text-danger">{{ errors.first('Nombre') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- </form> -->
                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isLoading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="cerrarModal()"><i class="fas fa-window-close"></i>Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-secondary" @click="guardaredoCompra(true)"><i class="fas fa-save"></i>Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-secondary" @click="guardaredoCompra(false)"><i class="fas fa-save"></i>Actualizar</button>
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
            url: '/estadocompra',
            isLoading_obtener:false,
            edoCompra:
            {
                id: 0,
                nombre: ''
            },

            modal: 0,
            tituloModal: '',
            tipoAccion: 0,
            isLoading: false,
            columns_estados: ['id', 'nombre'],
            list_estados: [],
            options_estados:
            {
                headings:
                {
                    'id': 'Acciones',
                    'nombre': 'Nombre',

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
         * Obtener los estados de compra
         */
        ObtenerEstados()
        {
            this.isLoading_obtener = true;
            axios.get(this.url).then(res =>
            {
                this.isLoading_obtener = false;
                if (res.data.status)
                {
                    this.list_estados = res.data.estados;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Guardar estado de compra
         */
        guardaredoCompra(nuevo)
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
                            'id': this.edoCompra.id,
                            'nombre': this.edoCompra.nombre
                        }
                    }).then(res =>
                    {
                        this.isLoading = false;
                        if (res.data.status)
                        {
                            this.cerrarModal();
                            this.ObtenerEstados();
                            if (nuevo)
                            {
                                toastr.success('Estado Agregado Correctamente');
                            }
                            else
                            {
                                toastr.success('Estado Actualizado Correctamente');
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
            this.edoCompra = {};
        },

        /**
         * Abrir modal para registro/actualización
         */
        abrirModal(nuevo = true, data = [])
        {
            this.modal = 1;
            if (nuevo)
            {
                this.tipoAccion = 1;
                this.tituloModal = 'Registrar Estado';
            }
            else
            {
                this.tipoAccion = 2;
                this.tituloModal = 'Actualizar Estado';
                this.edoCompra.id = data['id'];
                this.edoCompra.nombre = data['nombre'];
            }
        }
    },

    mounted()
    {
        this.ObtenerEstados();
    }
}
</script>
