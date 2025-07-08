<template>
<main class="main">
    <div class="container-fluid">

        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Buscar
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Fecha inicial</label>
                        <input type="date" class="form-control" v-model="date_one" ref="date_one">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Fecha final</label>
                        <input type="date" class="form-control" v-model="date_two" ref="date_two">
                    </div>
                    <div class="form-group col-md-1">
                        <label>&nbsp;</label>
                        <br>
                        <button type="button" class="btn btn-dark" name="button" @click="BuscarAsistencias">Buscar</button>
                    </div>
                    <div class="form-group col-md-2">
                        <template>
                            <label>Generar Reporte</label>
                            <br>
                            <select class="form-control" @change="GenerarReporte" v-model="reporte_estado">
                                <option value="1">Conserflow Semanal</option>
                                <option value="2">Conserflow Quincenal</option>
                                <option value="3">CSCT Semanal</option>
                                <option value="4">CSCT Quincenal</option>
                            </select>
                        </template>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <input class="form-control" v-model="data_busqueda" @keyup.enter="buscarEmp(0)">
                    </div>
                    <div class="col-md-2 mb-3">
                        <button class="btn btn-dark" @click="buscarEmp(0)"> <i class="fas fa-search"></i> </button>
                        <button class="btn btn-dark" @click="BorrarBusqueda()"> <i class="fas fa-broom"></i> </button>
                    </div>
                </div>
                <div class="table">
                    <div class="table-scroll">
                        <table class="table-main">
                            <thead>
                                <!-- Fechas -->
                                <tr>
                                    <th class="fix-col">
                                        NOMBRE DEL EMPLEADO
                                        <br>

                                    </th>
                                    <template v-if="data_response != ''">
                                        <template v-for="(f,i) in data_response.fechas">

                                            <td :key="i">
                                                {{f[0]}}
                                                <br>
                                                {{f[1]}}
                                            </td>

                                        </template>
                                    </template>

                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="data_response != ''">
                                    <template v-for="(d,i) in data_response.asistencias">
                                        <tr :key="i">
                                            <td class="fix-col">
                                                {{d.datos_empleado.nombre}}
                                            </td>
                                            <template v-for="(h,j) in d.asistencias">
                                                <td :key="j">
                                                    <p v-html="h.horario">
                                                    </p>
                                                </td>
                                            </template>
                                        </tr>

                                    </template>
                                </template>

                            </tbody>
                        </table>
                    </div>
                </div>
                <nav aria-label="">
                    <ul class="pagination">
                        <li :key="i" v-for="(p,i) in total_paginas" class="page-item">
                            <a class="page-link" href="#" @click="CargarPagina(p.index)">{{p.nombre}}</a>
                        </li>
                        <li class="page-item ml-3 my-auto">
                            Página actual: {{pagina_actual}}
                        </li>
                    </ul>
                </nav>

            </div>
        </div>

    </div>

</main>
</template>

<script>
import Utilerias from "../../Herramientas/utilerias.js";
var config = require("../../Herramientas/config-vuetables-client").call(this);
export default
{
    data()
    {
        return {
            data_busqueda: "",
            data_response: "",
            data_response_temporal: "",
            tab: 1,
            url: "/registro",
            valor_nuevo_estado: "",
            empleado: null,
            reporte_estado: "",
            // detalle: false,
            desabilitar: false,
            archivo: null,
            date_uno: "",
            date_dos: "",
            date_one: "",
            date_two: "",
            detalle: 1,
            estado: "",
            observaciones: "",
            id: "",
            registroasistencia:
            {
                id: 0,
                hora_entrada: "",
                hora_salida_comida: "",
                hora_entrada_comida: "",
                hora_salida: "",
                fecha: "",
                dia_id: 0,
                registro_id: 0,
                empleado_id: 0,
                fechanombre: "",
            },
            listaEmpleados: [],
            listaDias: [],
            dataR: [],
            modal: 0,
            tituloModal: "",
            tipoAccion: 0,
            disabled: 0,
            isLoading: false,
            isLoadingDetalle: false,
            // Tabla de asistencias
            pagina_actual: 0,
            listTipoRegistro: [],
            total_paginas: [],
            page: 1,
            total: 0,
            headings_asistencia: ["Empleado"],
            columns: ["empleado.id", "empleado.nombre", "empleado.ap_paterno", "empleado.ap_materno", "contador"],
            tableData: [],
            columnsAsistencias: ["fecha", "nombre", "hora_entrada", "hora_salida_comida", "hora_entrada_comida", "hora_salida", ],
            tableDataAsistencia: [],
            tableDataRegistros: [],
            lstAsistenciaCompleta: [],
            lstAsistenciaIncompleta: [],
            lstInasistencia: [],
            ListadoEstados: [],
            nombre_empleado: "",
            columnsRegistros: ["registros.id", "registros.fecha", "nombre_dia", "registros.hora_entrada", "registros.hora_salida_comida", "registros.hora_entrada_comida", "registros.hora_salida", "registros.observaciones"],
            options:
            {
                headings:
                {
                    "empleado.id": "Acciones",
                    "empleado.nombre": "Nombre",
                    "empleado.ap_paterno": "Apellido Paterno",
                    "empleado.ap_materno": "Apellido Materno",
                    "contador": "Estado",
                },
                perPage: 20,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                sortable: ["empleado.nombre", "empleado.ap_paterno", "empleado.ap_materno"],
                filterable: ["empleado.nombre", "empleado.ap_paterno", "empleado.ap_materno"],
                filterByColumn: true,
                texts: config.texts
            },
            optionsRegistros:
            {
                headings:
                {
                    "registros.id": "Acciones",
                    "registros.fecha": "Fecha",
                    "registros.dia_id": "Día",
                    "registros.hora_entrada": "Entrada",
                    "registros.hora_salida_comida": "Salida comida",
                    "registros.hora_entrada_comida": "Entrada comida",
                    "registros.hora_salida": "Salida",
                    "registros.observaciones": "Observaciones"
                },
                perPage: 20,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                sortable: ["registros.fecha", "registros.dia_id", "registros.hora_entrada", "registros.hora_salida_comida", "registros.hora_entrada_comida", "registros.hora_salida", "registros.observaciones"],
                filterable: ["registros.fecha", "registros.dia_id", "registros.hora_entrada", "registros.hora_salida_comida", "registros.hora_entrada_comida", "registros.hora_salida", "registros.observaciones"],
                filterByColumn: true,
                texts: config.texts
            },
            optionsdireccion:
            {
                headings:
                {
                    nombre: "Día",
                },
                perPage: 20,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                sortable: ["fecha", "nombre", "hora_entrada", "hora_salida_comida", "hora_entrada_comida", "hora_salida"],
                filterable: ["fecha", "nombre", "hora_entrada", "hora_salida_comida", "hora_entrada_comida", "hora_salida"],
                filterByColumn: true,
                listColumns:
                {
                    condicion: config.columnCondicion
                },
                texts: config.texts
            },
        }
    },
    methods:
    {

        /**
         * Buscar la asistencia en las fechas ingresadas
         */
        BuscarAsistencias()
        {

            let f1 = this.$refs.date_one.value;
            let f2 = this.$refs.date_two.value;
            if (f1 == "")
            {
                toastr.warning("Ingrese fecha inicial");
                return;
            }
            if (f2 == "")
            {
                toastr.warning("Ingrese fecha final");
                return;
            }

            let fecha_inicio = new Date(f1);
            let fecha_fin = new Date(f2);

            let time_inicio = new Date(f1).getTime();
            let time_fin = new Date(f2).getTime();
            let total_dias = (time_fin - time_inicio) / (1000 * 60 * 60 * 24);
            if (fecha_inicio > fecha_fin)
            {
                toastr.warning("Verifique sus fechas");
                return;
            }
            if (total_dias > 31)
            {
                toastr.warning("Seleccione máximo 1 mes de asistencias");
                return;
            }

            this.CargarPagina(0); // Pagina 0
        },
        /**
         * Buscar en página anterios
         */
        anterior()
        {
            this.page = this.page == 1 ? 1 : this.page - 1;
            if (this.data_busqueda === "")
            {
                this.CargarPagina(this.page * 20);
            }
            else
            {
                this.buscarEmp(this.page * 20);
            }
        },
        /**
         * Buscar página siguiente
         */
        siguiente()
        {
            this.page = this.page == this.total ? this.page : this.page + 1;
            if (this.data_busqueda === "")
            {
                this.CargarPagina(this.page * 20);
            }
            else
            {
                this.buscarEmp(this.page * 20);
            }
        },
        /**
         * Buscar el empleado ingresado
         */
        buscarEmp(index)
        {
            let f1 = this.$refs.date_one.value;
            let f2 = this.$refs.date_two.value;
            if (f1 == "")
            {
                toastr.warning("Ingrese fecha inicial");
                return;
            }
            if (f2 == "")
            {
                toastr.warning("Ingrese fecha final");
                return;
            }

            let fecha_inicio = new Date(f1);
            let fecha_fin = new Date(f2);
            if (fecha_inicio > fecha_fin)
            {
                toastr.warning("Verifique sus fechas");
                return;
            }

            this.data_response_temporal = this.data_response;
            axios.post("rh/asistencias/buscarempleado",
            {
                fecha_uno: this.date_one,
                fecha_dos: this.date_two,
                index: index,
                data: this.data_busqueda,
            }).then(res =>
            {
                if (res.status)
                {
                    this.data_response = res.data;
                    this.pagina_actual = index + 1;
                    this.headings_asistencia = ["Empleado"];
                    this.lstAsistenciaCompleta = res.data.asistencias;
                    this.total_paginas = res.data.paginas;
                    this.total = res.data.asistencias.length;
                    res.data.fechas.forEach(f =>
                    {
                        this.headings_asistencia.push(f);
                    });
                    this.total = res.data.paginas;
                }
                else
                {
                    toastr.error("Error", "Contacte al administrador");
                    console.error(res.data);
                }
                this.isLoading = false;
            });

        },

        /**
         * Buscar las asistencias de las fechas ingresadas
         */
        CargarPagina(index)
        {
            if (this.data_busqueda != "")
            {
                this.buscarEmp(index);
            }
            else
            {
                this.isLoading = true;
                axios.get(`rh/asistencias/buscarfechas/${this.date_one}&${this.date_two}&${index}`).then(res =>
                {
                    if (res.status)
                    {
                        this.data_response = res.data;
                        this.pagina_actual = index + 1;
                        this.headings_asistencia = ["Empleado"];
                        this.lstAsistenciaCompleta = res.data.asistencias;
                        this.total_paginas = res.data.paginas;
                        this.total = res.data.asistencias.length;
                        res.data.fechas.forEach(f =>
                        {
                            this.headings_asistencia.push(f);
                        });
                        this.total = res.data.paginas;
                    }
                    else
                    {
                        toastr.error(res.data.mensajes);
                    }
                    this.isLoading = false;
                });
            }
        },
        /**
         * Borra los datos de busqueda
         */
        BorrarBusqueda()
        {
            this.data_busqueda = "";
            this.CargarPagina(0);
        },

        /**
         * Descargar el reporte de las asistencias
         */
        GenerarReporte()
        {
            let inicio = this.$refs.date_one.value;
            let fin = this.$refs.date_two.value;
            if (inicio == "")
            {
                toastr.warning("Ingrese fecha inicial");
                return;
            }
            if (fin == "")
            {
                toastr.warning("Ingrese fecha final");
                return;
            }

            // Max 2 semanas
            let fecha1 = new Date(inicio);
            let fecha2 = new Date(fin)

            let resta = fecha2.getTime() - fecha1.getTime()
            let dias = Math.round(resta / (1000 * 60 * 60 * 24));
            if (dias > 16)
            {
                toastr.warning("Seleccione Max. 2 semanas");
                return;
            }
            window.open("rh/asistencias/reporte/" + inicio + "&" + fin + "&" + this.reporte_estado, );
            this.reporte_estado = "";

        },
    },
}
</script>

<style>
.table-main {
    border: none;
    border-right: solid 1px rgb(75, 90, 102);
    border-collapse: separate;
    border-spacing: 0;
    font: normal 13px Arial, sans-serif;
}

.table-main thead th {
    background-color: rgb(203, 220, 233);
    border: none;
    color: #336B6B;
    padding: 10px;
    text-align: left;
    text-shadow: 1px 1px 1px #fff;
    white-space: nowrap;
}

.table-main tbody td {
    border-bottom: solid 1px rgb(75, 90, 102);
    color: #333;
    padding: 10px;
    text-shadow: 1px 1px 1px #fff;
    white-space: nowrap;
}

.table {
    position: relative;
    /* border: solid 2px; */
}

.table-scroll {
    margin-left: 310px;
    overflow-x: scroll;
    overflow-y: visible;
    padding-bottom: 5px;
    /* width: 500px; */
    /* border: solid 2px; */
}

.table-main .fix-col {
    border-left: solid 1px rgb(75, 90, 102);
    /* border-right: solid 1px rgb(75, 90, 102); */
    /* border-bottom: : solid 0px */
    left: 0;
    word-break: break-all;
    position: absolute;
    top: auto;
    width: 310px;
    border-bottom: solid 0px;
}

.text-no-efect {
    font-size: 15px;
    text-shadow: none;
}

.no-autorizado {
    cursor: pointer;
}
</style>
