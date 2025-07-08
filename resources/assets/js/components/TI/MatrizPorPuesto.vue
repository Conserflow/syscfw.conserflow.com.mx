<template>
<main class="main">
    <!-- Listado MatrizRequisitos -->
    <div class="card" v-show="tipoCardMatrizRequisitos==1">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> MATRIZ DE REQUISITOS POR PUESTO
            <button v-if="PermisosCRUD.Create" type="button" @click="AbrirModalMatrizRequisitos(true)" class="btn btn-dark float-sm-right">
                <i class="fas fa-plus mr-1"></i>Nuevo
            </button>

            <button v-if="PermisosCRUD.Download" type="button" @click="Descargar" class="btn btn-dark mr-1 float-sm-right">
                <i class="fas fa-file-pdf mr-1"></i>Descargar
            </button>
        </div>
        <div class="card-body">
            <vue-element-loading :active="isObtenermatrizrequisitos_loading" />
            <v-client-table :columns="columns_matrizrequisitos" :data="list_matrizrequisitos" :options="options_matrizrequisitos">
                <template slot="id" slot-scope="props">
                    <div class="btn-group" role="group">
                        <div class="btn-group dropup" role="group">
                            <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-grip-horizontal"></i> Acciones
                            </button>
                            <div class="dropdown-menu">
                                <button type="button" class="dropdown-item" @click="AbrirModalMatrizRequisitos(false,props.row)">
                                    <i class="fas fa-edit"></i> Actualizar
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>
        </div>
    </div>

    <!--Inicio del modal MatrizRequisitos-->
    <div v-if="ver_modal_matrizrequisitos" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_matrizrequisitos}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_matrizrequisitos"></h4>
                        <button type="button" class="close" @click="CerrarModalMatrizRequisitos()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardarmatrizrequisitos_loading" />
                        <div>
                            <!-- Formulario -->

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Puesto</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" v-validate="'required'" v-model="matrizrequisitos.puesto" data-vv-name="Puesto">
                                    <span class="text-danger">{{ errors.first('Puesto') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Jefatura del puesto</label>
                                <div class="col-md-9">
                                    <v-select label="nombre" :options="list_puestos" v-validate="'required'" v-model="matrizrequisitos.puesto_jefe" data-vv-name="Jefatura del puesto">
                                    </v-select>
                                    <span class="text-danger">{{ errors.first('Jefatura del puesto') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Software</label>
                                <div class="col-md-9">
                                    <textarea maxlength="145" minlength="5" v-validate="'required'" v-model="matrizrequisitos.software" class="form-control" data-vv-name="Software"></textarea>
                                    <span class="text-danger">{{ errors.first('Software') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Características del equipo</label>
                                <div class="col-md-9">
                                    <textarea maxlength="145" minlength="5" v-validate="'required'" v-model="matrizrequisitos.equipo" class="form-control" data-vv-name="Características del equipo"></textarea>
                                    <span class="text-danger">{{ errors.first('Características del equipo') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Accesorios</label>
                                <div class="col-md-9">
                                    <textarea type="text" maxlength="45" minlength="3" v-validate="'required'" v-model="matrizrequisitos.accesorios" class="form-control" data-vv-name="Accesorios"></textarea>
                                    <span class="text-danger">{{ errors.first('Accesorios') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Impresora</label>
                                <div class="col-md-9">
                                    <input type="text" maxlength="50" minlength="3" v-validate="'required'" v-model="matrizrequisitos.impresora" class="form-control" data-vv-name="Impresora" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Impresora') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Red</label>
                                <div class="col-md-9">
                                    <select v-validate="'required'" v-model="matrizrequisitos.red" data-vv-name="Red" class="form-control">
                                        <option value="SI">Sí</option>
                                        <option value="NO">No</option>
                                    </select>
                                    <span class="text-danger">{{ errors.first('Red') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Otro</label>
                                <div class="col-md-9">
                                    <textarea type="text" maxlength="45" minlength="3" v-validate="'required'" v-model="matrizrequisitos.otro" class="form-control" data-vv-name="Otro"></textarea>
                                    <span class="text-danger">{{ errors.first('Otro') }}</span>
                                </div>
                            </div>

                            <!-- Formulario -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardarmatrizrequisitos_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalMatrizRequisitos()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                            <button type="button" v-if="tipoAccion_matrizrequisitos==1" class="btn btn-secondary" @click="RegistrarMatrizRequisitos(true)">
                                <i class="fas fa-save"></i>&nbsp;Guardar
                            </button>
                            <button type="button" v-if="tipoAccion_matrizrequisitos==2" class="btn btn-secondary" @click="RegistrarMatrizRequisitos(false)">
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
    <!--Fin del modal MatrizRequisitos-->

</main>
</template>

<script>
var config = require('../Herramientas/config-vuetables-client').call(this);
import Utilerias from '../Herramientas/utilerias.js';
export default
{
    data()
    {
        return {
            //// MatrizRequisitos
            url_matrizrequisitos: "ti/matrizrequisitos",
            tipoAccion_matrizrequisitos: 1,
            tipoCardMatrizRequisitos: 1,
            list_puestos: [],
            PermisosCRUD:
            {},
            ver_modal_matrizrequisitos: false,
            tituloModal_matrizrequisitos: "",
            matrizrequisitos_id: 0,
            isGuardarmatrizrequisitos_loading: false,
            isObtenermatrizrequisitos_loading: false,
            columns_matrizrequisitos: [
                "id",
                "puesto",
                "puesto_jefe",
                "software",
                "equipo",
                "accesorios",
                "impresora",
                "red",
                "otro"
            ],
            list_matrizrequisitos: [],
            matrizrequisitos:
            {},
            options_matrizrequisitos:
            {
                headings:
                {
                    id: "Acciones",
                    puesto_id: "Puesto",
                    puesto_jefe_id: "Jefatura del puesto",
                    software: "Software",
                    equipo: "Características del equipo",
                    accesorios: "Accesorios",
                    impresora: "Impresora",
                    red: "Red",
                    otro: "Otro",
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
        ObtenerMatrizRequisitos()
        {
            this.isObtenermatrizrequisitos_loading = true;
            axios.get(this.url_matrizrequisitos + "/obtener").then(res =>
            {
                this.isObtenermatrizrequisitos_loading = false;
                if (res.data.status)
                {
                    this.list_matrizrequisitos = res.data.matrizrequisitos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Abrir modal MatrizRequisitos
         */
        AbrirModalMatrizRequisitos(nuevo, data = {})
        {
            this.ObtenerPuestos();
            this.ver_modal_matrizrequisitos = true;
            if (nuevo)
            {
                this.tituloModal_matrizrequisitos = "Registrar Matriz de Requisitos";
                this.tipoAccion_matrizrequisitos = 1;
            }
            else
            {
                this.tituloModal_matrizrequisitos = "Actualizar Matriz de Requisitos";
                this.tipoAccion_matrizrequisitos = 2;
                this.matrizrequisitos = {
                    ...data
                };
                this.matrizrequisitos.puesto_jefe = {
                    id: data.puesto_jefe_id,
                    nombre: data.puesto_jefe
                }
            }
        },

        /**
         * Obtener todos los puestos
         */
        ObtenerPuestos()
        {
            axios.get("generales/puestos").then(res =>
            {
                if (res.data.status)
                {
                    this.list_puestos = res.data.puestos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Registrar MatrizRequisitos
         */
        RegistrarMatrizRequisitos(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.matrizrequisitos.id);
                data.append("puesto", this.matrizrequisitos.puesto);
                data.append("puesto_jefe_id", this.matrizrequisitos.puesto_jefe.id);
                data.append("software", this.matrizrequisitos.software);
                data.append("equipo", this.matrizrequisitos.equipo);
                data.append("accesorios", this.matrizrequisitos.accesorios);
                data.append("impresora", this.matrizrequisitos.impresora);
                data.append("red", this.matrizrequisitos.red);
                data.append("otro", this.matrizrequisitos.otro);

                this.isGuardarmatrizrequisitos_loading = true;
                axios.post(this.url_matrizrequisitos + "/guardar", data).then(res =>
                {
                    this.isGuardarmatrizrequisitos_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.ObtenerMatrizRequisitos();
                        this.CerrarModalMatrizRequisitos();
                    }
                    else
                    {
                        toastr.error(res.data.mensaje);
                    }
                })

            })
        },

        Descargar()
        {
            window.open(this.url_matrizrequisitos + "/descargar");
        },
        /**
         * Cerrar modal
         */
        CerrarModalMatrizRequisitos()
        {
            this.ver_modal_matrizrequisitos = false;
            this.matrizrequisitos = {};
        },

    },
    mounted()
    {
        this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
        this.ObtenerMatrizRequisitos();
    }
}
</script>
