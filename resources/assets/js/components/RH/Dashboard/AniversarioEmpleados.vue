<template>
<div class=''>
    <!-- Card Inicio-->
    <div class='card'>
        <!-- Inicio card-->
        <div class='card-header bg-dark text-white'>
            <i class='fa fa-align-justify'></i> Empleados - Aniversario
        </div>
        <div class='card-body'>
            <div class=''>
                <!-- Tabla de empleados-->
                <div class=''>
                    <v-client-table :columns='columns_empleados' :data='list_empleados' :options='options_empleados'>
                    </v-client-table>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
var config = require('../../Herramientas/config-vuetables-client').call(this);
export default
{
    data()
    {
        return {
            // Tabla empleados
            columns_empleados: ['nombre', "ap_paterno", "ap_materno", "fecha_ingreso", "anios"],
            list_empleados: [],
            options_empleados:
            {
                headings:
                {
                    empleado: 'Empleado',
                    estado: "Estado",
                    ap_paterno: "Apellido Paterno",
                    ap_materno: "Apellido Materno",
                    anios: "Años",
                }, // Headings,
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            },
        }
    }, //data
    methods:
    {
        /**
         * Obtiene todos los empleados
         */
        ObtenerEmpleados()
        {
            axios.get("rh/aniversario/obtenerempleados").then(res =>
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
        },
    },
    mounted()
    {
        this.ObtenerEmpleados();
    }
}
</script>
