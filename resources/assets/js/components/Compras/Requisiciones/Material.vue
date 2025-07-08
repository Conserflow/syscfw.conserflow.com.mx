<template>
<main class="main">
    <div class="card">
        <div class="card-header">
            <back-button />
            <span class="h6">{{folio}} - {{tipo}}</span>
            <button-header title="Nuevo" icon="fas fa-plus" @click="abrirModalPartidas" />
        </div>
        <div class="card-body">
            <vue-element-loading :active="loading_obtenerPartidas.enable" />
            <v-client-table :columns="columns_partidas" :data="list_partidas" :options="options_partidas">
                <template v-slot:id="props">
                    <actions>
                        <template #options>
                            <action title="Actualizar" icon="fas fa-pen" @click="abrirModalPartidas(false,props.row)" />
                            <action title="Eliminar" icon="fas fa-trash" @click="eliminarPartida(props.row.id)" />
                        </template>
                    </actions>
                </template>
            </v-client-table>
        </div>
    </div>

    <!-- Modal Partidas -->
    <modal id="partidas" :header="titulo_modal_partida" :mostrar="ver_modal_partidas" @cerrar="cerrarModalPartidas" :lg="true">
        <template #body>
            <vue-element-loading :active="loading_guardarPartidas.enable" />
            <form ref="frmPartidas" autocomplete="false">
                <div class="form-group">
                    <label>Concepto</label>
                    <textarea v-model="partida.concepto" name="concepto" class="form-control" maxlength="250" rows="3" v-validate="'required'">
                    </textarea>
                    <span class="text-danger">{{errors.first("concepto")}}</span>
                </div>
                <div class="form-group">
                    <label>Comentarios</label>
                    <textarea name="comentarios" class="form-control" v-model="partida.comentarios" maxlength="250" rows="3" />
            </div>
            <div class="form-row mb-2">
                <div class="col">
                    <label>Marca</label>
                    <input type="text" name="marca" v-model="partida.marca" class="form-control"  v-validate="'required'" />
                    <span class="text-danger">{{errors.first("marca")}}</span>
                </div>
                <div class="col">
                    <label>Tipo</label>
                    <select class="form-control" name="tipo" v-validate="'required'" @change="cambiarTipo" v-model="partida.tipo">
                        <option value="1">Artículo</option>
                        <option value="2">Servicio</option>
                    </select>
                    <span class="text-danger">{{errors.first("tipo")}}</span>
                </div>
            </div>
            <div class="form-row mb-2">
                <div class="col">
                    <label>Cantidad</label>
                    <input name="cantidad" v-model="partida.cantidad" class="form-control" type="number" v-validate="'required'" min="1">
                    <span class="text-danger">{{errors.first("cantidad")}}</span>
                </div>
                <div class="col">
                    <label>Unidad</label>
                    <v-select :disabled="partida.tipo==2" :options="list_unidades_medida" label="nombre" name="unidad" :v-validate="'required'" v-model="partida.unidad_medida" />
                    <span class="text-danger">{{errors.first("unidad")}}</span>
                </div>
            </div>
            <div class="form-group">
                <label>Documentos requeridos</label>
                    <textarea v-model="partida.documentos_requeridos" class="form-control" maxlength="250" rows="3" name="documentos" />
                    <span class="text-danger">{{errors.first("documentos")}}</span>
            </div>
        </form>
    </template>
    <template #footer>
        <vue-element-loading :active="loading_guardarPartidas.enable" />
        <button class="btn btn-dark" @click="guardarPartidas"><i class="fas fa-save"></i> Guardar</button>
        <button class="btn btn-secondary" @click="cerrarModalPartidas"><i class="fas fa-times"></i> Cerrar</button>
    </template>
</modal>
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
    del,
    get,
    postPut,
    question,
    validateSelect,
}
from "../../../utils/utils";
import ButtonHeader from '../../Shared/card/button-header.vue';
export default
{
    name: "requisiciones-materiales",
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
            url: "requisiciones/materiales",
            columns_partidas: [
                "id",
                "concepto",
                "marca",
                "cantidad",
                "unidad_medida.nombre",
                "comentarios",
            ],
            list_partidas: [],
            options_partidas:
            {
                ...config.options,
                headings:
                {
                    id: "Acciones",
                    "unidad_medida.nombre": "Unidad",
                }
            },

            // Modal
            list_unidades_medida: [],
            ver_modal_partidas: false,
            partida:
            {
                id: 0,
                comentarios: "-",
                marca: "N/A",
                documentos_requeridos: "N/A",
                unidad_medida:
                {},
                tipo: 1,
                concepto: "",
            },
            loading_guardarPartidas:
            {
                enable: false
            },
            loading_obtenerPartidas:
            {
                enable: false
            },
            titulo_modal_partida: "",
            // Conceptos
            ver_modal_conceptos: false,

            // Documentos Requeridos
            list_docs_requeridos: [],
            ver_modal_documentos_requeridos: false,
            loading_obtener_documentos_requeridos:
            {
                enable: false,
            }
        }
    },
    methods:
    {
        /**
         * Obtener las partidas de la requi actual
         */
        obtenerPartidas()
        {
            get(`${this.url}/partidas/${this.requi_id}`, null, this.obtenerPartidas, (res) =>
            {
                this.list_partidas = res.data.partidas;
            });
        },

        /**
         * Cargar información inicial
         */
        cargarInicial()
        {
            get("requisiciones/unidadesmedida", null, null, (res) =>
            {
                this.list_unidades_medida = res.data.unidades_medida;
            });

            get(`requisiciones/requisicion/${this.requi_id}`, null, null, (res) =>
            {
                this.folio = res.data.info.folio;
                this.tipo = res.data.info.tipo.nombre;
            });
        },

        /**
         * Abrir modal de partidas
         */
        abrirModalPartidas(nuevo = true, data = {})
        {
            this.ver_modal_partidas = true;
            if (nuevo)
            {
                this.nuevo = true;
                this.titulo_modal_partida = "Registrar partida";
            }
            else
            {
                this.titulo_modal_partida = "Actualizar partida";
                this.partida = {
                    ...data,
                    unidad_medida:
                    {
                        id: data.unidad_medida.id,
                        nombre: data.unidad_medida.nombre
                    }
                };
            }
        },

        /**
         * Indicar si la unidad es de tipo servicio o articulo
         */
        cambiarTipo()
        {
            if (this.partida.tipo == 1) // Articulo
            {
                this.partida.unidad_medida = {
                    id: 2,
                    nombre: "H87 - PIEZA"
                }
            }
            else // Servicio
            {
                this.partida.unidad_medida = {
                    id: 1,
                    nombre: "E48 - SERVICIO"
                }
            }
        },

        /**
         * Cerrar modal de partida
         */
        cerrarModalPartidas()
        {
            this.ver_modal_partidas = false;
            this.$validator.reset();
            this.partida = {
                tipo: 1,
                concepto: "",
                marca: "N/D",
                comentarios: "-",
                documentos_requeridos: "N/A",
                unidad_medida:
                {}
            };
        },

        /**
         * Guardar 
         */
        async guardarPartidas()
        {
            if (!validateSelect(this.partida.unidad_medida)) return;
            let isValid = await this.$validator.validate()
            if (!isValid) return;

            let data = {
                requi_id: this.requi_id,
                concepto: this.partida.concepto,
                marca: this.partida.marca,
                documentos_requeridos: this.partida.documentos_requeridos,
                comentarios: this.partida.comentarios,
                cantidad: this.partida.cantidad,
                tipo: this.partida.tipo,
                um_id: this.partida.unidad_medida.id,
            }

            postPut(this.url, data, this.partida.id, "Partida guardada correctamente",
                this.loading_guardarPartidas, () =>
                {
                    this.cerrarModalPartidas();
                    this.obtenerPartidas();
                })
        },

        /**
         * Eliminar la partida seleccionada
         */
        async eliminarPartida(id)
        {
            let res = await question("Eliminar partida",
                "¿Desea eliminar la partida seleccionada?", "warning");
            if (!res.isConfirmed) return;
            del(this.url, id, "Eliminado correctamente", this.loading_obtenerPartidas, () =>
            {
                this.obtenerPartidas();
            });
        },
    },
    mounted()
    {
        this.requi_id = this.$route.params.id;
        this.obtenerPartidas();
        this.cargarInicial();
    },
}
</script>
