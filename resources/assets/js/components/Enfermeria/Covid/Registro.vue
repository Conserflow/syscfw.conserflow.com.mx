<template>
<main class="main">
    <!-- Listado RegistroCovid -->
    <div class="card" v-show="tipoCardRegistroCovid==1">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Registros Covid
            <button v-if="PermisosCRUD.Create" type="button" @click="AbrirModalRegistroCovid(true)" class="btn btn-dark float-sm-right">
                <i class="fas fa-plus"></i>&nbsp;Nuevo
            </button>
            <button v-if="PermisosCRUD.Download" type="button" @click="DescargaReporte()" class="btn btn-dark float-sm-right mr-1">
                <i class="fas fa-download mr-1"></i> Descargar
            </button>
        </div>
        <div class="card-body">
            <vue-element-loading :active="isObtenerregistrocovid_loading" />
            <v-client-table :columns="columns_registrocovid" :data="list_registrocovid" :options="options_registrocovid">
                <template slot="id" slot-scope="props">
                    <div class="btn-group" role="group">
                        <div class="btn-group dropup" role="group">
                            <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-grip-horizontal"></i> Acciones
                            </button>
                            <div class="dropdown-menu">
                                <button type="button" class="dropdown-item" @click="AbrirModalRegistroCovid(false,props.row)">
                                    <i class="fas fa-edit"></i> Actualizar
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <template slot="inicio_sintomas" slot-scope="props">
                    <p v-if="props.row.inicio_sintomas==null">-</p>
                    <p v-else>{{props.row.inicio_sintomas}}</p>
                </template>

                <template slot="fecha_deteccion" slot-scope="props">
                    <p v-if="props.row.fecha_deteccion==null">-</p>
                    <p v-else>{{props.row.fecha_deteccion}}</p>
                </template>

                <template slot="inicio_incapacidad" slot-scope="props">
                    <p v-if="props.row.inicio_incapacidad==null">-</p>
                    <p v-else>{{props.row.inicio_incapacidad}}</p>
                </template>

                <template slot="termino_incapacidad" slot-scope="props">
                    <p v-if="props.row.termino_incapacidad==null">-</p>
                    <p v-else>{{props.row.termino_incapacidad}}</p>
                </template>
            </v-client-table>
        </div>
    </div>

    <!--Inicio del modal RegistroCovid-->
    <div v-if="ver_modal_registrocovid" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_registrocovid}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_registrocovid"></h4>
                        <button type="button" class="close" @click="CerrarModalRegistroCovid()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardarregistrocovid_loading" />
                        <div>
                            <!-- Formulario -->
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Empleado</label>
                                <div class="col-md-6">
                                    <v-select label="nombre" :options="list_empleados" v-validate="'required'" v-model="registrocovid.empleado" data-vv-name="Empleado">
                                    </v-select>
                                    <span class="text-danger">{{ errors.first("Empleado") }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Diagnóstico</label>
                                <div class="col-md-9">
                                    <textarea rows="3" v-validate="'required'" v-model="registrocovid.diagnostico" class="form-control" data-vv-name="Diagnostico"></textarea>
                                    <span class="text-danger">{{ errors.first("Diagnostico") }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Inicio sintomas</label>
                                <div class="col-md-3">
                                    <input type="date" v-model="registrocovid.inicio_sintomas" class="form-control" data-vv-name="Inicio sintomas" />
                                    <span class="text-danger">{{ errors.first("Inicio sintomas") }}</span>
                                </div>

                                <label class="col-md-3 form-control-label">Fecha de detección</label>
                                <div class="col-md-3">
                                    <input type="date" v-model="registrocovid.fecha_deteccion" class="form-control" data-vv-name="Fecha de detección" />
                                    <span class="text-danger">{{ errors.first("Fecha de detección") }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Inicio incapacidad</label>
                                <div class="col-md-3">
                                    <input type="date" v-model="registrocovid.inicio_incapacidad" class="form-control" data-vv-name="Inicio incapacidad" />
                                    <span class="text-danger">{{ errors.first("Inicio incapacidad") }}</span>
                                </div>
                                <label class="col-md-3 form-control-label">Días incapacidad</label>
                                <div class="col-md-2">
                                    <input type="text" maxlength="150" v-validate="'required'" minlength="3" v-model="registrocovid.dias_incapacidad" class="form-control" data-vv-name="Días incapacidad" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first("Días incapacidad") }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Fin incapacidad</label>
                                <div class="col-md-3">
                                    <input type="date" v-model="registrocovid.termino_incapacidad" class="form-control" data-vv-name="Fin incapacidad" />
                                    <span class="text-danger">{{ errors.first("Fin incapacidad") }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Prueba</label>
                                <div class="col-md-9">
                                    <input type="text" maxlength="75" minlength="3" v-validate="'required'" v-model="registrocovid.prueba" class="form-control" data-vv-name="Prueba" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first("Prueba") }}</span>
                                </div>
                            </div>

                            <!-- Formulario -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardarregistrocovid_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalRegistroCovid()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                            <button type="button" v-if="tipoAccion_registrocovid==1" class="btn btn-secondary" @click="RegistrarRegistroCovid(true)">
                                <i class="fas fa-save"></i>&nbsp;Guardar
                            </button>
                            <button type="button" v-if="tipoAccion_registrocovid==2" class="btn btn-secondary" @click="RegistrarRegistroCovid(false)">
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
    <!--Fin del modal RegistroCovid-->

</main>
</template>

<script>
var config = require("../../Herramientas/config-vuetables-client").call(this);
import Utilerias from "../../Herramientas/utilerias.js";
export default
{
    data()
    {
        return {
            //// RegistroCovid
            url_registrocovid: "enfermeria/registrocovid",
            tipoAccion_registrocovid: 1,
            tipoCardRegistroCovid: 1,
            PermisosCRUD:
            {},
            ver_modal_registrocovid: false,
            tituloModal_registrocovid: "",
            registrocovid_id: 0,
            isGuardarregistrocovid_loading: false,
            isObtenerregistrocovid_loading: false,
            columns_registrocovid: [
                "id",
                "empleado",
                "diagnostico",
                "inicio_sintomas",
                "fecha_deteccion",
                "inicio_incapacidad",
                "dias_incapacidad",
                "termino_incapacidad",
                "prueba"
            ],
            list_registrocovid: [],
            list_empleados: [],
            registrocovid:
            {},
            options_registrocovid:
            {
                headings:
                {
                    id: "Acciones",
                    empleado: "Empleado",
                    diagnostico: "Diagnostico",
                    inicio_sintomas: "Inicio sintomas",
                    fecha_deteccion: "Fecha de detección",
                    inicio_incapacidad: "Inicio incapacidad",
                    dias_incapacidad: "Inicio incapacidad",
                    termino_incapacidad: "Fin incapacidad",
                    prueba: "Prueba",

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
        ObtenerRegistroCovid()
        {
            this.isObtenerregistrocovid_loading = true;
            axios.get(this.url_registrocovid + "/obtener").then(res =>
            {
                this.isObtenerregistrocovid_loading = false;
                if (res.data.status)
                {
                    this.list_registrocovid = res.data.registrocovid;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Abrir modal RegistroCovid
         */
        AbrirModalRegistroCovid(nuevo, data = {})
        {
            this.ObtenerEmpleados();
            this.ver_modal_registrocovid = true;
            if (nuevo)
            {
                this.tituloModal_registrocovid = "Registrar Registros Covid";
                this.tipoAccion_registrocovid = 1;
            }
            else
            {
                this.tituloModal_registrocovid = "Actualizar Registros Covid";
                this.tipoAccion_registrocovid = 2;
                this.registrocovid = {
                    ...data
                };
                this.registrocovid.empleado = {
                    id: data.empleado_id,
                    puesto_id: data.puesto_id,
                    nombre: data.empleado,
                    puesto: data.puesto
                };
            }
        },

        /**
         * Obtener empleados
         */
        ObtenerEmpleados()
        {
            axios.get("generales/empleadospuestosactivos").then(res =>
            {
                if (res.data.status)
                {
                    this.list_empleados = res.data.empleados;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });

        },

        /**
         * Registrar RegistroCovid
         */
        RegistrarRegistroCovid(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;

                this.isGuardarregistrocovid_loading = true;
                axios.post(this.url_registrocovid + "/guardar",
                {
                    "id": this.registrocovid.id,
                    "empleado_id": this.registrocovid.empleado.id,
                    "puesto_id": this.registrocovid.empleado.puesto_id,
                    "diagnostico": this.registrocovid.diagnostico,
                    "inicio_sintomas": this.registrocovid.inicio_sintomas,
                    "fecha_deteccion": this.registrocovid.fecha_deteccion,
                    "inicio_incapacidad": this.registrocovid.inicio_incapacidad,
                    "dias_incapacidad": this.registrocovid.dias_incapacidad,
                    "termino_incapacidad": this.registrocovid.termino_incapacidad,
                    "prueba": this.registrocovid.prueba,
                }).then(res =>
                {
                    this.isGuardarregistrocovid_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.ObtenerRegistroCovid();
                        this.CerrarModalRegistroCovid();
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
        CerrarModalRegistroCovid()
        {
            this.ver_modal_registrocovid = false;
            this.registrocovid = {};
        },

        /**
         * Generar reporte
         */
        DescargaReporte()
        {
            window.open(this.url_registrocovid + "/descargar");
        }

    },
    mounted()
    {
        this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
        this.ObtenerRegistroCovid();
    }
}
</script>
