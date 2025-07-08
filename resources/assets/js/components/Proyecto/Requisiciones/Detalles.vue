<template>
<main class="main">
    <div class="card">
        <div class="card-header">
            <back-button />
            <span class="h6">{{folio}} - {{tipo}}</span>
        </div>
        <div class="card-body">
            <vue-element-loading :active="loading_obtenerPartidas.enable" />
            <v-client-table :columns="columns_partidas" :data="list_partidas" :options="options_partidas">
                <template v-slot:cantidad_almacen="props">
                    <div style="display:flex">
                        <input type="number" class="form-control cantidad_almacen" placeholder="0" v-model="props.row.cantidad_almacen" min="0" :max="props.row.cantidad">
                        <button class="ml-1 btn-sm btn btn-success" @click="actualizarCantidadAlmacen(props.row)">
                            <i class="fas fa-save"></i>
                        </button>
                    </div>
                </template>
            </v-client-table>
        </div>
    </div>
</main>
</template>

<script>
import
{
    config
}
from "../../../utils/vue-tables";
import
{
    error,
    get,
    postPut,
}
from "../../../utils/utils";
import ButtonHeader from '../../Shared/card/button-header.vue';
export default
{
    name: "proyectos-requisiciones-detalles",
    components:
    {
        ButtonHeader,
    },
    data()
    {
        return {
            requi_id: 0,
            folio: "",
            tipo: "",
            loading_obtenerPartidas:
            {
                enable: false,
            },
            url: "requisiciones/proyectos",
            columns_partidas: [
                "concepto",
                "marca",
                "unidad_medida.nombre",
                "cantidad",
                "cantidad_almacen",
            ],
            list_partidas: [],
            options_partidas:
            {
                ...config.options,
                headings:
                {
                    id: "Acciones",
                    "unidad_medida.nombre": "Unidad",
                    cantidad: "Cant. Requerida",
                    cantidad_almacen: "Cant. Almcén",
                }
            },
        }
    },
    methods:
    {

        /**
         * Cargar información inicial
         */
        cargarInicial()
        {
            get(`requisiciones/requisicion/${this.requi_id}`, null, null, (res) =>
            {
                this.folio = res.data.info.folio;
                this.tipo = res.data.info.tipo.nombre;
            });
        },

        /**
         * Obtener las partidas de la requi actual
         */
        obtenerPartidas()
        {
            get(`${this.url}/${this.requi_id}`, null, this.loading_obtenerPartidas, (res) =>
            {
                this.list_partidas = res.data.info.partidas;
            });
        },

        /**
         * Actualizar la cantidad de almacen
         */
        actualizarCantidadAlmacen(partida)
        {
            const valido = (value, max) =>
            {
                const regex = /^(?:0|[1-9]\d*)(?:\.\d+)?$/;
                const valido = regex.test(value);
                return valido && (parseFloat(value) <= max) && (parseFloat(value) >= 0);
            }

            if (!valido(partida.cantidad_almacen, partida.cantidad))
            {
                error("Ingrese una cantidad válida");
                return;
            }
            postPut(this.url,
            {
                cantidad: partida.cantidad_almacen
            }, partida.id, "Cantidad actualizada correctamente", this.loading_obtenerPartidas, (res) =>
            {
                this.obtenerPartidas();
            });
        }
    },
    mounted()
    {
        this.requi_id = this.$route.params.id;
        this.cargarInicial();
        this.obtenerPartidas();
    },
}
</script>

<style scoped>
input[type='number']::-webkit-inner-spin-button,
input[type='number']::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Para Firefox */
input[type='number'] {
    -moz-appearance: textfield;
}
</style>
