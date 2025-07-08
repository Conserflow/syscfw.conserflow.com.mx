<template>
<div>
    <div class="card">
        <div class="card-body">
            <v-client-table :data="list_mttos" :options="optionsmto" :columns="columnsmto" ref="tableMto">
                <template slot="tipo" slot-scope="props">
                    <button v-if="props.row.tipo==1" class="btn btn-outline-warning">Mantenimiento</button>
                    <button v-else class="btn btn-outline-success">Servicio</button>
                </template>
                <template slot="partidas" slot-scope="props">
                    <ul>
                        <li :key="index" v-for="(p,index) in props.row.partidas" style="list-style:none">
                            <span style="font-size:12px" class="badge badge-pill badge-primary">{{p.nombre}}</span>
                        </li>
                    </ul>
                </template>
            </v-client-table>
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
            id: '',
            list_mttos: [],
            columnsmto: [
                "fecha",
                "proveedor",
                "responsable",
                "partidas",
                "tipo"
            ],
            optionsmto:
            {
                headings:
                {},
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                listColumns:
                {},
                texts: config.texts
            },
        }
    },

    methods:
    {
        /**
         * Obtiene todos los mantenimientos y servicios de la unidad ingresada
         */
        ObtenerMantenimientos(id)
        {
            this.isLoading = true;
            axios.get('vehiculos/mttos/obtenermttos/' + id).then(res =>
            {
                if (res.data.status)
                {
                    this.isLoading = false;
                    this.list_mttos = res.data.mantenimientos;
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

    }
}
</script>
