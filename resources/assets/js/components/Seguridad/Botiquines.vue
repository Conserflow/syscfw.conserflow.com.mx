<template>
<main class="main">
    <div class="container-fluid" style="min-height:40vh">
        <!-- Ejemplo de tabla Listado -->
        <div class="card" v-if="tipoCard==1">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Inspección de Botiquines
                <button type="button" @click="AbrirModalInspeccion()" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <div class="card-body">
                <vue-element-loading :active="isInspecciones_loading" />
                <v-client-table :columns="columns_inspecciones" :data="list_inspecciones" :options="options_inspeccion">
                    <template slot="id" slot-scope="props">
                        <div class="btn-group" role="group">
                            <div class="btn-group dropup" role="group">
                                <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i>&nbsp; Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <button type="button" @click="AbrirModalInspeccion(false,props.row)" class="dropdown-item">
                                        <i class="fas fa-edit"></i>&nbsp; Actualizar
                                    </button>
                                    <button type="button" @click="VerBotiquines(props.row)" class="dropdown-item">
                                        <i class="fas fa-eye"></i>&nbsp; Ver
                                    </button>
                                    <button type="button" @click="Descargar(props.row.id)" class="dropdown-item">
                                        <i class="fas fa-file-pdf"></i>&nbsp; Descargar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template slot="tipo" slot-scope="props">
                        <p v-if="props.row.tipo==1">Portátil</p>
                        <p v-if="props.row.tipo==2">Fijo</p>
                    </template>
                </v-client-table>

            </div>
        </div>
        <!-- Fin ejemplo de tabla Listado -->

        <!-- Participantes de platica -->
        <div class="card" v-if="tipoCard==2">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Botiquín de {{area}}
                <button type="button" @click="tipoCard=1" class="btn btn-dark float-sm-right mx-1">
                    <i class="fas fa-arrow-left"></i>&nbsp;Regresar
                </button>
                <button type="button" @click="AbrirModalBotiquin(true)" class="btn btn-dark float-sm-right mx-1">
                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <div class="card-body">
                <v-client-table :columns="columns_botiquines" :data="list_botiquines" :options="options_botiquines">
                    <template slot="id" slot-scope="props">
                        <div class="btn-group" role="group">
                            <div class="btn-group dropup" role="group">
                                <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i>&nbsp; Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <button type="button" @click="AbrirModalBotiquin(false,props.row)" class="dropdown-item">
                                        <i class="fas fa-edit"></i>&nbsp; Actualizar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </v-client-table>

            </div>
        </div>
        <!-- Participantes de platica -->
    </div>

    <!--Inicio del modal agregar/actualizar-->
    <div v-if="ver_modal_inspeccion" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_inspeccion}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <vue-element-loading :active="isGuardarBotiquin_loading" />
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="CerrarModalBotiquin()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Fecha</label>
                            <div class="col-md-3">
                                <input type="date" v-validate="'required'" data-vv-name="Fecha" v-model="inspeccion.fecha" class="form-control">
                                <span class="text-danger">{{ errors.first('Fecha') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Área</label>
                            <div class="col-md-6">
                                <input type="text" v-validate="'required|max:50'" data-vv-name="Área" v-model="inspeccion.area" class="form-control" autocomplete="off">
                                <span class="text-danger">{{ errors.first('Área') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Tipo</label>
                            <div class="col-md-3">
                                <select class="form-control" v-model="inspeccion.tipo">
                                    <option value="1">Portátil</option>
                                    <option value="2">Fijo</option>
                                </select>
                            </div>
                            <label class="col-md-2 form-control-label">No. de Botiquín</label>
                            <div class="col-md-3">
                                <input type="number" v-validate="'required'" min="1" max="100" data-vv-name="Número de Botiquín" v-model="inspeccion.numero" class="form-control">
                                <span class="text-danger">{{ errors.first('Número de Botiquín') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 form-control-label">El botiquín está en un lugar visible</label>
                            <div class="col-md-6">
                                <input type="radio" value="1" v-model="inspeccion.visible"> Sí
                                <span class="mx-5"></span>
                                <input type="radio" value="2" v-model="inspeccion.visible"> No
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 form-control-label">El botiquín está en buen estado</label>
                            <div class="col-md-6">
                                <input type="radio" value="1" v-model="inspeccion.buen_estado"> Sí
                                <span class="mx-5"></span>
                                <input type="radio" value="2" v-model="inspeccion.buen_estado"> No
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Inspector</label>
                            <div class="col-md-6">
                                <v-select :options="list_empleados" label="nombre" data-vv-name="Inspector" v-validate="'required'" v-model="inspeccion.inspector"></v-select>
                                <span class="text-danger">{{ errors.first('Inspector') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Responsable</label>
                            <div class="col-md-6">
                                <v-select :options="list_empleados" label="nombre" v-validate="'required'" v-model="inspeccion.responsable"></v-select>
                                <span class="text-danger">{{ errors.first('Responsable') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Recomendaciones</label>
                            <div class="col-md-8">
                                <textarea rows="4" data-vv-name="Recomendaciones" v-model="inspeccion.recomendaciones" v-validate="'required'" class="form-control"></textarea>
                                <span class="text-danger">{{ errors.first('Recomendaciones') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" @click="CerrarModalInspeccion()"><i class="fas fa-times"></i>&nbsp;Cerrar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-secondary" @click="GuardarInspeccion(true)"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-secondary" @click="GuardarInspeccion(false)"><i class="fas fa-save"></i>&nbsp;Actualizar</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->

    <!--Inicio del modal botiquin agregar/actualizar-->
    <div v-if="ver_modal_botiquin" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_botiquin}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <vue-element-loading :active="isGuardarInspeccion_loading" />
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_Botiquin"></h4>
                        <button type="button" class="close" @click="CerrarModalBotiquin()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Material</label>
                            <div class="col-md-5">
                                <textarea maxlength="100" v-validate="'required'" data-vv-name="Material" v-model="botiquin_modal.material" class="form-control">
                                </textarea>
                                <span class="text-danger">{{ errors.first('Material') }}</span>
                            </div>

                            <div class="form-check form-inline">
                                <label class="form-check-label col-md-6">
                                    Elemento de apoyo
                                </label>
                                <label class="switch switch-default switch-pill switch-dark">
                                    <input type="checkbox" class="switch-input" v-model="botiquin_modal.apoyo">
                                    <span class="switch-label"></span>
                                    <span class="switch-handle"></span>
                                </label>
                                <span class="ml-3">{{botiquin_modal.apoyo?"Sí":"No"}}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Existencia</label>
                            <div class="col-md-2">
                                <input type="number" min="1" step="1" v-validate="'required|decimal:0'" data-vv-name="Existencia" v-model="botiquin_modal.existencia" class="form-control">
                                <span class="text-danger">{{ errors.first('Existencia') }}</span>
                            </div>
                            <label class="col-md-2 form-control-label">Reposición</label>
                            <div class="col-md-2">
                                <input type="number" min="0" step="1" v-validate="'required|decimal:0'" class="form-control" data-vv-name="Reposición" v-model="botiquin_modal.reposicion" />
                                <span class="text-danger">{{ errors.first('Reposición') }}</span>
                            </div>
                            <label class="col-md-2 form-control-label">Fecha de vencimiento</label>
                            <div class="col-md-2">
                                <input type="date" data-vv-name="Fecha de vencimiento" v-validate="'required'" class="form-control" v-model="botiquin_modal.fecha_vencimiento" />
                                <span class="text-danger">{{ errors.first('Fecha de vencimiento') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Observación</label>
                            <div class="col-md-8">
                                <textarea rows="4" data-vv-name="Recomendaciones" v-model="botiquin_modal.observacion" v-validate="'required'" class="form-control"></textarea>
                                <span class="text-danger">{{ errors.first('Recomendaciones') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" @click="CerrarModalBotiquin()"><i class="fas fa-times"></i>&nbsp;Cerrar</button>
                        <button type="button" v-if="tipoAccion_botiquin==1" class="btn btn-secondary" @click="GuardarBotiquin(true)"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                        <button type="button" v-if="tipoAccion_botiquin==2" class="btn btn-secondary" @click="GuardarBotiquin(false)"><i class="fas fa-save"></i>&nbsp;Actualizar</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->

</main>
</template>

<script>
var config = require('../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            url: "seguridad/inspeccionbotiquin/",
            ver_modal_inspeccion: 0,
            tituloModal: '',
            columns_botiquines: [
                "id",
                "material",
                "existencia",
                "reposicion",
                "fecha_vencimiento",
                "observacion",
            ],
            list_botiquines: [],
            options_botiquines:
            {
                headings:
                {
                    id: 'Acción',
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts,
                filterable: [
                    "id",
                    "material",
                    "existencia",
                    "reposicion",
                    "fecha_vencimiento",
                    "observacion"
                ],
                sortable: [
                    "id",
                    "material",
                    "existencia",
                    "reposicion",
                    "fecha_vencimiento",
                    "observacion"
                ]
            },
            inspeccion:
            {
                visible: 1,
                buen_estado: 1,
                tipo: 1,
                recomendaciones: "-"
            },
            list_empleados: [],
            tipoAccion: 0,
            isInspecciones_loading: false,
            isGuardarInspeccion_loading: false,
            columns_inspecciones: ["id", "fecha", "area","tipo", "numero", "inspector", "responsable"],
            list_inspecciones: [],
            options_inspeccion:
            {
                headings:
                {
                    id: 'Acción',
                    area: "Área"
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            },
            // Participantes
            participante:
            {

            },
            participantes: [],
            tipoCard: 1,
            isBotiquines_loading: false,
            isGuardarBotiquin_loading: false,
            botiquin_modal:
            {},
            tipoAccion_botiquin: 1,
            tituloModal_Botiquin: "",
            area: "",
            ver_modal_botiquin: false,
        }
    },
    computed:
    {},
    methods:
    {

        /**
         * Obtener todas las inspeccion
         */
        ObtenerInspecciones()
        {
            this.isInspecciones_loading = true;
            axios.get(this.url + "obtener").then(res =>
            {
                if (res.data.status)
                {
                    this.isInspecciones_loading = false;
                    this.list_inspecciones = res.data.inspecciones;
                }
                else toastr.error(res.data.status);
            })
        },

        AbrirModalInspeccion(nuevo = true, inspeccion = {})
        {
            this.ver_modal_inspeccion = true;
            if (nuevo)
            {
                this.tipoAccion = 1;
                this.tituloModal = "Registar Inspección";
            }
            else
            {
                this.tipoAccion = 2;
                this.tituloModal = "Actualizar Inspección";
                this.inspeccion = {
                    ...inspeccion
                };
                this.inspeccion.inspector = {
                    id: inspeccion.inspector_id,
                    nombre: inspeccion.inspector,
                };
                this.inspeccion.responsable = {
                    id: inspeccion.responsable_id,
                    nombre: inspeccion.responsable,
                };

            }
        },

        /**
         * Obtener todos los empleados
         */
        ObtenerEmpleados()
        {
            axios.get("generales/empleadoactivos").then(res =>
            {
                if (res.data.status)
                {
                    this.list_empleados = res.data.empleados;
                }
                else toastr.error(res.data.mensaje);
            })
        },

        /**
         * Registrar inspeccion
         */
        GuardarInspeccion(nuevo = false)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                this.isGuardarInspeccion_loading = true;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.inspeccion.id);
                data.append("area", this.inspeccion.area);
                data.append("numero", this.inspeccion.numero);
                data.append("fecha", this.inspeccion.fecha);
                data.append("inspector_id", this.inspeccion.inspector.id);
                data.append("responsable_id", this.inspeccion.responsable.id);
                data.append("tipo", this.inspeccion.tipo);
                data.append("visible", this.inspeccion.visible);
                data.append("buen_estado", this.inspeccion.buen_estado);
                data.append("recomendaciones", this.inspeccion.recomendaciones);
                axios.post(this.url + "guardar", data).then(res =>
                {
                    this.isGuardarInspeccion_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Inspección guardada correctamente");
                        this.CerrarModalInspeccion();
                        this.ObtenerInspecciones();
                    }
                    else
                    {
                        toastr.error(res.data.mensaje);
                    }
                })
            })

        },
        CerrarModalInspeccion()
        {
            this.ver_modal_inspeccion = 0;
            this.inspeccion = {
                responsable:
                {},
                inspector:
                {},
                visible: 1,
                buen_estado: 1,
                tipo: 1,
                recomendaciones: "-"
            }
        },
        VerBotiquines(inspeccion)
        {
            this.tipoCard = 2;
            this.inspeccion_id = inspeccion.id;
            this.isBotiquines_loading = true;
            this.area = inspeccion.area;
            this.ObtenerBotiquines(inspeccion.id);
        },

        // Obtener los botiquines de la inspección actual
        ObtenerBotiquines()
        {
            this.isBotiquines_loading = true;
            axios.get(this.url + "botiquines/obtener/" + this.inspeccion_id).then(res =>
            {
                this.isBotiquines_loading = false;
                if (res.data.status)
                {
                    this.list_botiquines = res.data.botiquines;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Guardar los botiquines
         */
        GuardarBotiquin(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;

                this.isGuardarBotiquin_loading = true;
                let data = new FormData();
                if (!nuevo) data.append("id", this.botiquin_modal.id);
                data.append("sib_id", this.inspeccion_id);
                data.append("apoyo", this.botiquin_modal.apoyo ? 1 : 0);
                data.append("fecha_vencimiento", this.botiquin_modal.fecha_vencimiento);
                data.append("existencia", this.botiquin_modal.existencia);
                data.append("reposicion", this.botiquin_modal.reposicion);
                data.append("material", this.botiquin_modal.material);
                data.append("observacion", this.botiquin_modal.observacion);

                axios.post(this.url + "botiquines/guardar", data).then(res =>
                {
                    this.isGuardarBotiquin_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.CerrarModalBotiquin();
                        this.ObtenerBotiquines();
                    }
                    else toastr.error(res.data.mensaje);
                })
            })
        },

        /**
         * Abrirl modal para registro de botiquin
         */
        AbrirModalBotiquin(nuevo, botiquin = {})
        {
            this.LimpiarBotiquin();
            this.ver_modal_botiquin = true;
            if (nuevo)
            {
                this.tituloModal_Botiquin = "Registrar botiquín";
                this.tipoAccion_botiquin = 1;
            }
            else
            {
                this.tituloModal_Botiquin = "Actualizar botiquín";
                this.botiquin_modal = {
                    ...botiquin
                };
                this.tipoAccion_botiquin = 2;
            }
        },

        /**
         * 
         */
        LimpiarBotiquin()
        {
            this.botiquin_modal = {
                apoyo: false,
                existencia: 0,
                reposicion: 0,
                material: "",
                observacion: "-"
            };
        },

        /**
         * Cerrar modal botiquin
         */
        CerrarModalBotiquin()
        {
            this.ver_modal_botiquin = false;
        },

        Descargar(p_id)
        {
            window.open(this.url + 'descargar/' + p_id, '_blank');
        }
    },
    mounted()
    {
        this.ObtenerInspecciones();
        this.ObtenerEmpleados();
    }
}
</script>
