<template>
<main class="main">
    <div class="card">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> REQUISICIONES POR REVISAR
        </div>
        <div class="card-body">
            <vue-element-loading :active="loading_guardarRequisicion.enable" />
            <v-server-table ref="tblRequisiciones" :columns="columns_requisiciones" :url="this.url+'?query={}&limit=10&ascending=0&page=1&byColumn=1&orderBy=requisiciones2__folio'" :options="options_requisiciones">
                <template v-slot:id="props">
                    <actions>
                        <template #options>
                            <action title="Detalles" icon="fas fa-list" @click="verDetalles(props.row)" />
                            <action title="Aprobar" icon="fas fa-check" @click="aprobar(props.row.id)" />
                            <action title="Rechazar" icon="fas fa-times" @click="rechazar(props.row.id)" />
                        </template>
                    </actions>
                </template>
            </v-server-table>
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
    getText,
    postPut,
    question,
}
from "../../../utils/utils";
import Utilerias from '../../../components/Herramientas/utilerias.js';

export default
{
    name: "requisiciones-main",
    components:
    {},
    data()
    {
        return {
            permisosCRUD:
            {},
            empleado_actual:
            {},
            url: "requisiciones/almacen",
            columns_requisiciones: [
                "id",
                "proyecto.nombre_corto",
                "requisiciones2__folio",
                "tipo.nombre",
                "fecha_emision",
                "solicita.raw_nom_solicita",
                "aprueba.raw_nom_solicita",
            ],
            options_requisiciones:
            {
                ...config.options,
                headings:
                {
                    "id": "Acciones",
                    "requisiciones2__folio": "Folio",
                    "proyecto.nombre_corto": "Proyecto",
                    "solicita.raw_nom_solicita": "Solicita",
                    "aprueba.raw_nom_solicita": "Aprueba",
                    "tipo.nombre": "Tipo",
                    "fecha_emision": "Fecha",
                },
                sortable: [
                    "id",
                    "proyecto.nombre_corto",
                    "requisiciones2__folio",
                    "tipo.nombre",
                    "fecha_emision",
                    "aprueba.raw_nom_solicita",
                    "solicita.raw_nom_solicita",
                ],
                filterable: [
                    "id",
                    "proyecto.nombre_corto",
                    "requisiciones2__folio",
                    "tipo.nombre",
                    "fecha_emision",
                    "solicita.raw_nom_solicita",
                    "aprueba.raw_nom_solicita",
                ],
            },

            loading_guardarRequisicion:
            {
                enable: false,
            },
        }
    },
    methods:
    {
        /**
         * Obtener las requisiciones registradas
         */
        obtenerRequisiciones()
        {
            this.$refs.tblRequisiciones.refresh();
        },

        /**
         * Abrir ventana de detalles
         */
        verDetalles(row)
        {
            this.$router.push(
            {
                name: "almacen-requisiciones-detalles",
                params:
                {
                    id: row.id,
                },
            });
        },

        /**
         * Rechazar la requisición
         */
        async rechazar(id)
        {
            const res_motivo = await getText("Ingrese el motivo de rechazo");
            if (!res_motivo.isConfirmed) return;
            const motivo = res_motivo.value.trim();
            if (motivo == "") return;

            const data = {
                id,
                motivo
            };

            postPut(`${this.url}/rechazar`, data, null, "Requisición rechazada correctamente",
                this.loading_guardarRequisicion, () =>
                {
                    this.obtenerRequisiciones();
                });
        },

        /**
         * Aprobar la requisición
         */
        async aprobar(id)
        {
            const res = await question("¿Desea aproba la requisición?", "Se aprobara la requisición");
            if (!res.isConfirmed) return;

            const data = {
                id
            };

            postPut(`${this.url}/aprobar`, data, null, "Requisición aprobada correctamente",
                this.loading_guardarRequisicion, () =>
                {
                    this.obtenerRequisiciones();
                });
        },
    },
    mounted()
    {
        this.permisosCRUD = Utilerias.getCRUD(this.$route.path);
    },
}
</script>
