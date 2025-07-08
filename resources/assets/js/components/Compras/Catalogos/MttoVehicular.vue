<template>
<main class="main">
    <div class="container-fluid">
        <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Servicios vehiculares
                <button type="button" @click="abrirModal()" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus"></i>Nuevo
                </button>
            </div>
            <div class="card-body">
                <v-client-table :columns="columns_servicios" :data="list_servicios" :options="options_servicios" ref="myTable">
                    <template slot="id" slot-scope="props">
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group dropup" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i> Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <button type="button" @click="abrirModal(false,props.row)" class="dropdown-item">
                                        <i class="fas fa-edit"></i>Actualizar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </v-client-table>
            </div>
        </div>
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
                                <label class="col-md-3 form-control-label">Descripción</label>
                                <div class="col-md-6">
                                    <textarea v-validate="'required'" v-model="catalogo.descripcion" class="form-control" data-vv-name="descripcion" placeholder="Descripción" autocomplete="off" rows="8" cols="80">
                                    </textarea>
                                    <span class="text-danger">{{ errors.first('descripcion') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Código</label>
                                <div class="col-md-6">
                                    <input type="text" v-validate="'required'" v-model="catalogo.codigo" class="form-control" data-vv-name="codigo" placeholder="Codígo" autocomplete="off">
                                    <span class="text-danger">{{ errors.first('codigo') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Marca</label>
                                <div class="col-md-6">
                                    <input type="text" v-validate="'required'" v-model="catalogo.marca" class="form-control" data-vv-name="marca" placeholder="Marca" autocomplete="off">
                                    <span class="text-danger">{{ errors.first('marca') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Comentario</label>
                                <div class="col-md-6">
                                    <input type="text" v-validate="'required'" v-model="catalogo.comentario" class="form-control" data-vv-name="comentario" placeholder="Comentario" autocomplete="off">
                                    <span class="text-danger">{{ errors.first('comentario') }}</span>
                                </div>
                            </div>
                        </div>
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
            url: '/catmantenimientovehiculos',
            desabilitado: 0,
            catalogo:
            {
                id: 0,
                descripcion: '',
                codigo: '',
                marca: '',
                comentario: '',
            },
            centro_costo: [],
            modal: 0,
            tituloModal: '',
            tipoAccion: 0,
            isLoading: false,
            isLoading_servicios: false,
            columns_servicios: ['id', 'descripcion', 'codigo', 'marca', 'comentario'],
            list_servicios: [],
            options_servicios:
            {
                headings:
                {
                    'id': 'Acción',
                    'descripcion': 'Descripción',
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
         * Obtener todos los servicios registrados
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
         * Registrar o actualizar servicio 
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
                            'id': this.catalogo.id,
                            'descripcion': this.catalogo.descripcion,
                            'codigo': this.catalogo.codigo,
                            'marca': this.catalogo.marca,
                            'comentario': this.catalogo.comentario,
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
         * Cerrar modal de registro/actualización
         */
        cerrarModal()
        {
            this.modal = 0;
            this.tituloModal = '';
            Utilerias.resetObject(this.catalogo);
        },

        /**
         * Abrir modal para registro/actualización del servicio 
         */
        abrirModal(nuevo = true, data = [])
        {
            this.modal = 1;
            if (nuevo)
            {
                this.tituloModal = 'Registrar Catalogo';
                this.tipoAccion = 1;
            }
            else
            {
                this.tituloModal = 'Actualizar Catalogo';
                this.catalogo.id = data['id'];
                this.tipoAccion = 2;
                this.catalogo.descripcion = data['descripcion'];
                this.catalogo.codigo = data['codigo'];
                this.catalogo.marca = data['marca'];
                this.catalogo.comentario = data['comentario'];
            }
        }
    },

    mounted()
    {
        this.ObtenerServicios();
    }
}
</script>
