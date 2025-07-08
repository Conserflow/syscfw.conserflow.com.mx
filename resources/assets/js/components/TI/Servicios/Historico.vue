<template>
<main class="main">
    <div class="">

        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Histórico Servicios

                <button type="button" class="btn btn-dark float-sm-right" @click="AbrirModalHistorico(true)">
                    <i class="fas fa-plus">&nbsp;</i>Nuevo
                </button>

                <button data-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1" class="btn btn-dark float-sm-right mr-1">
                    <i class="fas fa-file-pdf mr-1"></i>Descargar
                </button>
            </div>
            <div class="card-body">
                <div class="collapse multi-collapse" id="multiCollapseExample1">
                    <div class="container row">
                        <div class="col-md-3">
                            <div class="">
                                <label>Año</label>
                                <select class="form-control" v-model="anio">
                                    <option value="2020">2020</option>
                                    <option value="2021">2021</option>
                                    <option value="2022">2022</option>
                                </select>
                                <button @click="DescargarHistorico" class="btn btn-dark mt-2">Generar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                <vue-element-loading :active="isObtenerLoading" />
                <div>
                    <v-client-table :data="list_historico" :options="options" :columns="columns">
                        <template slot="id" slot-scope="props">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <template>
                                        <button type="button" @click="AbrirModalHistorico(false, props.row)" class="dropdown-item">
                                            <i class="fas fa-edit mr-1"></i>Actualizar
                                        </button>
                                        <button type="button" @click="Eliminar(props.row.id)" class="dropdown-item">
                                            <i class="fas fa-times mr-1"></i>Eliminar
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </v-client-table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-dark modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="CerrarModalHistorico()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <label>Tipo</label>
                                <select class="form-control" v-model="tipo" data-vv-name="tipo" v-validate="'required'">
                                    <option value="PREVENTIVO">Preventivo</option>
                                    <option value="CORRECTIVO">Correctivo</option>
                                </select>
                                <span class="text-danger">{{errors.first("tipo")}}</span>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label>Usuario</label>
                                <input class="form-control" type="text" v-model="usuario" data-vv-name="usuario" v-validate="'required'">
                                <span class="text-danger">{{errors.first("usuario")}}</span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-9 mb-3">
                                <label>Problema/Servicio</label>
                                <textarea class="form-control" name="name" rows="2" cols="80" data-vv-name="Problema/Servicio" v-validate="'required'" v-model="problema_servicio"></textarea>
                                <span class="text-danger">{{errors.first("Problema/Servicio")}}</span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Fecha Reporte</label>
                                <input type="date" class="form-control" v-model="fecha_reporte" data-vv-name="fecha reporte" v-validate="'required'">
                                <span class="text-danger">{{errors.first("fecha reporte")}}</span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-9 mb-3">
                                <label>Solución</label>
                                <textarea class="form-control" name="name" rows="2" cols="80" data-vv-name="solucion" v-validate="'required'" v-model="solucion"></textarea>
                                <span class="text-danger">{{errors.first("solucion")}}</span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Fecha Solución</label>
                                <input type="date" class="form-control" v-model="fecha_solucion" data-vv-name="fecha solucion" v-validate="'required'">
                                <span class="text-danger">{{errors.first("fecha solucion")}}</span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <label>Reincidencia</label>
                                <input type="text" class="form-control" v-model="reincidencia" data-vv-name="reincidencia" v-validate="'required'">
                                <span class="text-danger">{{errors.first("reincidencia")}}</span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-9 mb-3">
                                <label>Realiza</label>
                                <v-select v-validate="'required'" label="nombre" :options="list_empleados" v-model="realiza"></v-select>
                                <span class="text-danger">{{errors.first("realiza")}}</span>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isObtenerLoading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalHistorico()"><i class="fas fa-times"></i>&nbsp;Cerrar</button>
                            <button type="button" v-if="tipoAccion == 1" class="btn btn-secondary" @click="GuardarHistorico(1)"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                            <button type="button" v-if="tipoAccion == 2" class="btn btn-secondary" @click="GuardarHistorico(0)"><i class="fas fa-save"></i>&nbsp;Actualizar</button>
                        </div>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- Modal -->

    </div>
</main>
</template>

<script>
var config = require("../../Herramientas/config-vuetables-client").call(this);

export default
{
    data()
    {
        return {
            url: "ti/historico",
            isObtenerLoading: false,
            isGuardarLoading: false,
            modal: 0,
            tituloModal: "",
            tipoAccion: 0,
            listaEmpleados: [],
            id: 0,
            tipo: "",
            usuario: "",
            problema_servicio: "",
            fecha_reporte: "",
            solucion: "",
            fecha_solucion: "",
            reincidencia: "",
            realiza:
            {},
            list_empleados:[],
            list_historico: [],
            columns: [
                "id",
                "tipo",
                "nombre_usuario",
                "problema_servicio",
                "fecha_reporte",
                "fecha_solucion",
                "empleado_realiza",
            ],
            reporte_inicio: "",
            reporte_fin: "",
            anio: 2022,
            options:
            {
                headings:
                {
                    "id": "Acciones",
                    "empleado_realiza": "Realiza"
                }, // Headings,
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            }, //options
        }
    },
    methods:
    {
        /**
         * Obtiene todos los mantenimientos registrados
         */
        ObtenerHistoricos()
        {
            this.isObtenerLoading = true;
            axios.get(this.url + "/obtener").then(res =>
            {
                this.isObtenerLoading = false;
                if (res.data.status)
                {
                    this.list_historico = res.data.historico;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        AbrirModalHistorico(nuevo, data = [])
        {
            this.ObtenerEmpleados();
            this.modal = 1;
            if (nuevo)
            {
                this.tituloModal = "GuardarHistorico";
                this.tipoAccion = 1;
            }
            else
            {
                this.tituloModal = "Actualizar";
                this.tipoAccion = 2;
                this.id = data["id"];
                this.tipo = data["tipo"];
                this.usuario = data["nombre_usuario"];
                this.problema_servicio = data["problema_servicio"];
                this.fecha_reporte = data["fecha_reporte"];
                this.solucion = data["solucion"];
                this.fecha_solucion = data["fecha_solucion"];
                this.reincidencia = data["reincidencia"];
                this.realiza = {
                    id: data["empleado_realiza_id"],
                    nombre: data["empleado_realiza"]
                };
            }
        },

        CerrarModalHistorico()
        {
            this.vaciar();
            this.modal = 0;
        },

        /**
         * Limpiar modal
         */
        vaciar()
        {
            this.tipo = "";
            this.usuario = "";
            this.problema_servicio = "";
            this.fecha_reporte = "";
            this.solucion = "";
            this.fecha_solucion = "";
            this.reincidencia = "";
        },

        /**
         * Guardar el registro
         */
        GuardarHistorico(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                this.isGuardarLoading = true;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.id);

                data.append("tipo", this.tipo);
                data.append("empleado_id", 0);
                data.append("nombre_usuario", this.usuario);
                data.append("problema_servicio", this.problema_servicio);
                data.append("fecha_reporte", this.fecha_reporte);
                data.append("solucion", this.solucion);
                data.append("fecha_solucion", this.fecha_solucion);
                data.append("reincidencia", this.reincidencia);
                data.append("empleado_realiza_id", this.realiza.id);
                axios.post(this.url + "/guardar", data).then(res =>
                {
                    this.isGuardarLoading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado Correctamente");
                        this.CerrarModalHistorico();
                        this.ObtenerHistoricos();
                    }
                    else
                    {
                        toastr.error(res.data.mensaje);
                    }
                });

            });
        },

        /**
         * Eliminar registro
         */
        Eliminar(id)
        {
            this.isObtenerLoading = true;
            axios.post(this.url + "/eliminar",
            {
                id
            }).then(res =>
            {
                this.isObtenerLoading = false;
                if (res.data.status)
                {
                    toastr.success("Eliminado Correctamente");
                    this.ObtenerHistoricos();
                }
                else
                {
                    toastr.error(res.data);
                }
            });
        },

        /**
         * Obtiene todos los empleados
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
         * Descargar historico
         */
        DescargarHistorico()
        {
            window.open(`${this.url}/descargar/${this.anio}`);
        },

    },
    mounted()
    {
        this.ObtenerHistoricos();
    }
}
</script>
