<template>
<div class="main">
    <div class="card">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> ATENCIÓN MÉDICA - REPORTES
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist" ref="tabs">
                <li class="nav-item">
                    <a class="nav-link active" aria-expanded="true" data-toggle="tab" href="#tabPeriodo" role="tab">
                        <i class="fas fa-calendar mr-1"></i>Periodo
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabAreas" role="tab">
                        <i class="fas fa-map-marker-alt mr-1"></i>Áreas
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div id="tabPeriodo" ref="tabPeriodo" class="tab-pane active" aria-expanded="true">
                    <div class="form-group row">
                        <label class="col-md-2 form-control-label">Seleccionar</label>
                        <!-- Año -->
                        <div class="col-md-2">
                            <select class="form-control" v-model="reporte.anio">
                                <option value="0">GENERAL</option>
                                <option value="2019">2019</option>
                                <option value="2020">2020</option>
                                <option value="2021">2021</option>
                                <option value="2022">2022</option>
                            </select>
                        </div>
                        <!-- mes -->
                        <div class="col-md-2" v-show="reporte.anio!=0">
                            <select class="form-control" v-model="reporte.mes">
                                <option value="0">GENERAL</option>
                                <option value="1">ENERO</option>
                                <option value="2">FEBRERO</option>
                                <option value="3">MARZO</option>
                                <option value="4">ABRIL</option>
                                <option value="5">MAYO</option>
                                <option value="6">JUNIO</option>
                                <option value="7">JULIO</option>
                                <option value="8">AGOSTO</option>
                                <option value="9">SEPTIEMBRE</option>
                                <option value="0">OCTUBRE</option>
                                <option value="11">NOVIEMBRE</option>
                                <option value="12">DICIEMBRE</option>
                            </select>
                        </div>
                        <button class="btn btn-dark" @click="BuscarPorFecha">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <vue-element-loading :active="isGraficaPeriodo_loading" />
                    <div>
                        <barchart ref="barchart_fechas"></barchart>
                        <br>
                        <br>
                        <table class="table table-sm table-responsive table-striped table-bordered">
                            <thead class="">
                                <tr>
                                    <td class="font-weight-bold">Motivo de Atención</td>
                                    <td class="font-weight-bold">Tipo de Atención</td>
                                    <td class="font-weight-bold">No. Personal Atendido</td>
                                    <td class="font-weight-bold">Medicamentos y Material Usados</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr :key="i" v-for="(r,i) in tabla_fechas" scope="row">
                                    <td>{{r.motivo}}</td>
                                    <td>{{r.tipo}}</td>
                                    <td>{{r.n_empleados}}</td>
                                    <td>{{r.medicamentos}}</td>
                                </tr>
                                <tr>
                                    <td colspan="4"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="font-weight-bold">Total de casos atendidos</td>
                                    <td>{{n}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="tabAreas" ref="tabAreas" class="tab-pane fade">

                    <div class="form-group row">
                        <label class="col-md-2 form-control-label">Seleccionar</label>
                        <div class="col-md-4">
                            <select class="form-control" v-model="reporte.depto">
                                <option :key="i" v-for="(d,i) in list_departamentos" :value="d.id">
                                    {{d.nombre}}
                                </option>
                            </select>
                        </div>
                        <button class="btn btn-dark" @click="BuscarPorDepto">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <vue-element-loading :active="isGraficaDeptos_loading" />
                    <div>
                        <barchart ref="barchart_deptos"></barchart>
                        <br>
                        <br>
                        <table class="table table-sm table-responsive table-striped table-bordered">
                            <thead class="">
                                <tr>
                                    <td class="font-weight-bold">Motivo de Atención</td>
                                    <td class="font-weight-bold">Tipo de Atención</td>
                                    <td class="font-weight-bold">No. Personal Atendido</td>
                                    <td class="font-weight-bold">Medicamentos y Material Usados</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr :key="i" v-for="(r,i) in tabla_deptos" scope="row">
                                    <td>{{r.motivo}}</td>
                                    <td>{{r.tipo}}</td>
                                    <td>{{r.n_empleados}}</td>
                                    <td>{{r.medicamentos}}</td>
                                </tr>
                                <tr>
                                    <td colspan="4"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="font-weight-bold">Total de casos atendidos</td>
                                    <td>{{n_deptos}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
const BarChart = r => require.ensure([], () => r(require('../../Herramientas/Charts/BarChart.vue')),"enf");

export default
{
    components:
    {
        "barchart": BarChart
    },
    data()
    {
        return {
            url: "enfermeria/reportes",
            // Fechas
            tabla_fechas: [],
            n: 0,
            //
            tipo: 1,
            reporte:
            {
                mes: 1,
                anio: 2022,
            },
            isGraficaPeriodo_loading: false,
            // Ubicaciones
            list_departamentos: [],
            tabla_deptos:[],
            n_deptos:0,
            isGraficaDeptos_loading: false,

        }
    },
    methods:
    {
        // Fechas
        /**
         * Buscar datos para generar la gráfica por fecha
         */
        BuscarPorFecha()
        {
            this.isGraficaPeriodo_loading = true;
            axios.get(this.url + `/fecha/${this.reporte.anio}/${this.reporte.mes}`).then(res =>
            {
                this.isGraficaPeriodo_loading = false;
                if (res.data.status)
                {
                    this.tabla_fechas = res.data.data.tabla;
                    // Obtener datos para la grafica
                    let nombres = [];
                    let datos = [];
                    res.data.data.grafica.forEach(e =>
                    {
                        nombres.push(e.motivo);
                        datos.push(e.n_empleados);
                    });
                    let n = 0;
                    res.data.data.tabla.forEach(e =>
                    {
                        n += e.n_empleados;
                    });
                    this.n = n;
                    // Mostrar grafica
                    setTimeout(() =>
                    {
                        const el = this.$refs.tabPeriodo;

                        if (el)
                        {
                            this.$refs.barchart_fechas.Mostrar("chart-fechas", nombres, datos);
                            el.scrollIntoView(
                            {
                                behavior: 'smooth'
                            });
                        }
                    }, 50);
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Obtener los departamentos 
         */
        ObtenerDepartamentos()
        {
            axios.get(this.url + "/obtenerdptos").then(res =>
            {
                if (res.data.status)
                {
                    this.list_departamentos = res.data.departamentos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });

        },

        /**
         * Buscar las atenciones por departamentos
         */
        BuscarPorDepto()
        {
            this.isGraficaDeptos_loading = true;
            axios.get(this.url + `/departamentos/${this.reporte.depto}`).then(res =>
            {
                this.isGraficaDeptos_loading = false;
                if (res.data.status)
                {
                    this.tabla_deptos = res.data.data.tabla;
                    // Obtener datos para la grafica
                    let nombres = [];
                    let datos = [];
                    res.data.data.grafica.forEach(e =>
                    {
                        nombres.push(e.motivo);
                        datos.push(e.n_empleados);
                    });
                    let n = 0;
                    res.data.data.tabla.forEach(e =>
                    {
                        n += e.n_empleados;
                    });
                    this.n_deptos = n;
                    // Mostrar grafica
                    setTimeout(() =>
                    {
                        const el = this.$refs.tabAreas;
                        if (el)
                        {
                            this.$refs.barchart_deptos.Mostrar("chart-deptos", nombres, datos);
                            el.scrollIntoView(
                            {
                                behavior: 'smooth'
                            });
                        }
                    }, 50);
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

    },
    mounted()
    {
        this.ObtenerDepartamentos();
    }
}
</script>

<style>
.chart-sm {
    max-height: 10rem
}
</style>
