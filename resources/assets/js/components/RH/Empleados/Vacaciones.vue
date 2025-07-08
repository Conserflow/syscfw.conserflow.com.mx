<template>
<main class="main">
    <!-- Listado VacaionesEmpleado -->
    <div class="card" v-show="tipoCardVacaionesEmpleado==1">
        <div class="card-header">
            <i class="fa fa-align-justify"></i>
            Vacaciones de Empleados
            <p v-show="false">
                <a class="btn btn-dark float-sm-right mx-1" data-toggle="collapse" href="#collapseReporte" role="button" aria-expanded="false" aria-controls="collapseReporte">
                    Reporte <i class="fas fa-file-excel"></i>
                </a>
            </p>

        </div>
        <div class="row">
            <div class="col">
                <div class="collapse multi-collapse" id="collapseReporte">
                    <br>
                    <div class="container row ml-2">
                        <div class="col-md-4">
                            <div class="">
                                <label>Inicio</label>
                                <input type="date" v-model="reporte.inicio" class="form-control">
                                <label>Fin</label>
                                <input type="date" v-model="reporte.fin" class="form-control">
                                <button @click="GenerarReporte" class="btn btn-dark mt-2">Generar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <vue-element-loading :active="isObtenervacaionesempleado_loading" />
            <v-client-table :columns="columns_vacaionesempleado" :data="list_vacaionesempleado" :options="options_vacaionesempleado">
                <template slot="id" slot-scope="props">
                    <div class="btn-group" role="group">
                        <div class="btn-group dropup" role="group">
                            <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-grip-horizontal"></i> Acciones
                            </button>
                            <div class="dropdown-menu">
                                <button type="button" class="dropdown-item" @click="AbrirModalVacaionesEmpleado(props.row)">
                                    <i class="fas fa-edit"></i> Registrar vacaciones
                                </button>

                                <button type="button" class="dropdown-item" @click="VerHistorial(props.row)">
                                    <i class="fas fa-list"></i> Historial de vacaciones
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                <template slot="dias_disponibles" slot-scope="props">
                    <span v-if="props.row.dias_disponibles>0" class="text-success">{{props.row.dias_disponibles}}</span>
                    <span v-else class="text-danger">{{props.row.dias_disponibles}}</span>
                </template>
            </v-client-table>
        </div>
    </div>

    <!--Inicio del modal VacaionesEmpleado-->
    <div v-if="ver_modal_vacaionesempleado" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_vacaionesempleado}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_vacaionesempleado"></h4>
                        <button type="button" class="close" @click="CerrarModalVacaionesEmpleado()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardarvacaionesempleado_loading" />
                        <div>
                            <!-- Formulario -->

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Empleado</label>
                                <div class="col-md-9">
                                    <input v-validate="'required'" data-vv-name="Empleado" type="text" disabled class="form-control" v-model="vacaionesempleado.empleado">
                                    <span class="text-danger">{{errors.first("Empleado")}}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Puesto</label>
                                <div class="col-md-6">
                                    <input type="text" disabled class="form-control" v-model="vacaionesempleado.puesto">
                                    <span v-show="vacaionesempleado.puesto==null" class="text-danger">El puesto es requierido</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Fecha de ingreso</label>
                                <div class="col-md-6">
                                    <input type="text" disabled class="form-control" v-model="vacaionesempleado.fecha_ingreso">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Periodo</label>
                                <div class="col-md-6">
                                    <v-select label="periodo" :options="list_periodos" v-model="periodo"></v-select>
                                </div>
                            </div>
                            <div class="form-group row" v-if="periodo">
                                <label class="col-md-3 form-control-label">Días ganados</label>
                                <div class="col-md-3">
                                    <input type="text" disabled class="form-control" v-model="periodo.dias_ganados">
                                </div>
                                <label class="col-md-3 form-control-label">Días disponibles</label>
                                <div class="col-md-3">
                                    <input type="text" disabled class="form-control" v-model="periodo.dias_disponibles">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Días a tomar</label>
                                <div class="col-md-3">
                                    <input type="number" min="1" max="32" data-vv-name="Días a tomar" @input="ComprobarDias" v-validate="'required'" class="form-control" v-model="vacaionesempleado.dias_a_tomar">
                                    <span class="text-danger">{{errors.first("Días a tomar")}}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="form-control-label col-md-3">Fecha de inicio</label>
                                <div class="col-md-3">
                                    <input type="date" v-validate="'required'" data-vv-name="Inicio" class="form-control" v-model="vacaionesempleado.fecha_inicio">
                                    <span class="text-danger">{{errors.first("Inicio")}}</span>
                                </div>
                                <label class="form-control-label col-md-2">Fecha de fin</label>
                                <div class="col-md-3">
                                    <input type="date" class="form-control" v-validate="'required'" data-vv-name="Fin" v-model="vacaionesempleado.fecha_fin">
                                    <span class="text-danger">{{errors.first("Fin")}}</span>
                                </div>
                            </div>

                            <!-- Formulario -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardarvacaionesempleado_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalVacaionesEmpleado()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                            <button type="button" v-if="tipoAccion_vacaionesempleado==1" class="btn btn-secondary" @click="RegistrarVacaionesEmpleado()">
                                <i class="fas fa-save"></i>&nbsp;Guardar
                            </button>
                            <button type="button" v-if="tipoAccion_vacaionesempleado==2" class="btn btn-secondary" @click="RegistrarVacaionesEmpleado()">
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

    <!--Inicio del modal Historial-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_historial}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title">Historial de vacaciones</h4>
                        <button type="button" class="close" @click="CerrarModalHistorial()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isHistorial_loading" />
                        <h6>{{historial_vacaciones_nombre}}</h6>
                        <br>
                        <table class="table table-sm" width="100%">
                            <thead>
                                <tr>
                                    <th width="10%">#</th>
                                    <th width="30%">Inicio</th>
                                    <th width="30%">Fin</th>
                                    <th width="30%">Días tomados</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(h,i) in historial_vacaciones">
                                    <tr :key="`fecha_${i}`">
                                        <td colspan="4">
                                            <table class="table table-sm bg-white">
                                                <thead>
                                                    <tr>
                                                        <td colspan="4" style="padding:0px">
                                                            <div class="">
                                                                <p class="font-weight-bold">{{h.anio}}</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tr class="bg-white" :key=j v-for="(f,j) in h.fechas">
                                                    <td width="10%">{{j+1}}</td>
                                                    <td width="30%">{{f.fecha_inicio}}</td>
                                                    <td width="30%">{{f.fecha_fin}}</td>
                                                    <td width="30%">
                                                        <div class="text-center">
                                                            {{f.dias_tomados}}
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td><span class="font-weight-bold">Total</span></td>
                                                    <td class="text-center">{{h.total_dias}} </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </template>

                            </tbody>
                        </table>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" @click="CerrarModalHistorial()">
                            <i class="fas fa-window-close"></i>&nbsp;Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!--Fin del modal Historial-->
</main>
</template>

<style>
.modal-md {
    width: 40%;
}
</style>

<script>
var config = require('../../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            //// VacaionesEmpleado
            anio: 2024,
            url_vacaionesempleado: "rh/vacacionesempleado",
            tipoAccion_vacaionesempleado: 1,
            tipoCardVacaionesEmpleado: 1,
            ver_modal_vacaionesempleado: false,
            tituloModal_vacaionesempleado: "",
            vacaionesempleado_id: 0,
            isGuardarvacaionesempleado_loading: false,
            isObtenervacaionesempleado_loading: false,
            columns_vacaionesempleado: [
                "id",
                "empleado",
                "puesto",
                "fecha_ingreso",
                "dias_ganados",
                "dias_tomados",
                "dias_disponibles",
            ],
            list_vacaionesempleado: [],
            vacaionesempleado:
            {},
            options_vacaionesempleado:
            {
                headings:
                {
                    id: "Acciones",
                    Empleado: "Empleado",

                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            },
            list_periodos: [],
            periodo:
            {},
            valido: false,
            // Historial de vacaicones
            historial_vacaciones: [],
            historial_vacaciones_nombre: "",
            ver_modal_historial: 0,
            isHistorial_loading: false,
            // Reporte
            reporte:
            {},

        }
    },
    methods:
    {
        // Metodos
        /**
         * Obtener todos los empleados con los dias de vacaciones disponibles
         */
        ObtenerVacaionesEmpleado()
        {
            this.isObtenervacaionesempleado_loading = true;
            axios.get(`${this.url_vacaionesempleado}/obtener/${this.anio}`).then(res =>
            {
                this.isObtenervacaionesempleado_loading = false;
                if (res.data.status)
                {
                    this.list_vacaionesempleado = res.data.empleados;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Abrir modal VacaionesEmpleado
         */
        AbrirModalVacaionesEmpleado(data = {})
        {
            this.tituloModal_vacaionesempleado = "Registrar vacaciones";
            this.ver_modal_vacaionesempleado = true;
            this.vacaionesempleado = {
                ...data
            };
            axios.post(this.url_vacaionesempleado + "/periodos",
            {
                empleado_id: data.id
            }).then(res =>
            {
                if (res.data.status)
                {
                    this.list_periodos = res.data.periodos;
                }
                else
                    toastr.error(res.data.mensaje);
            });
        },

        /**
         * Registrar VacaionesEmpleado
         */
        async RegistrarVacaionesEmpleado()
        {
            const isValid = this.$validator.validate();
            if (!isValid) return;
            if (this.vacaionesempleado.puesto == null)
            {
                toastr.warning("El puesto es requerido");
                return;
            }
            if (!this.valido)
            {
                toastr.warning("Periodo no válido");
                return;
            }

            // Validar fechas
            var hoy = new Date();
            let fecha_inicio = new Date(this.vacaionesempleado.fecha_inicio);
            let fecha_fin = new Date(this.vacaionesempleado.fecha_fin);
            if (fecha_fin < fecha_inicio)
            {
                toastr.warning("Fecha fin no puede ser menor a la de inicio");
                return;
            }

            let data = new FormData();
            data.append("empleado_id", this.vacaionesempleado.empleado_id);
            data.append("fecha_inicio", this.vacaionesempleado.fecha_inicio);
            data.append("fecha_fin", this.vacaionesempleado.fecha_fin);
            data.append("contrato_id", this.vacaionesempleado.contrato_id);
            data.append("periodo", this.periodo.periodo);
            data.append("dias_a_tomar", this.vacaionesempleado.dias_a_tomar);

            this.isGuardarvacaionesempleado_loading = true;
            axios.post(this.url_vacaionesempleado + "/guardar", data).then(res =>
            {
                this.isGuardarvacaionesempleado_loading = false;
                if (res.data.status)
                {
                    this.periodo = null;
                    this.list_periodos = [];
                    toastr.success("Guardado correctamente");
                    this.ObtenerVacaionesEmpleado();
                    this.CerrarModalVacaionesEmpleado();
                }
                else
                {
                    if (res.data.tipo == 2)
                    {
                        Swal.fire(
                            "Error al registrar las vacaciones",
                            res.data.mensaje,
                            "error"
                        )
                    }
                    else
                        toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Comprueba que los días a tomar no superen los días permitidos
         */
        ComprobarDias()
        {
            this.valido = false;
            if (this.periodo == null) return;
            if (this.vacaionesempleado.dias_a_tomar == null) return;
            if (this.vacaionesempleado.dias_a_tomar > this.periodo.dias_disponibles)
            {
                toastr.warning("Días insuficientes");
                setTimeout(() =>
                {
                    this.vacaionesempleado.dias_a_tomar = 0;
                }, 1500)
            }
            this.valido = true;
        },
        /**
         * Cerrar modal
         */
        CerrarModalVacaionesEmpleado()
        {
            this.ver_modal_vacaionesempleado = false;
        },

        /**
         * Muestra todas las vacaiones tomadas del empleado
         */
        VerHistorial(emp)
        {
            this.ver_modal_historial = true;
            this.isHistorial_loading = true;
            this.historial_vacaciones_nombre = emp.empleado;
            axios.get(`${this.url_vacaionesempleado}/historial/${emp.id}`).then(res =>
            {
                this.isHistorial_loading = false;
                if (res.data.status)
                {
                    this.historial_vacaciones = res.data.historial;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Cerrar modal de historial
         */
        CerrarModalHistorial()
        {
            this.periodo = null;
            this.ver_modal_historial = false;
            this.historial_vacaciones = [];
            this.historial_vacaciones_nombre = "";
        },

        /**
         * Generar reporte
         */
        GenerarReporte()
        {
            const data = `${this.reporte.inicio}&${this.reporte.fin}`;
            window.open(`${this.url_vacaionesempleado}/reporte/${data}`, "_blak");
        },
    },
    mounted()
    {
        this.ObtenerVacaionesEmpleado();

    }
}
</script>
