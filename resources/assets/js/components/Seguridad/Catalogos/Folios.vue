<template>
<main class="main">
    <!-- Listado Folios_Permisos -->
    <div class="card" v-show="tipoCardFolios_Permisos==1">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> FOLIO DE PROYECTOS
            <template v-if="PermisosCRUD.Create">
                <button type="button" @click="AbrirModalFolios_Permisos(true)" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus mr-1"></i>Nuevo
                </button>
            </template>
        </div>
        <div class="card-body">
            <vue-element-loading :active="isObtenerfolios_permisos_loading" />
            <v-client-table :columns="columns_folios_permisos" :data="list_folios_permisos" :options="options_folios_permisos">
            </v-client-table>
        </div>
    </div>

    <!--Inicio del modal Folios_Permisos-->
    <div v-if="ver_modal_folios_permisos" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_folios_permisos}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_folios_permisos"></h4>
                        <button type="button" class="close" @click="CerrarModalFolios_Permisos()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardarfolios_permisos_loading" />
                        <div>
                            <!-- Formulario -->
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Proyecto</label>
                                <div class="col-md-9">
                                    <v-select label="nombre_corto" :options="list_proyectos" v-validate="'required'" v-model="folios_permisos.proyecto" data-vv-name="Proyecto">
                                    </v-select>
                                    <span class="text-danger">{{ errors.first('Proyecto') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Nombre</label>
                                <div class="col-md-4">
                                    <input type="text" maxlength="5" minlength="1" v-validate="'required'" v-model="folios_permisos.nombre" class="form-control" data-vv-name="Nombre" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Nombre') }}</span>
                                </div>
                            </div>

                            <!-- Formulario -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardarfolios_permisos_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalFolios_Permisos()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                            <button type="button" v-if="tipoAccion_folios_permisos==1" class="btn btn-secondary" @click="RegistrarFolios_Permisos(true)">
                                <i class="fas fa-save"></i>&nbsp;Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal Folios_Permisos-->

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
            //// Folios_Permisos
            url_folios_permisos: "seguridad/folios_permisos",
            tipoAccion_folios_permisos: 1,
            tipoCardFolios_Permisos: 1,
            PermisosCRUD:
            {},
            ver_modal_folios_permisos: false,
            tituloModal_folios_permisos: "",
            folios_permisos_id: 0,
            isGuardarfolios_permisos_loading: false,
            isObtenerfolios_permisos_loading: false,
            columns_folios_permisos: [
                "proyecto",
                "nombre"
            ],
            list_proyectos: [],
            list_folios_permisos: [],
            folios_permisos:
            {},
            options_folios_permisos:
            {
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
        ObtenerFolios_Permisos()
        {
            this.isObtenerfolios_permisos_loading = true;
            axios.get(this.url_folios_permisos + "/obtener").then(res =>
            {
                this.isObtenerfolios_permisos_loading = false;
                if (res.data.status)
                {
                    this.list_folios_permisos = res.data.folios_proyectos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Obtener todos los proyectos
         */
        ObtenerProyectos()
        {
            axios.get("generales/proyectos/1").then(res =>
            {
                console.error(res.data.proyectos);

                if (res.data.status)
                {
                    this.list_proyectos = res.data.proyectos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Abrir modal Folios_Permisos
         */
        AbrirModalFolios_Permisos(nuevo)
        {
            this.ver_modal_folios_permisos = true;
            if (nuevo)
            {
                this.tituloModal_folios_permisos = "Registrar Folio de Proyectos";
                this.tipoAccion_folios_permisos = 1;
            }
        },

        /**
         * Registrar Folios_Permisos
         */
        RegistrarFolios_Permisos(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.folios_permisos.id);
                data.append("proyecto_id", this.folios_permisos.proyecto.id);
                data.append("nombre", this.folios_permisos.nombre);

                this.isGuardarfolios_permisos_loading = true;
                axios.post(this.url_folios_permisos + "/guardar", data).then(res =>
                {
                    this.isGuardarfolios_permisos_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.ObtenerFolios_Permisos();
                        this.CerrarModalFolios_Permisos();
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
        CerrarModalFolios_Permisos()
        {
            this.ver_modal_folios_permisos = false;
            this.Folios_Permisos = {};
        },

    },
    mounted()
    {
        this.ObtenerFolios_Permisos();
        this.ObtenerProyectos();
        this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
    }
}
</script>
