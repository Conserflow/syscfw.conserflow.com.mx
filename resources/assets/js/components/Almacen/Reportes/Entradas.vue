<template>
<main class="main">
    <div class="">

        <div class="card">
            <vue-element-loading :active="isLoading" />
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Entradas
            </div>
            <div class="card-body">
                <v-server-table :columns="columns" :url="'entrada'" :options="options" ref="myTablePrincipal">
                    <template slot="descarga" slot-scope="props">
                        <button type="button" class="btn btn-outline-dark" @click="descargarnuevo(props.row)">
                            <i class="fas fa-download"></i>&nbsp;<i class="fas fa-file-pdf"></i>
                        </button>
                    </template>
                    <template slot="condicion" slot-scope="props">
                        <template v-if="props.row.condicion == 1">
                            <button type="button" class="btn btn-outline-success">Activo</button>
                        </template>
                        <template v-if="props.row.condicion == 0">
                            <button type="button" class="btn btn-outline-danger">Dado de Baja</button>
                        </template>
                    </template>
                </v-server-table>
            </div>
        </div>
    </div>
</main>
</template>

<script>
import Utilerias from '../../Herramientas/utilerias.js';
var config = require('../../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            modales: 0,
            modal: 0,
            tituloModal: '',
            isLoading: false,
            tipoAccion: 0,
            orden_compra:
            {},
            columns: [ 'foliocompra', 'fecha', 'comentarios', 'descarga', 'condicion'],
            tableData: [],
            options:
            {
                headings:
                {
                    'id': 'Acciones',
                    'fecha': 'Fecha',
                    'foliocompra': 'Folio',
                    'comentarios': 'Comentarios',
                    'formato_entrada': 'Formato de entrada',
                    'condicion': 'Condición',
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts,
                requestAdapter: function (data)
                {
                    var arr = [];
                    // console.log(data);
                    arr.push(
                    {
                        'OC.folio': data.query.foliocompra,
                        'fecha': data.query.fecha,
                        'comentarios': data.query.comentarios,
                    });
                    data.query = arr[0];
                    return data;
                },
            },
            entrada:
            {
                id: 0,
                fecha: '',
                comentarios: '',
                condicion: 0,
                disabled: true,
                orden_compra_id: '',
            },
            empleado_responsable: 0,
            disabled: 0,
            listaOC: [],
            listaAlmacenes: [],

        }
    },
    methods:
    {
        /**
         * 
         * @return {Response} [se obtiene la]
         */
        fecha(fecha)
        {
            const meses = [
                "Enero", "Febrero", "Marzo",
                "Abril", "Mayo", "Junio", "Julio",
                "Agosto", "Septiembre", "Octubre",
                "Noviembre", "Diciembre"
            ];

            const date = new Date(fecha);
            const dia = date.getDate() + 1;
            const mes = date.getMonth();
            const ano = date.getFullYear();
            return `${dia} de ${meses[mes]} del ${ano}`;
        },

        descargar(data)
        {
            window.open('descargar-entrada/' + data.id, '_blank');
        },

        descargarnuevo(data)
        {
            window.open('descargar-entrada-nuevo-formato/' + data.id, '_blank');
        },
    },

    mounted()
    {}
}
</script>
