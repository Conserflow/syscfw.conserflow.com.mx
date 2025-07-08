<template>
<div>
    <button class="btn btn-sm btn-secondary float-sm-right mb-1" @click="Recargar">
        <i class="fas fa-sync mr-1"></i> Actualizar
    </button>
    <v-server-table ref="tblHerramientas" :columns="columns_herramientas" url="almacen/herramientas/obtenerserver?query={}&limit=10&ascending=1&page=1&byColumn=1" :options="options_herramientas">
        <template slot="formato_entrada" slot-scope="props">
            <button class="btn btn-outline-dark mr-2" @click="DescargarEntrada(props.row.e__id)">
                <i class="fas fa-download mr-1"></i>
            </button>{{props.row.formato_entrada}}
        </template>
    </v-server-table>
</div>
</template>

<script>
var config = require('../../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            // Catalogo
            columns_herramientas: ["formato_entrada", "a__id", "nombre", "descripcion", "no_serie", "ah__marca", "modelo"],
            options_herramientas:
            {
                headings:
                {
                    formato_entrada: "Entrada",
                    a__id: "Codigo",
                    ah__marca: "Marca",
                    disponible: "Condicion"
                },
                perPage: 15,
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
        DescargarEntrada(id)
        {
            window.open(`descargar-entrada-nuevo-formato/${id}`);
        },
        Recargar()
        {
            this.$refs.tblHerramientas.refresh();
        }
    },
    mounted()
    {}

}
</script>

<style scoped>
</style>
