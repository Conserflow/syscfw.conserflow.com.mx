<template>
<main class="main">
    <div class="container-fluid1">

        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Vales De Equipo
                <button v-if="PermisosCRUD.Download" type="button" @click="DescargarReporte()" class="btn btn-dark float-sm-right btn btn-sm">
                    <i class="fas fa-download mr-1"></i>Descargar
                </button>
            </div>
            <div class="card-body1">

                <ul class="nav nav-tabs" role="tablist" ref="tabs">
                    <li class="nav-item">
                        <a class="nav-link active" aria-expanded="true" data-toggle="tab" href="#menu1" role="tab" @click="CambiarTab(1)"><i class="fas fa-user-shield"></i>&nbsp;Resguardo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#menu2" role="tab" @click="CambiarTab(2)"><i class="fas fa-map-marker-alt"></i>&nbsp;Salida</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div id="menu1" class="tab-pane active" aria-expanded="true" v-show="tab == 1">
                        <div class="form-row">
                            <div class="col-md-8">
                                <h4>Resguardo</h4>
                            </div>
                            <div class="col-md-4">
                                <button v-if="PermisosCRUD.Create" type="button" @click="AbrirModalResguardo()" class="btn btn-dark float-sm-right">
                                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                                </button>
                                <div class="dropdown">

                                    <button class="btn btn-secondary dropdown-toggle float-sm-right" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Empresa
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                        <button class="dropdown-item" type="button" @click="empresa = 1;ObtenerResguardos()">Conserflow</button>
                                        <button class="dropdown-item" type="button" @click="empresa = 2;ObtenerResguardos()">CSCT</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <resguardo ref="resguardo"></resguardo>
                    </div>
                    <div id="menu2" class="tab-pane fade" v-show="tab == 2">
                        <div class="form-row">
                            <div class="col-md-8">
                                <h4>Sitio</h4>
                            </div>
                            <div class="col-md-4">
                                <button type="button" @click="AbrirModalSitio()" class="btn btn-dark float-sm-right">
                                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                                </button>
                                <div class="dropdown">

                                    <button class="btn btn-secondary dropdown-toggle float-sm-right" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Empresa
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                        <button class="dropdown-item" type="button" @click="empresa = 1;ObtenerSitios()">Conserflow</button>
                                        <button class="dropdown-item" type="button" @click="empresa = 2;ObtenerSitios()">CSCT</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <sitio ref="sitio"></sitio>
                    </div>
                </div>

            </div>
        </div>

    </div>
</main>
</template>

<script>
import Utilerias from '../../Herramientas/utilerias.js';
const Resguardo = r => require.ensure([], () => r(require('./Resguardo.vue')), 'ti');
const Sitio = r => require.ensure([], () => r(require('./Sitio.vue')), 'ti');

export default
{
    data()
    {
        return {
            tab: 1,
            empresa: 1,
            PermisosCRUD:
            {},
        }
    },
    components:
    {
        'resguardo': Resguardo,
        'sitio': Sitio,
    },
    methods:
    {
        /**
         * Cambia el tab
         */
        CambiarTab(id)
        {
            this.tab = id;
        },

        /**
         * Abrir modal para registro de resguardo
         */
        AbrirModalResguardo()
        {
            var childResguardo = this.$refs.resguardo;
            childResguardo.AbrirModal(true);
        },

        /**
         * Abrir modal para registro de sitio
         */
        AbrirModalSitio()
        {
            var childSitio = this.$refs.sitio;
            childSitio.AbrirModal(true);
        },

        /**
         * Cargargar los vales de resguardo
         */
        ObtenerResguardos()
        {
            var childResguardo1 = this.$refs.resguardo;
            childResguardo1.CargarDatos(this.empresa);
        },

        /**
         * Cargargar los vales de sitio
         */
        ObtenerSitios()
        {
            var ChildSitio = this.$refs.sitio;
            ChildSitio.CargarDatos(this.empresa);
        },
        CargarInicio()
        {
            this.CambiarTab(1);
        },

        /**
         * Descargar todos los resguardos registrados
         */
        DescargarReporte()
        {
            window.open("ti/vales/descargar");
        }
    },
    mounted()
    {
        this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
        this.CargarInicio()
    }
}
</script>
