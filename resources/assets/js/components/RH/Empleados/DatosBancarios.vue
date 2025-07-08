<template>
<main class="main">
    <div class="container-fluid">
        <div class="card" v-show="tipo_card==1">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Datos Bancarios
                <button v-show="PermisosCRUD.Download" type="button" @click="Descargar" class="btn btn-sm btn-dark float-sm-right">
                    <i class="fas fa-download mr-1"></i>Descargar
                </button>
            </div>
            <div class="card-body">
                <vue-element-loading :active="isObtenerEmpleados_loading" />
                <v-client-table :columns="columns_empleados" :data="list_empleados" :options="options_empleados">
                </v-client-table>
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
            PermisosCRUD:
            {},
            isObtenerEmpleados_loading: false,
            tipo_card: 1,
            url: "rh/empleados/datosbancarios",
            columns_empleados: [
                "nombre",
                "ap_paterno",
                "ap_materno",
                "banco",
                "cuenta",
                "clabe",
                "tarjeta",
            ],
            list_empleados: [],
            options_empleados:
            {
                headings:
                {
                    "nombre": "Nombre",
                    "ap_paterno": "Apellido P.",
                    "ap_materno": "Apellido M.",
                },
                perPage: 20,
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
        ObtenerEmpleados()
        {
            this.isObtenerEmpleados_loading = true;
            axios.get(this.url + "/obtener").then(res =>
            {
                this.isObtenerEmpleados_loading = false;
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
        Descargar()
        {
            window.open(this.url + "/descargar")
        }
    },
    mounted()
    {
        this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
        this.ObtenerEmpleados();
    }
}
</script>
