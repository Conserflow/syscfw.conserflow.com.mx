<template>
<main class="main">
    <!-- Listado AtencionMedica -->
    <div class="card" v-show="tipoCardAtencionMedica==1">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> ATENCIÓN MÉDICA
            <template v-if="PermisosCRUD.Create">
                <button type="button" @click="AbrirModalAtencionMedica(true)" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                </button>
            </template>
        </div>
        <div class="card-body">
            <vue-element-loading :active="isObteneratencionmedica_loading" />
            <v-client-table :columns="columns_atencionmedica" :data="list_atencionmedica" :options="options_atencionmedica">
                <template slot="tipo" slot-scope="props">
                    <button v-if="props.row.tipo==='SM'" class="btn btn-outline-success">Serivios Médicos</button>
                    <button v-else-if="props.row.tipo==='EG'" class="btn btn-outline-warning">Enfermedad General</button>
                    <button v-else class="btn btn-outline-danger">Accidente de Trabajo</button>
                </template>
            </v-client-table>
        </div>
    </div>

    <!--Inicio del modal AtencionMedica-->
    <div v-if="ver_modal_atencionmedica" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_atencionmedica}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_atencionmedica"></h4>
                        <button type="button" class="close" @click="CerrarModalAtencionMedica()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardaratencionmedica_loading" />
                        <div>
                            <!-- Formulario -->

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Fecha</label>
                                <div class="col-md-3">
                                    <input type="date" class="form-control" v-validate="'required'" v-model="atencionmedica.fecha" data-vv-name="Fecha" />
                                    <span class="text-danger">{{ errors.first('Fecha') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Empleado</label>
                                <div class="col-md-6">
                                    <v-select label="nombre" :options="list_empleados" v-validate="'required'" v-model="atencionmedica.empleado" data-vv-name="Empleado">
                                    </v-select>
                                    <span class="text-danger">{{ errors.first('Empleado') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Empresa</label>
                                <div class="col-md-4">
                                    <input v-if="atencionmedica.empleado!=null" type="text" class="form-control" disabled v-model="atencionmedica.empleado.empresa">
                                    <input v-else type="text" class="form-control" disabled>
                                </div>
                                <label class="col-md-1 form-control-label">Puesto</label>
                                <div class="col-md-3">
                                    <input v-if="atencionmedica.empleado!=null" type="text" class="form-control" disabled v-model="atencionmedica.empleado.puesto">
                                    <input v-else type="text" class="form-control" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Motivo</label>
                                <div class="col-md-6">
                                    <v-select label="nombre" :options="list_motivos" v-validate="'required'" v-model="atencionmedica.motivo" data-vv-name="Motivo">
                                    </v-select>
                                    <span class="text-danger">{{ errors.first('Motivo') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Tipo</label>
                                <div class="col-md-4">
                                    <template v-if="atencionmedica.motivo!=null">
                                        <select class="form-control" v-model="atencionmedica.motivo.tipo" disabled>
                                            <option value="EG">Enfermedad General</option>
                                            <option value="AT">Accidente de Trabajo</option>
                                            <option value="SM">Serivios Médicos</option>
                                        </select>
                                    </template>
                                    <template v-else>
                                        <input type="text" class="form-control" disabled>
                                    </template>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Medicamentos usados</label>
                                <div class="col-md-6">
                                    <textarea class="form-control" rows="4" v-validate="'required'" v-model="atencionmedica.medicamentos" data-vv-name="Medicamentos">
                                    </textarea>
                                    <span class="text-danger">{{ errors.first('Medicamentos') }}</span>
                                </div>
                            </div>

                            <!-- Formulario -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardaratencionmedica_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalAtencionMedica()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                            <button type="button" v-if="tipoAccion_atencionmedica==1" class="btn btn-secondary" @click="RegistrarAtencionMedica(true)">
                                <i class="fas fa-save"></i>&nbsp;Guardar
                            </button>
                            <button type="button" v-if="tipoAccion_atencionmedica==2" class="btn btn-secondary" @click="RegistrarAtencionMedica(false)">
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
    <!--Fin del modal AtencionMedica-->

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
            //// AtencionMedica
            url_atencionmedica: "enfermeria/atencionmedica",
            tipoAccion_atencionmedica: 1,
            tipoCardAtencionMedica: 1,
            PermisosCRUD:
            {},
            ver_modal_atencionmedica: false,
            tituloModal_atencionmedica: "",
            atencionmedica_id: 0,
            isGuardaratencionmedica_loading: false,
            isObteneratencionmedica_loading: false,
            columns_atencionmedica: [
                "fecha",
                "empleado",
                "tipo",
                "motivo",
                "medicamentos"
            ],
            list_atencionmedica: [],
            list_empleados: [],
            list_motivos: [],
            atencionmedica:
            {
                empleado:
                {
                    emppresa:"",
                    puesto:"",
                }
            },
            options_atencionmedica:
            {
                headings:
                {
                    id: "Acciones",
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
         * Cargar los empleados y motivos de atención médica
         */
        CargarDatos()
        {
            // empleado
            axios.get("generales/empleadosgenerales").then(res =>
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

            // motivos
            axios.get("enfermeria/catalogos/motivoatencion/obtener").then(res =>
            {
                if (res.data.status)
                {
                    this.list_motivos = res.data.motivos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });

        },

        /**
         * Obtener todos los registros
         */
        ObtenerAtencionMedica()
        {
            this.isObteneratencionmedica_loading = true;
            axios.get(this.url_atencionmedica + "/obtener").then(res =>
            {
                this.isObteneratencionmedica_loading = false;
                if (res.data.status)
                {
                    this.list_atencionmedica = res.data.atenciones;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Abrir modal AtencionMedica
         */
        AbrirModalAtencionMedica(nuevo, data = {})
        {
            this.CargarDatos();
            this.ver_modal_atencionmedica = true;
            if (nuevo)
            {
                this.tituloModal_atencionmedica = "Registrar Atención Médica";
                this.tipoAccion_atencionmedica = 1;
            }
            else
            {
                this.tituloModal_atencionmedica = "Actualizar Atención Médica";
                this.tipoAccion_atencionmedica = 2;
                this.atencionmedica = {
                    ...data,
                    empleado:
                    {
                        id: data.empleado_id,
                        nombre: data.empleado
                    },
                    motivo:
                    {
                        id: data.motivo_id,
                        nombre: data.motivo
                    }
                };
            }
        },

        /**
         * Registrar AtencionMedica
         */
        RegistrarAtencionMedica(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.atencionmedica.id);
                data.append("empleado_id", this.atencionmedica.empleado.id);
                data.append("puesto_id", this.atencionmedica.empleado.puesto_id);
                data.append("motivo_id", this.atencionmedica.motivo.id);
                data.append("fecha", this.atencionmedica.fecha);
                data.append("medicamentos", this.atencionmedica.medicamentos);

                this.isGuardaratencionmedica_loading = true;
                axios.post(this.url_atencionmedica + "/guardar", data).then(res =>
                {
                    this.isGuardaratencionmedica_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.ObtenerAtencionMedica();
                        this.CerrarModalAtencionMedica();
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
        CerrarModalAtencionMedica()
        {
            this.ver_modal_atencionmedica = false;
            this.atencionmedica = {};
        },

    },
    mounted()
    {
        this.ObtenerAtencionMedica();
        this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
    }
}
</script>
