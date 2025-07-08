<template>
<main class="main">
    <!-- Listado FactorRiesgo -->
    <div class="card" v-show="tipoCardFactorRiesgo==1">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> FACTORES DE RIESGO
            <button v-show="PermisosCRUD.Create" type="button" @click="AbrirModalFactorRiesgo(true)" class="btn btn-dark float-sm-right">
                <i class="fas fa-plus"></i>&nbsp;Nuevo
            </button>
        </div>
        <div class="card-body">
            <vue-element-loading :active="isObtenerfactorriesgo_loading" />
            <v-client-table :columns="columns_factorriesgo" :data="list_factorriesgo" :options="options_factorriesgo">
                <template slot="id" slot-scope="props">
                    <div class="btn-group" role="group">
                        <div class="btn-group dropup" role="group">
                            <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-grip-horizontal"></i> Acciones
                            </button>
                            <div class="dropdown-menu">
                                <button v-show="PermisosCRUD.Upload" type="button" class="dropdown-item" @click="SubirDocumento(props.row.id)">
                                    <i class="fas fa-upload"></i> Subir Evidencia
                                </button>
                                <button v-show="PermisosCRUD.Download" type="button" class="dropdown-item" @click="DescargarCuestionario(props.row.id)">
                                    <i class="fas fa-download"></i> Descargar Cuestionario
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                <template slot="documento" slot-scope="props">
                    <template v-if="PermisosCRUD.Download">
                        <button v-if="props.row.documento==null" class="btn btn-dark">N/D</button>
                        <button v-else class="btn btn-dark" @click="DescargarEvidencia(props.row.id)">
                            <i class="fas fa-download"></i>
                        </button>
                    </template>
                    <template v-else>
                        <button class="btn btn-dark">N/D</button>
                    </template>
                </template>
            </v-client-table>
        </div>
    </div>

    <!--Inicio del modal FactorRiesgo-->
    <div v-if="ver_modal_factorriesgo" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_factorriesgo}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_factorriesgo"></h4>
                        <button type="button" class="close" @click="CerrarModalFactorRiesgo()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardarfactorriesgo_loading" />
                        <div>
                            <!-- Formulario -->
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Nombre</label>
                                <div class="col-md-9">
                                    <v-select label="nombre" :options="list_empleados" v-validate="'required'" v-model="factorriesgo.empleado" data-vv-name="Nombre">
                                    </v-select>
                                    <span class="text-danger">{{ errors.first('Nombre') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Puesto</label>
                                <div class="col-md-9">
                                    <input disabled type="text" data-vv-name="Puesto" class="form-control" v-model="factorriesgo.empleado.puesto" v-validate="'required'">
                                    <span class="text-danger">{{ errors.first('Puesto') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Fecha</label>
                                <div class="col-md-9">
                                    <input type="date" v-validate="'required'" v-model="factorriesgo.fecha" class="form-control" data-vv-name="Fecha" />
                                    <span class="text-danger">{{ errors.first('Fecha') }}</span>
                                </div>
                            </div>

                            <!-- Formulario -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardarfactorriesgo_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalFactorRiesgo()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                            <button type="button" v-if="tipoAccion_factorriesgo==1" class="btn btn-secondary" @click="RegistrarFactorRiesgo(true)">
                                <i class="fas fa-save"></i>&nbsp;Guardar
                            </button>
                            <button type="button" v-if="tipoAccion_factorriesgo==2" class="btn btn-secondary" @click="RegistrarFactorRiesgo(false)">
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
    <!--Fin del modal FactorRiesgo-->

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
            //// FactorRiesgo
            url_factorriesgo: "rh/factorriesgo",
            tipoAccion_factorriesgo: 1,
            PermisosCRUD:
            {},
            tipoCardFactorRiesgo: 1,
            ver_modal_factorriesgo: false,
            tituloModal_factorriesgo: "",
            factorriesgo_id: 0,
            isGuardarfactorriesgo_loading: false,
            isObtenerfactorriesgo_loading: false,
            columns_factorriesgo: [
                "id",
                "fecha",
                "nombre",
                "puesto",
                "documento"
            ],
            list_factorriesgo: [],
            list_empleados: [],
            factorriesgo:
            {
                empleado:
                {},
            },
            options_factorriesgo:
            {
                headings:
                {
                    id: "Acciones",
                    nombre: "Empleado"

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
        ObtenerFactorRiesgo()
        {
            this.isObtenerfactorriesgo_loading = true;
            axios.get(this.url_factorriesgo + "/obtener").then(res =>
            {
                this.isObtenerfactorriesgo_loading = false;
                if (res.data.status)
                {
                    this.list_factorriesgo = res.data.factorriesgo;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Abrir modal FactorRiesgo
         */
        AbrirModalFactorRiesgo(nuevo, data = {})
        {
            this.ver_modal_factorriesgo = true;
            if (nuevo)
            {
                this.tituloModal_factorriesgo = "Registrar Factores de Riesgo";
                this.tipoAccion_factorriesgo = 1;
                this.ObtenerEmpleados();
            }
        },

        /**
         * Obtener los empleados y el puesto
         */
        ObtenerEmpleados()
        {
            axios.get("rh/empleados/empleadospuesto/obtener").then(res =>
            {
                if (res.data.status)
                {
                    this.list_empleados = res.data.empleados;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Registrar FactorRiesgo
         */
        RegistrarFactorRiesgo(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                let data = new FormData();
                data.append("empleado_id", this.factorriesgo.empleado.id);
                data.append("puesto_id", this.factorriesgo.empleado.puesto_id);
                data.append("fecha", this.factorriesgo.fecha);

                this.isGuardarfactorriesgo_loading = true;
                axios.post(this.url_factorriesgo + "/guardar", data).then(res =>
                {
                    this.isGuardarfactorriesgo_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.ObtenerFactorRiesgo();
                        this.CerrarModalFactorRiesgo();
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
        CerrarModalFactorRiesgo()
        {
            this.ver_modal_factorriesgo = false;
            this.factorriesgo = {
                empleado:
                {}
            };
        },

        /**
         * Descargar la evidencia subida anteriormente
         */
        DescargarEvidencia(id)
        {
            window.open(this.url_factorriesgo + "/descargarevidencia/" + id, "_blank");
        },

        /**
         * Subir la evidencia de los factores de riesgo
         */
        SubirDocumento(id)
        {
            Swal.fire(
            {
                title: "Cargar documento (PDF)",
                input: "file",
                confirmButtonText: "Cargar",
            }).then(result =>
            {
                if (result.value == null) return;
                if (result.value.type === "application/pdf")
                {
                    let data = new FormData();
                    data.append("id", id);
                    data.append("evidencia", result.value);
                    axios.post(this.url_factorriesgo + "/subirdocumento", data).then(res =>
                    {
                        if (res.data.status)
                        {
                            toastr.success("Documento subido correctamente");
                            this.ObtenerFactorRiesgo();
                        }
                        else
                        {
                            toastr.error(res.data.mensaje);
                        }
                    }).catch(r =>
                    {
                        console.error(r);
                        toastr.error("Error al subir el documento");
                    })
                }
                else
                {
                    toastr.warning("Seleccione un PDF");
                }
            })
        },

        /**
         * Descargar el cuestionario de los factores de riesgo
         */
        DescargarCuestionario(id)
        {
            window.open(this.url_factorriesgo + "/descargarcuestionario/" + id, "_blank");
        },

    },

    mounted()
    {
        this.ObtenerFactorRiesgo();
        this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
    }
}
</script>
