<template>
<main class="main">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Datos de empleados
            </div>
            <vue-element-loading :active="isObtenerLoading"/>
            <div class="card-body">
                <v-client-table :data="list_empleados" :options="options_empleados" :columns="columns_empleados">
                </v-client-table>
            </div>
        </div>

    </div>
</main>
</template>

<script>
var config = require("../Herramientas/config-vuetables-client").call(this);

export default
{
    data()
    {
        return {
            list_empleados: [],
            url: "enfermeria/empleados",
            isObtenerLoading: false,
            columns_empleados: ["nombre", "puesto", "nss_imss","curp","rfc"],
            options_empleados:
            {
                headings:
                {
                    nss_imss:"NSS",
                    rfc:"RFC",
                    curp:"CURP",
                },
                perPage: 30,
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
        ObtenerEmpleados()
        {
            this.isObtenerLoading = true;
            axios.get(this.url + "/obtener").then(res =>
            {
                if (res.data.status)
                {
                    this.list_empleados = res.data.empleados;
                    this.isObtenerLoading = false;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        }
    },
    mounted()
    {
        this.ObtenerEmpleados();
    },
}
</script>
