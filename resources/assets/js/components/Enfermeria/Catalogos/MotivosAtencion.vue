<template>
<main class="main">
    <!-- Listado MotivoAtencion -->
    <div class="card" v-show="tipoCardMotivoAtencion==1">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Motivos De Atención
            <template v-if="PermisosCRUD.Create">
                <button type="button" @click="AbrirModalMotivoAtencion(true)" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                </button>
            </template>
        </div>
        <div class="card-body">
            <vue-element-loading :active="isObtenermotivoatencion_loading" />
            <v-client-table :columns="columns_motivoatencion" :data="list_motivoatencion" :options="options_motivoatencion">
                <template slot="id" slot-scope="props">
                    <div class="btn-group" role="group">
                        <div class="btn-group dropup" role="group">
                            <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-grip-horizontal"></i> Acciones
                            </button>
                            <div class="dropdown-menu">
                                <button type="button" class="dropdown-item" @click="AbrirModalMotivoAtencion(false,props.row)">
                                    <i class="fas fa-edit"></i> Actualizar
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                <template slot="tipo" slot-scope="props">
                    <p v-if="props.row.tipo==='SM'">Serivios Médicos</p>
                    <p v-else-if="props.row.tipo==='EG'">Enfermedad General</p>
                    <p v-else>Accidente de Trabajo</p>
                </template>
            </v-client-table>
        </div>
    </div>

    <!--Inicio del modal MotivoAtencion-->
    <div v-if="ver_modal_motivoatencion" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_motivoatencion}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_motivoatencion"></h4>
                        <button type="button" class="close" @click="CerrarModalMotivoAtencion()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardarmotivoatencion_loading" />
                        <div>
                            <!-- Formulario -->

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Tipo</label>
                                <div class="col-md-5">
                                    <select class="form-control" v-model="motivoatencion.tipo" v-validate="'required'">
                                        <option value="EG">Enfermedad General</option>
                                        <option value="AT">Accidente de Trabajo</option>
                                        <option value="SM">Servicios Médicos</option>
                                    </select>
                                    <span class="text-danger">{{ errors.first('Tipo') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Nombre</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" v-validate="'required'" v-model="motivoatencion.nombre" data-vv-name="Nombre">
                                    <span class="text-danger">{{ errors.first('Nombre') }}</span>
                                </div>
                            </div>

                            <!-- Formulario -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardarmotivoatencion_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalMotivoAtencion()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                            <button type="button" v-if="tipoAccion_motivoatencion==1" class="btn btn-secondary" @click="RegistrarMotivoAtencion(true)">
                                <i class="fas fa-save"></i>&nbsp;Guardar
                            </button>
                            <button type="button" v-if="tipoAccion_motivoatencion==2" class="btn btn-secondary" @click="RegistrarMotivoAtencion(false)">
                                <i class="fas fa-save"></i>&nbsp;Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal MotivoAtencion-->
</main>
</template>

<script>
var config = require('../../Herramientas/config-vuetables-client').call(this);
import Utilerias from '../../Herramientas/utilerias.js';

export default
{
    data()
    {
        return {
            //// MotivoAtencion
            url_motivoatencion: "enfermeria/catalogos/motivoatencion",
            tipoAccion_motivoatencion: 1,
            tipoCardMotivoAtencion: 1,
            PermisosCRUD:
            {},
            ver_modal_motivoatencion: false,
            tituloModal_motivoatencion: "",
            motivoatencion_id: 0,
            isGuardarmotivoatencion_loading: false,
            isObtenermotivoatencion_loading: false,
            columns_motivoatencion: [
                "id",
                "tipo",
                "nombre"
            ],
            list_motivoatencion: [],
            motivoatencion:
            {
                tipo:"EG",
            },
            options_motivoatencion:
            {
                headings:
                {
                    id: "Acciones",
                    nombre: "Nombre",
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
    methods:
    {
        // Metodos
        /**
         * Obtener todos los registros
         */
        ObtenerMotivoAtencion()
        {
            this.isObtenermotivoatencion_loading = true;
            axios.get(this.url_motivoatencion + "/obtener").then(res =>
            {
                this.isObtenermotivoatencion_loading = false;
                if (res.data.status)
                {
                    this.list_motivoatencion = res.data.motivos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Abrir modal MotivoAtencion
         */
        AbrirModalMotivoAtencion(nuevo, data = {})
        {
            this.ver_modal_motivoatencion = true;
            if (nuevo)
            {
                this.tituloModal_motivoatencion = "Registrar Motivo de Atención";
                this.tipoAccion_motivoatencion = 1;
            }
            else
            {
                this.tituloModal_motivoatencion = "Actualizar Motivo de Atención";
                this.tipoAccion_motivoatencion = 2;
                this.motivoatencion = {
                    ...data
                };
            }
        },

        /**
         * Registrar MotivoAtencion
         */
        RegistrarMotivoAtencion(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.motivoatencion.id);
                data.append("tipo", this.motivoatencion.tipo);
                data.append("nombre", this.motivoatencion.nombre);

                this.isGuardarmotivoatencion_loading = true;
                axios.post(this.url_motivoatencion + "/guardar", data).then(res =>
                {
                    this.isGuardarmotivoatencion_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.ObtenerMotivoAtencion();
                        this.CerrarModalMotivoAtencion();
                    }
                    else
                    {
                        toastr.error(res.data.mensaje);
                    }
                })

            })
        },

        /**
         * Cerrar modal
         */
        CerrarModalMotivoAtencion()
        {
            this.ver_modal_motivoatencion = false;
            this.motivoatencion = {
                tipo:"EG",
            };
        },

    },
    mounted()
    {
        this.ObtenerMotivoAtencion();
        this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
    }
}
</script>
