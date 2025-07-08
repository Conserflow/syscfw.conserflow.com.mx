<template>
<main class="main">
    <div class="card">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> REQUISICIONES
            <button-header v-if="permisosCRUD.Create" title="Nuevo" icon="fas fa-plus" @click="abrirModalRequisiciones" />
        </div>
        <div class="card-body">
            <vue-element-loading :active="loading_guardarRequisicion.enable" />
            <v-server-table ref="tblRequisiciones" :columns="columns_requisiciones" :url="this.url+'/requisicion?query={}&limit=10&ascending=0&page=1&byColumn=1&orderBy=requisiciones2__folio'" :options="options_requisiciones">
                <template v-slot:requisiciones2__id="props">
                    <template v-if="[1,3].includes(props.row.condicion)">
                        <actions v-if="verAcciones(props.row)">
                            <template #options>
                                <action v-if="permisosCRUD.Read" title="Detalles" icon="fas fa-list" @click="verDetalles(props.row)" />
                                <action v-if="permisosCRUD.Update" title="Actualizar" icon="fas fa-pen" @click="abrirModalRequisiciones(false,props.row)" />
                                <action v-if="permisosCRUD.Create" title="Cerrar" icon="fas fa-paper-plane" @click="cerrarRequisicion(props.row)" />
                                <action v-if="permisosCRUD.Delete" title="Eliminar" icon="fas fa-times" @click="eliminarRequisicion(props.row.id)" />
                            </template>
                        </actions>
                    </template>
                </template>
                <template v-slot:descargar="props">
                    <template v-if="[2,4].includes(props.row.condicion)">
                        <button class="btn btn-outline-dark" @click="descargarRequisicion(props.row)">
                            <i class="fas fa-download"></i>
                        </button>
                    </template>
                </template>
                <template v-slot:condicion="props">
                    <template v-if="props.row.condicion==0">
                        <button class="btn btn-outline-danger" @click="verMotivo(props.row.motivo_eliminacion,'eliminación')">Eliminado</button>
                    </template>
                    <template v-if="props.row.condicion==1">
                        <button class="btn btn-outline-success">Nuevo</button>
                    </template>
                    <template v-if="props.row.condicion==2">
                        <button class="btn btn-outline-warning">En revisión por almacén</button>
                    </template>
                    <template v-if="props.row.condicion==3">
                        <button class="btn btn-outline-danger" @click="verMotivo(props.row.motivo_rechazo,'rechazo')">Rechazado por Almacén</button>
                    </template>
                    <template v-if="props.row.condicion==4">
                        <button class="btn btn-outline-info">En revisión por Supervisor</button>
                    </template>
                    <template v-if="props.row.condicion==5">
                        <button class="btn btn-outline-danger">Rechazado por Supervisor</button>
                    </template>
                    <template v-if="props.row.condicion==6">
                        <button class="btn btn-outline-success">En Compras</button>
                    </template>
                </template>
            </v-server-table>
        </div>
    </div>

    <!-- Modal crear requisiciones -->
    <modal :header="titulo_modal_requisiciones" :lg="true" :mostrar="ver_modal_requisiciones" @cerrar="cerrarModalRequisicion">
        <template #body>
            <vue-element-loading :active="loading_guardarRequisicion.enable" />
            <form autocomplete="false">
                <div class="form-row mb-2" v-if="!nuevo">
                    <div class="col">
                        <label>Folio</label>
                        <input disabled name="folio" type="text" class="form-control" v-model="requisicion.folio" />
                    </div>
                </div>
                <div class="form-group">
                    <label>Proyecto</label>
                    <v-select :disabled="!nuevo" name="proyecto" :options="list_proyectos" label="nombre_corto" v-validate="'required'" v-model="requisicion.proyecto" />
                    <span class="text-danger">{{errors.first("proyecto")}}</span>
                </div>
                <div class="form-row mb-2">
                    <div class="col">
                        <label>Área Solicitante</label>
                        <v-select :options="list_areas" label="nombre" name="area" v-validate="'required'" v-model="requisicion.area" />
                        <span class="text-danger">{{errors.first("area")}}</span>

                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="col">
                        <label>Tipo</label>
                        <v-select :disabled="!nuevo" :options="list_tipos_requisicion" label="nombre" name="tipo" v-validate="'required'" v-model="requisicion.tipo" />
                        <span class="text-danger">{{errors.first("tipo")}}</span>
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="col">
                        <label>Fecha de entrega requerida</label>
                        <date-picker v-model="requisicion.fecha_entrega" name="fecha_entrega" data-vv-as="Fecha de entrega" v-validate="'required'" :language="language_es" :disabled-dates="disabledDates"></date-picker>
                        <span class="text-danger">{{errors.first("fecha_entrega")}}</span>
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="col">
                        <label>Lugar de entrega</label>
                        <select name="lugar_entrega" v-model="requisicion.lugar_entrega" class="form-control" v-validate="'required'">
                            <option value="ALMACEN TEHUACAN">ALMACÉN TEHUACÁN</option>
                            <option value="ALMACEN COATZACOALCOS">ALMACÉN COATZACOALCOS</option>
                            <option value="SITIO">SITIO</option>
                        </select>
                        <span class="text-danger">{{errors.first("lugar_entrega")}}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Notas Adicionales</label>
                    <textarea name="notas" v-validate="'required'" v-model="requisicion.notas" class="form-control" rows="5"></textarea>
                    <span class="text-danger">{{errors.first("notas")}}</span>
                </div>
                <div class="form-group">
                    <label>Aprueba</label>
                    <v-select name="aprueba" v-model="requisicion.aprueba" :options="list_personal_aprueba" label="nombre" v-validate="'required'" />
                    <span class="text-danger">{{errors.first("aprueba")}}</span>
                </div>
                <div style="height: 3rem"></div>
            </form>
        </template>
        <template #footer>
            <vue-element-loading :active="loading_guardarRequisicion.enable" />
            <button class="btn btn-dark" @click="guardarRequisicion"><i class="fas fa-save"></i> Guardar</button>
            <button class="btn btn-secondary" @click="cerrarModalRequisicion"><i class="fas fa-times"></i> Cerrar</button>
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
    get,
    getText,
    info,
    postPut,
    question,
    validateSelect,
}
from "../../../utils/utils";
import Utilerias from '../../../components/Herramientas/utilerias.js';
import Datepicker from 'vuejs-datepicker';
import moment from 'moment';
import
{
    es
}
from 'vuejs-datepicker/dist/locale/index';

export default
{
    name: "requisiciones-main",
    components:
    {
        "date-picker": Datepicker
    },
    data()
    {
        return {
            permisosCRUD:
            {},
            empleado_actual:
            {},
            url: "requisiciones",
            columns_requisiciones: [
                "requisiciones2__id",
                "proyecto.nombre_corto",
                "requisiciones2__folio",
                "tipo.nombre",
                "fecha_emision",
                "solicita.raw_nom_solicita",
                "aprueba.raw_nom_solicita",
                "descargar",
                "condicion",
            ],
            list_requisiciones: [],
            options_requisiciones:
            {
                ...config.options,
                headings:
                {
                    "requisiciones2__id": "Acciones",
                    "requisiciones2__folio": "Folio",
                    "proyecto.nombre_corto": "Proyecto",
                    "solicita.raw_nom_solicita": "Solicita",
                    "aprueba.raw_nom_solicita": "Aprueba",
                    "tipo.nombre": "Tipo",
                    "fecha_emision": "Fecha",
                    "condicion": "Estado",
                },
                sortable: [
                    "requisiciones2__id",
                    "proyecto.nombre_corto",
                    "requisiciones2__folio",
                    "tipo.nombre",
                    "fecha_emision",
                    "aprueba.raw_nom_solicita",
                    "solicita.raw_nom_solicita",
                ],
                filterable: [
                    "requisiciones2__id",
                    "proyecto.nombre_corto",
                    "requisiciones2__folio",
                    "tipo.nombre",
                    "fecha_emision",
                    "solicita.raw_nom_solicita",
                    "aprueba.raw_nom_solicita",
                ],
            },

            // Modal
            nuevo: false,
            titulo_modal_requisiciones: "",
            ver_modal_requisiciones: false,
            language_es: es,
            solicita:
            {},
            requisicion:
            {
                proyecto:
                {},
                area:
                {},
                tipo:
                {},
                solicita:
                {},
                tipo:
                {},
                aprueba:
                {},
                lugar_entrega: "ALMACEN TEHUACAN",
                fecha_entrega: "",
                notas: "-",
            },
            disabledDates:
            {},
            list_empleados: [],
            list_areas: [],
            list_proyectos: [],
            list_personal_aprueba: [],
            list_tipos_requisicion: [],
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
         * Abrir modal para la requsicion
         */
        abrirModalRequisiciones(nuevo = true, data = {})
        {
            this.ver_modal_requisiciones = true;
            this.$validator.reset();
            if (nuevo)
            {
                this.nuevo = true;
                this.titulo_modal_requisiciones = "Registrar requisición";
            }
            else
            {
                this.titulo_modal_requisiciones = "Actualizar requisición";
                this.nuevo = false;
                this.requisicion = {
                    ...data,
                    tipo:
                    {
                        id: data.tipo.id,
                        nombre: data.tipo.nombre,
                    },
                    aprueba:
                    {
                        id: data.aprueba.id,
                        nombre: data.aprueba.raw_nom_solicita,
                    },
                };
            }
        },

        /**
         * Cerrar modal
         */
        cerrarModalRequisicion()
        {
            this.ver_modal_requisiciones = false;
            this.$validator.reset();
            this.requisicion = {
                notas: "-",
            };
        },

        /**
         * Guardar requisicion
         */
        async guardarRequisicion()
        {
            if (!validateSelect([
                    this.requisicion.proyecto,
                    this.requisicion.area,
                    this.requisicion.tipo,
                    this.requisicion.aprueba,
                ])) return;

            const isValid = await this.$validator.validate();

            if (!isValid) return;

            let data = {
                "proyecto_id": this.requisicion.proyecto.id,
                "tipo_id": this.requisicion.tipo.id,
                "area_id": this.requisicion.area.id,
                "lugar_entrega": this.requisicion.lugar_entrega,
                "fecha_entrega": moment(this.requisicion.fecha_entrega).format('YYYY/MM/DD'),
                "notas": this.requisicion.notas,
                "empleado_aprueba_id": this.requisicion.aprueba.id,
            };

            let mensaje = "Requisición " + (this.requisicion.id == null ? "registrada" : "actualizada") +
                " correctamente";

            postPut(`${this.url}/requisicion`, data, this.requisicion.id, mensaje,
                this.loading_guardarRequisicion, (res) =>
                {
                    this.obtenerRequisiciones();
                    // Abrir detalles cuando se crea una nueva requi
                    if (this.requisicion.id == null)
                    {
                        this.verDetalles(res.data.requisicion);
                    }
                    this.cerrarModalRequisicion();
                });
        },

        /**
         * Eliminar la requisicion seleccionada
         */
        async eliminarRequisicion(id)
        {
            let mensaje = "Requisición eliminada correctamente";
            let res = await question("¿Eliminar requisición?", "Esta acción no se podrá deshacer")
            if (!res.isConfirmed) return;

            const res_motivo = await getText("Ingrese el motivo de la eliminación");
            const motivo = res_motivo.value.trim();
            if (motivo == "") return;

            const data = {
                id,
                motivo
            };

            postPut(`${this.url}/requisicion/eliminar`, data, null, mensaje,
                this.loading_guardarRequisicion, () =>
                {
                    this.obtenerRequisiciones();
                })
        },

        /**
         * Abrir ventana de detalles
         */
        verDetalles(row)
        {
            this.$router.push(
            {
                name: row.tipo.ruta,
                params:
                {
                    id: row.id,
                },
            });
        },

        /**
         * Descargar el documento de la requisicion
         */
        descargarRequisicion(row)
        {
            window.open(`${this.url}/descargar/${row.id}`, "_blank");
        },

        /**
         * Cargar información inicial
         */
        cargarInicial()
        {
            get("generales/empleadoactivos", null, null, (res) =>
                this.list_empleados = res.data.empleados);
            get("generales/proyectos/1", null, null, (res) =>
                this.list_proyectos = res.data.proyectos);
            get(`${this.url}/tipos`, null, null, (res) =>
                this.list_tipos_requisicion = res.data.tipos);
            get("requisiciones/personalaprueba", null, null, (res) =>
                this.list_personal_aprueba = res.data.empleados);
            get("salidassgi/salidanc/departamentos", null, null, (res) =>
                this.list_areas = res.data.departamentos);
            get("generales/empleadoactual", null, null, (res) =>
                this.empleado_actual = res.data.empleados[0]);

            // Fecha minima
            const min = new Date();
            min.setDate(min.getDate() - 1);
            this.disabledDates = {
                to: min
            };
        },

        /**
         * Cerrar la requisicion
         */
        async cerrarRequisicion(row)
        {
            let res = await question("Cerrar requisición", "¿Desea cerrar la requisición?");
            if (!res.isConfirmed) return;

            let data = {
                id: row.id,
            };
            postPut(`${this.url}/requisicion/cerrar`, data, null, "Requisición cerrada correctamente",
                this.loading_guardarRequisicion, () =>
                {
                    this.obtenerRequisiciones();
                })
        },

        /**
         * Ver motivo de eliminación
         */
        verMotivo(motivo, tipo)
        {
            info(`Motivo de ${tipo}:`, motivo);
        },

        verAcciones(row)
        {
            if (this.empleado_actual.id == 150) return true;
            const permiso = row.condicion == 1 &&
                (row.empleado_solicita_id == this.empleado_actual.id ||
                    row.empleado_autoriza_id == this.empleado_actual.id);
            return permiso;
        }
    },
    mounted()
    {
        this.cargarInicial();
        this.permisosCRUD = Utilerias.getCRUD(this.$route.path);
    },
}
</script>
