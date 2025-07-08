<template>
<main class="main">
    <!-- Listado CuestionarioInfra -->
    <div class="card" v-show="tipoCardCuestionarioInfra==1">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Cuestionario Infraestructura
            <button type="button" @click="Descargar()" class="btn btn-dark float-sm-right ml-1">
                <i class="fas fa-file-pdf"></i>&nbsp;Descargar
            </button>
            <button v-if="PermisosCRUD.Create" type="button" @click="AbrirModalCuestionarioInfra(true)" class="btn btn-dark float-sm-right">
                <i class="fas fa-plus"></i>&nbsp;Nuevo
            </button>
        </div>
        <div class="card-body">
            <vue-element-loading :active="isObtenercuestionarioinfra_loading" />
            <v-client-table :columns="columns_cuestionarioinfra" :data="list_cuestionarioinfra" :options="options_cuestionarioinfra">
                <template slot="id" slot-scope="props">
                    <div class="btn-group" role="group">
                        <div class="btn-group dropup" role="group">
                            <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-grip-horizontal"></i> Acciones
                            </button>
                            <div class="dropdown-menu">
                                <button v-if="PermisosCRUD.Upload" type="button" class="dropdown-item" @click="SubirCuestionario(props.row.id)">
                                    <i class="fas fa-edit"></i> Subir Cuestionario
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                <template slot="documento" slot-scope="props" v-if="PermisosCRUD.Download">
                    <span v-if="props.row.documento==null" class="btn btn-dark">N/D</span>
                    <button @click="DescargarEvidencia(props.row.id)" v-else class="btn btn-dark">
                        <i class="fas fa-download"></i>
                    </button>
                </template>
            </v-client-table>
        </div>
    </div>

    <!--Inicio del modal CuestionarioInfra-->
    <div v-if="ver_modal_cuestionarioinfra" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_cuestionarioinfra}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_cuestionarioinfra"></h4>
                        <button type="button" class="close" @click="CerrarModalCuestionarioInfra()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardarcuestionarioinfra_loading" />
                        <div>
                            <!-- Formulario -->

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Empleado</label>
                                <div class="col-md-9">
                                    <v-select label="nombre" :options="list_empleados" v-validate="'required'" v-model="cuestionarioinfra.empleado" data-vv-name="Empleado">
                                    </v-select>
                                    <span class="text-danger">{{ errors.first('Empleado') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Fecha</label>
                                <div class="col-md-9">
                                    <input type="date" v-validate="'required'" v-model="cuestionarioinfra.fecha" class="form-control" data-vv-name="Fecha" />
                                    <span class="text-danger">{{ errors.first('Fecha') }}</span>
                                </div>
                            </div>

                            <!-- Formulario -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardarcuestionarioinfra_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalCuestionarioInfra()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                            <button type="button" v-if="tipoAccion_cuestionarioinfra==1" class="btn btn-secondary" @click="RegistrarCuestionarioInfra(true)">
                                <i class="fas fa-save"></i>&nbsp;Guardar
                            </button>
                            <button type="button" v-if="tipoAccion_cuestionarioinfra==2" class="btn btn-secondary" @click="RegistrarCuestionarioInfra(false)">
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
    <!--Fin del modal CuestionarioInfra-->

</main>
</template>

<script>
import Utilerias from '../Herramientas/utilerias.js';
var config = require('../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            PermisosCRUD:{},
            //// CuestionarioInfra
            url_cuestionarioinfra: "rh/cuestionarioinfra",
            tipoAccion_cuestionarioinfra: 1,
            tipoCardCuestionarioInfra: 1,
            ver_modal_cuestionarioinfra: false,
            tituloModal_cuestionarioinfra: "",
            cuestionarioinfra_id: 0,
            isGuardarcuestionarioinfra_loading: false,
            isObtenercuestionarioinfra_loading: false,
            columns_cuestionarioinfra: [
                "id",
                "nombre",
                "fecha",
                "documento"
            ],
            list_cuestionarioinfra: [],
            cuestionarioinfra:
            {},
            options_cuestionarioinfra:
            {
                headings:
                {
                    id: "Acciones",
                    nombre: "Empleado",
                    fecha: "Fecha",

                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            },
            list_empleados: [],

        }
    },
    methods:
    {
        // Metodos
        /**
         * Obtener todos los registros
         */
        ObtenerCuestionarioInfra()
        {
            this.isObtenercuestionarioinfra_loading = true;
            axios.get(this.url_cuestionarioinfra + "/obtener").then(res =>
            {
                this.isObtenercuestionarioinfra_loading = false;
                if (res.data.status)
                {
                    this.list_cuestionarioinfra = res.data.cuestionarioinfra;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Abrir modal CuestionarioInfra
         */
        AbrirModalCuestionarioInfra(nuevo, data = {})
        {
            this.ObtenerEmpleados();
            this.ver_modal_cuestionarioinfra = true;
            if (nuevo)
            {
                this.tituloModal_cuestionarioinfra = "Registrar Cuestionario infraestructura";
                this.tipoAccion_cuestionarioinfra = 1;
            }
            else
            {
                this.tituloModal_cuestionarioinfra = "Actualizar Cuestionario infraestructura";
                this.tipoAccion_cuestionarioinfra = 2;
                this.asd = {
                    ...data
                };
            }
        },

        /**
         * Registrar CuestionarioInfra
         */
        RegistrarCuestionarioInfra(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.cuestionarioinfra_id);
                data.append("empleado_id", this.cuestionarioinfra.empleado.id);
                data.append("fecha", this.cuestionarioinfra.fecha);

                this.isGuardarcuestionarioinfra_loading = true;
                axios.post(this.url_cuestionarioinfra + "/guardar", data).then(res =>
                {
                    this.isGuardarcuestionarioinfra_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.ObtenerCuestionarioInfra();
                        this.CerrarModalCuestionarioInfra();
                    }
                    else
                    {
                        toastr.error(res.data.mensaje);
                    }
                })

            })
        },

        /**
         * Obtener todos los empleados registrados
         */
        ObtenerEmpleados()
        {
            axios.get("generales/empleadoactivos").then(res =>
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
         * Cerrar modal
         */
        CerrarModalCuestionarioInfra()
        {
            this.ver_modal_cuestionarioinfra = false;
            this.cuestionarioinfra = {};
        },

        /**
         * Subir el cuestionario del empleado
         */
        SubirCuestionario(id)
        {
            Swal.fire(
            {
                title: "Cargar cuestionario (PDF)",
                input: "file",
                confirmButtonText: "Cargar",
            }).then(result =>
            {
                console.error(result);
                if (result.value == null) return;
                if (result.value.type === "application/pdf")
                {
                    let data = new FormData();
                    data.append("cuestionario_id", id);
                    data.append("evidencia", result.value);
                    axios.post(this.url_cuestionarioinfra + "/subircuestionario", data).then(res =>
                    {
                        if (res.data.status)
                        {
                            toastr.success("Documento subido correctamente");
                            this.ObtenerCuestionarioInfra();
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
         * Desscargar plantilla
         */
        Descargar()
        {
            window.open("rh/cuestionarioinfra/plantilla", "_blank")
        },

        /**
         * Descargar el documento
         */
        DescargarEvidencia(id)
        {
            window.open(this.url_cuestionarioinfra + '/descargarevidencia/' + id, '_blank');
        }
    },
    mounted()
    {
        this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
        this.ObtenerCuestionarioInfra();
    }
}
</script>
