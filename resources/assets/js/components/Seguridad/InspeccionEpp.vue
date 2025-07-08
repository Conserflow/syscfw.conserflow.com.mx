<template>
<main class="main">
    <div class="container-fluid" style="min-height:40vh">
        <!-- Ejemplo de tabla Listado -->
        <div class="card" v-if="tipoCard==1">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Inspección de EPP
                <button type="button" @click="AbrirModalInspeccion()" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <div class="card-body">
                <vue-element-loading :active="isInspeccion_loading" />
                <v-client-table :columns="columns_inspecciones" :data="list_inspecciones" :options="options_inspecciones">
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
                                    <button type="button" @click="VerParticipantes(props.row)" class="dropdown-item">
                                        <i class="fas fa-users"></i>&nbsp; Ver participantes
                                    </button>
                                    <button type="button" @click="Descargar(props.row.id)" class="dropdown-item">
                                        <i class="fas fa-file-pdf"></i>&nbsp; Descargar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </v-client-table>

            </div>
        </div>
        <!-- Fin ejemplo de tabla Listado -->

        <!-- Participantes de Inspeccion -->
        <div class="card" v-if="tipoCard==2">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Participantes
                <button type="button" @click="tipoCard=1" class="btn btn-dark float-sm-right mx-1">
                    <i class="fas fa-arrow-left"></i>&nbsp;Regresar
                </button>
                <button type="button" @click="AbrirModalTrabajador(true)" class="btn btn-dark float-sm-right mx-1">
                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <div class="card-body">
                <vue-element-loading :active="isParticipantes_loading" />
                <v-client-table :columns="columns_participantes" :data="list_participantes" :options="options_participantes">
                    <template slot="id" slot-scope="props">
                        <div class="btn-group" role="group">
                            <div class="btn-group dropup" role="group">
                                <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i>&nbsp; Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <button type="button" @click="AbrirModalTrabajador(false,props.row)" class="dropdown-item">
                                        <i class="fas fa-edit"></i>&nbsp; Detalles
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </v-client-table>

            </div>
        </div>
        <!-- Participantes de Inspeccion -->
    </div>

    <!--Inicio del modal agregar/actualizar-->
    <div class="modal fade" v-if="ver_modal_inspecciones" tabindex="-1" :class="{'mostrar' : ver_modal_inspecciones}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <vue-element-loading :active="isGuardarpl_loading" />
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="CerrarModalInspeccion()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Fecha</label>
                            <div class="col-md-4">
                                <input type="date" v-validate="'required'" data-vv-name="Fecha" v-model="Inspeccion.fecha" class="form-control">
                                <span class="text-danger">{{ errors.first('Fecha') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Ubicación</label>
                            <div class="col-md-4">
                                <input type="text" v-validate="'required|max:50'" data-vv-name="Ubicación" v-model="Inspeccion.ubicacion" class="form-control" autocomplete="off">
                                <span class="text-danger">{{ errors.first('Ubicación') }}</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Realizó</label>
                            <div class="col-md-6">
                                <v-select :options="list_empleados" v-model="Inspeccion.realiza" data-vv-name="Realizó" label="nombre" v-validate="'required'"></v-select>
                                <span class="text-danger">{{ errors.first('Realizó') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Revisó</label>
                            <div class="col-md-6">
                                <v-select :options="list_empleados" v-model="Inspeccion.revisa" data-vv-name="Revisó" label="nombre" v-validate="'required'"></v-select>
                                <span class="text-danger">{{ errors.first('Revisó') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Observaciones</label>
                            <div class="col-md-6">
                                <textarea class="form-control" v-model="Inspeccion.observaciones" data-vv-name="Observaciones" v-validate="'required'"></textarea>
                                <span class="text-danger">{{ errors.first('Observaciones') }}</span>
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

    <!-- Modal trabajador -->
    <div class="modal fade" v-if="ver_modal_trabajador" tabindex="-1" :class="{'mostrar' : ver_modal_trabajador}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <vue-element-loading :active="isGuardarparticip_loading" />
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_trabajador"></h4>
                        <button type="button" class="close" @click="CerrarModalTrabajador()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Empleado</label>
                            <div class="col-md-5">
                                <v-select :disabled="tipoAccion_trabajador==2" :options="list_empleados" v-model="trabajador.empleado" data-vv-name="Empleado" label="nombre" v-validate="'required'"></v-select>
                                <span class="text-danger">{{ errors.first('Empleado') }}</span>
                            </div>
                        </div>
                        <br>
                        <hr>
                        <p class="h5 text-mutted my-2 mb-3">Equipo de Protección Personal</p>
                        <div class="form-group row">
                            <label class="col-md-5 form-control-label font-weight-bold1">Overol o ropa de algodón RF</label>
                            <div class="col-md-4">
                                <select class="form-control" v-model="trabajador.epp_overol">
                                    <option :key="i" v-for="(op,i) in opciones" :value="op.clave">{{op.nombre}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 form-control-label font-weight-bold1">Calzado de Seguridad</label>
                            <div class="col-md-4">
                                <select class="form-control" v-model="trabajador.epp_calzado">
                                    <option :key="i" v-for="(op,i) in opciones" :value="op.clave">{{op.nombre}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 form-control-label font-weight-bold1">Casco con logo </label>
                            <div class="col-md-4">
                                <select class="form-control" v-model="trabajador.epp_casco">
                                    <option :key="i" v-for="(op,i) in opciones" :value="op.clave">{{op.nombre}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 form-control-label font-weight-bold1">Guantes adecuados al riesgo de trabajo</label>
                            <div class="col-md-4">
                                <select class="form-control" v-model="trabajador.epp_guantes">
                                    <option :key="i" v-for="(op,i) in opciones" :value="op.clave">{{op.nombre}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 form-control-label font-weight-bold1">Protección ocular</label>
                            <div class="col-md-4">
                                <select class="form-control" v-model="trabajador.epp_ocular">
                                    <option :key="i" v-for="(op,i) in opciones" :value="op.clave">{{op.nombre}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 form-control-label font-weight-bold1">Protección respiratoria</label>
                            <div class="col-md-4">
                                <select class="form-control" v-model="trabajador.epp_respiratoria">
                                    <option :key="i" v-for="(op,i) in opciones" :value="op.clave">{{op.nombre}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 form-control-label font-weight-bold1">Protección Auditiva</label>
                            <div class="col-md-4">
                                <select class="form-control" v-model="trabajador.epp_auditiva">
                                    <option :key="i" v-for="(op,i) in opciones" :value="op.clave">{{op.nombre}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 form-control-label font-weight-bold1">Barbiquejo</label>
                            <div class="col-md-4">
                                <select class="form-control" v-model="trabajador.epp_barbiquejo">
                                    <option :key="i" v-for="(op,i) in opciones" :value="op.clave">{{op.nombre}}</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <hr>
                        <p class="h5 text-mutted my-2 mb-3">Equipo de Protección Personal</p>
                        <div class="form-group row">
                            <label class="col-md-5 form-control-label font-weight-bold1">Protección Respiratoria ERA</label>
                            <div class="col-md-4">
                                <select class="form-control" v-model="trabajador.epa_respiratoria">
                                    <option :key="i" v-for="(op,i) in opciones" :value="op.clave">{{op.nombre}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 form-control-label font-weight-bold1">Arnes de Seguridad</label>
                            <div class="col-md-4">
                                <select class="form-control" v-model="trabajador.epa_arnes">
                                    <option :key="i" v-for="(op,i) in opciones" :value="op.clave">{{op.nombre}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 form-control-label font-weight-bold1">Careta Facial</label>
                            <div class="col-md-4">
                                <select class="form-control" v-model="trabajador.epa_careta">
                                    <option :key="i" v-for="(op,i) in opciones" :value="op.clave">{{op.nombre}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 form-control-label font-weight-bold1">Mangas, peto, polainas,rodillera </label>
                            <div class="col-md-4">
                                <select class="form-control" v-model="trabajador.epa_mangas">
                                    <option :key="i" v-for="(op,i) in opciones" :value="op.clave">{{op.nombre}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 form-control-label font-weight-bold1">Mascarilla de media cara/Cara completa</label>
                            <div class="col-md-4">
                                <select class="form-control" v-model="trabajador.epa_mascarilla">
                                    <option :key="i" v-for="(op,i) in opciones" :value="op.clave">{{op.nombre}}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" @click="CerrarModalTrabajador()"><i class="fas fa-times"></i>&nbsp;Cerrar</button>
                        <button type="button" v-if="tipoAccion_trabajador==1" class="btn btn-secondary" @click="GuardarTrabajador(true)"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                        <button type="button" v-if="tipoAccion_trabajador==2" class="btn btn-secondary" @click="GuardarTrabajador(false)"><i class="fas fa-save"></i>&nbsp;Actualizar</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- Modal trabajador -->
</main>
</template>

<script>
var config = require('../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            url: "seguridad/inspeccionepp/",
            ver_modal_inspecciones: 0,
            tituloModal: '',
            Inspeccion:
            {

            },
            list_empleados: [],
            tipoAccion: 0,
            isInspeccion_loading: false,
            isGuardarpl_loading: false,
            columns_inspecciones: ["id", "fecha", "realiza", "revisa", "observaciones"],
            list_inspecciones: [],
            options_inspecciones:
            {
                headings:
                {
                    id: 'Acción',
                    nombre: 'Nombre',
                    nombre: "Responsable"
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
            ver_modal_trabajador: false,
            list_participantes: [],
            participantes: [],
            trabajador:
            {},
            tituloModal_trabajador: "",
            tipoAccion_trabajador: 1,
            tipoCard: 1,
            isParticipantes_loading: false,
            isGuardarparticip_loading: false,
            columns_participantes: ["id","empleado", "puesto"],
            list_participantes: [],
            opciones: [
            {
                clave: "OK",
                nombre: "BUENAS CONDICIONES"
            },
            {
                clave: "X",
                nombre: "MALAS CONDICIONES"
            },
            {
                clave: "CD",
                nombre: "CAMBIO POR DESGASTE"
            },
            {
                clave: "N/A",
                nombre: "NO APLICA"
            }],
            trabajador_id: 0,
            options_participantes:
            {
                headings:
                {
                    id: 'Acción',
                },
                pperPage: 10,
                perPageValues: [],
                skin: config.skin,
                filterable:["id","empleado", "puesto"],
                sortable:["id","empleado", "puesto"],
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            },
        }
    },
    computed:
    {},
    methods:
    {

        /**
         * Obtener todas las inspecciones
         */
        ObtenerInspeccion()
        {
            this.isInspeccion_loading = true;
            axios.get(this.url + "obtener").then(res =>
            {
                if (res.data.status)
                {
                    this.isInspeccion_loading = false;
                    this.list_inspecciones = res.data.inspecciones;
                }
                else toastr.error(res.data.status);
            })
        },

        AbrirModalInspeccion(nuevo = true, inspeccion = {})
        {
            this.ver_modal_inspecciones = true;
            if (nuevo)
            {
                this.tipoAccion = 1;
                this.tituloModal = "Registar Inspección";
            }
            else
            {
                this.tipoAccion = 2;
                this.tituloModal = "Actualizar Inspección";
                this.Inspeccion = {
                    ...inspeccion
                };
                this.Inspeccion = {
                    id: inspeccion.id,
                    fecha: inspeccion.fecha,
                    ubicacion: inspeccion.ubicacion,
                    observaciones: inspeccion.observaciones,
                    revisa:
                    {
                        id: inspeccion.empleado_revisa_id,
                        nombre: inspeccion.revisa
                    },
                    realiza:
                    {
                        id: inspeccion.empleado_realiza_id,
                        nombre: inspeccion.realiza
                    },
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
         * Registrar plática
         */
        GuardarInspeccion(nuevo = false)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                this.isGuardarpl_loading = true;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.Inspeccion.id);
                data.append("fecha", this.Inspeccion.fecha);
                data.append("ubicacion", this.Inspeccion.ubicacion);
                data.append("observaciones", this.Inspeccion.observaciones);
                data.append("empleado_realiza_id", this.Inspeccion.realiza.id);
                data.append("empleado_revisa_id", this.Inspeccion.revisa.id);
                axios.post(this.url + "guardar", data).then(res =>
                {
                    this.isGuardarpl_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Plática guardada correctamente");
                        this.CerrarModalInspeccion();
                        this.ObtenerInspeccion();
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
            this.ver_modal_inspecciones = 0;
            this.Inspeccion = {
                revisa:
                {},
                realiza:
                {}
            }

        },
        VerParticipantes(Inspeccion)
        {
            this.tipoCard = 2;
            this.Inspeccion_id = Inspeccion.id;
            this.isParticipantes_loading = true;
            this.ObtenerParticipantes(Inspeccion.id);
        },

        // Obtener los participante de la Inspeccion selecionada
        ObtenerParticipantes()
        {
            this.isParticipantes_loading = true;
            axios.get(this.url + "participantes/obtener/" + this.Inspeccion_id).then(res =>
            {
                this.isParticipantes_loading = false;
                if (res.data.status)
                {
                    this.list_participantes = res.data.participantes;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Guardar los participantes en la Inspeccion seleccinoada
         */
        GuardarTrabajador(nuevo)
        {

            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                this.isGuardarparticip_loading = true;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.trabajador_id);
                data.append("sip_id", this.Inspeccion_id);
                data.append("inspeccion_id", this.Inspeccion_id);
                data.append("empleado_id", this.trabajador.empleado.id);
                data.append("epp_overol", this.trabajador.epp_overol);
                data.append("epp_calzado", this.trabajador.epp_calzado);
                data.append("epp_casco", this.trabajador.epp_casco);
                data.append("epp_guantes", this.trabajador.epp_guantes);
                data.append("epp_ocular", this.trabajador.epp_ocular);
                data.append("epp_respiratoria", this.trabajador.epp_respiratoria);
                data.append("epp_auditiva", this.trabajador.epp_auditiva);
                data.append("epp_barbiquejo", this.trabajador.epp_barbiquejo);
                data.append("epa_respiratoria", this.trabajador.epa_respiratoria);
                data.append("epa_arnes", this.trabajador.epa_arnes);
                data.append("epa_careta", this.trabajador.epa_careta);
                data.append("epa_mangas", this.trabajador.epa_mangas);
                data.append("epa_mascarilla", this.trabajador.epa_mascarilla);
                axios.post(this.url + "participantes/guardar", data).then(res =>
                {
                    this.isGuardarparticip_loading = false;
                    if (res.data.status)
                    {
                        this.participantes = [];
                        toastr.success("Guardado correctamente");
                        this.ObtenerParticipantes();
                        this.CerrarModalTrabajador();
                    }
                    else toastr.error(res.data.mensaje);
                })
            })
        },
        /**
         * Registra un nuevo empleado en la inspección
         */
        AbrirModalTrabajador(nuevo, data)
        {
            this.ver_modal_trabajador = true;
            if (nuevo)
            {
                this.LimpiarTrabajador();
                this.tituloModal_trabajador = "Registrar empleado";
                this.tipoAccion_trabajador = 1;
            }
            else
            {
                this.tipoAccion_trabajador=2;
                this.tituloModal_trabajador = "Actualizar empleado";
                this.tipoAccion_trabajador = 2;
                this.trabajador_id = data.id;
                this.trabajador = {
                    ...data,
                    empleado:
                    {
                        id: data.empleado_id,
                        nombre: data.empleado
                    }
                };
            }
        },
        CerrarModalTrabajador()
        {
            this.ver_modal_trabajador = false;
        },
        LimpiarTrabajador()
        {
            this.trabajador = {
                epp_overol: "OK",
                epp_calzado: "OK",
                epp_casco: "OK",
                epp_guantes: "OK",
                epp_ocular: "OK",
                epp_respiratoria: "OK",
                epp_auditiva: "OK",
                epp_barbiquejo: "OK",
                epa_respiratoria: "OK",
                epa_arnes: "OK",
                epa_careta: "OK",
                epa_mangas: "OK",
                epa_mascarilla: "OK",
            };
        },

        /**
         * Formato
         */
        Descargar(p_id)
        {
            window.open(this.url + 'descargar/' + p_id, '_blank');
        }
    },
    mounted()
    {
        this.ObtenerInspeccion();
        this.CerrarModalInspeccion();
        this.ObtenerEmpleados();
    }
}
</script>
