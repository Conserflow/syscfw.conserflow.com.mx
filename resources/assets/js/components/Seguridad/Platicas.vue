<template>
<main class="main">
    <div class="container-fluid" style="min-height:40vh">
        <!-- Ejemplo de tabla Listado -->
        <div class="card" v-if="tipoCard==1">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Pláticas de Seguridad
                <button type="button" @click="AbrirModalPlaticas()" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <div class="card-body">
                <vue-element-loading :active="isPlaticas_loading" />
                <v-client-table :columns="columns_platicas" :data="list_platicas" :options="options_platicas">
                    <template slot="id" slot-scope="props">
                        <div class="btn-group" role="group">
                            <div class="btn-group dropup" role="group">
                                <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i>&nbsp; Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <button type="button" @click="AbrirModalPlaticas(false,props.row)" class="dropdown-item">
                                        <i class="fas fa-edit"></i>&nbsp; Actualizar
                                    </button>
                                    <button type="button" @click="DescargarPlantilla(props.row.id)" class="dropdown-item">
                                        <i class="fas fa-file-pdf"></i>&nbsp; Descargar Plantilla
                                    </button>
                                    <button type="button" @click="SubirEvidencia(props.row.id)" class="dropdown-item">
                                        <i class="fas fa-file-pdf"></i>&nbsp; Subir Evidencia
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template slot="documento" slot-scope="props">
                        <span v-if="props.row.documento==null" class="btn btn-dark">N/D</span>
                        <button @click="DescargarEvidencia(props.row.id)" v-else class="btn btn-dark">
                            <i class="fas fa-download"></i>
                        </button>
                    </template>
                </v-client-table>

            </div>
        </div>
        <!-- Fin ejemplo de tabla Listado -->

        <!-- Participantes de platica -->
        <div class="card" v-if="tipoCard==2">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Participantes
                <button type="button" @click="tipoCard=1" class="btn btn-dark float-sm-right mx-1">
                    <i class="fas fa-arrow-left"></i>&nbsp;Regresar
                </button>
            </div>
            <div class="card-body">
                <br>
                <vue-element-loading :active="isGuardarparticip_loading" />

                <div class="form-group row">
                    <label class="col-md-2 form-control-label">Responsable</label>
                    <div class="col-md-6">
                        <v-select multiple :options="list_empleados" v-model="participantes" data-vv-name="Participantes" label="nombre" v-validate="'required'"></v-select>
                        <span class="text-danger">{{ errors.first('Participantes') }}</span>
                    </div>
                    <button class="btn btn-dark" @click="GuadarParticipantes()">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <br>
                <br>
                <vue-element-loading :active="isParticipantes_loading" />
                <v-client-table :columns="columns_participantes" :data="list_participantes" :options="options_participantes">
                </v-client-table>

            </div>
        </div>
        <!-- Participantes de platica -->
    </div>

    <!--Inicio del modal agregar/actualizar-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_platicas}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <vue-element-loading :active="isGuardarpl_loading" />
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="CerrarModalPlaticas()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Fecha</label>
                            <div class="col-md-3">
                                <input type="date" v-validate="'required'" data-vv-name="Fecha" v-model="platica.fecha" class="form-control">
                                <span class="text-danger">{{ errors.first('Fecha') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Ubicación</label>
                            <div class="col-md-4">
                                <input type="text" v-validate="'required|max:50'" data-vv-name="Ubicación" v-model="platica.ubicacion" class="form-control" autocomplete="off">
                                <span class="text-danger">{{ errors.first('Ubicación') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Tema</label>
                            <div class="col-md-6">
                                <textarea v-validate="'required|max:100'" data-vv-name="Tema" v-model="platica.tema" class="form-control">
                                </textarea>
                                <span class="text-danger">{{ errors.first('Tema') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Responsable</label>
                            <div class="col-md-6">
                                <v-select :options="list_empleados" v-model="platica.responsable" data-vv-name="Responsable" label="nombre" v-validate="'required'"></v-select>
                                <span class="text-danger">{{ errors.first('Responsable') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" @click="CerrarModalPlaticas()"><i class="fas fa-times"></i>&nbsp;Cerrar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-secondary" @click="GuardarPlatica(true)"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-secondary" @click="GuardarPlatica(false)"><i class="fas fa-save"></i>&nbsp;Actualizar</button>
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
            url: "seguridad/platicas/",
            ver_modal_platicas: 0,
            tituloModal: '',
            platica:
            {

            },
            list_empleados: [],
            tipoAccion: 0,
            isPlaticas_loading: false,
            isGuardarpl_loading: false,
            columns_platicas: ["id", "fecha", "tema", "nombre","documento"],
            list_platicas: [],
            options_platicas:
            {
                headings:
                {
                    id: 'Acción',
                    nombre: 'Nombre',
                    nombre: "Responsable",
                    documento:"Evidencia"
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
            list_participantes: [],
            participantes: [],
            tipoCard: 1,
            isParticipantes_loading: false,
            isGuardarparticip_loading: false,
            columns_participantes: ["nombre", "puesto"],
            list_participantes: [],
            options_participantes:
            {
                headings:
                {
                    id: 'Acción',
                    nombre: "Empleado"
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
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
         * Obtener todas las platicas
         */
        ObtenerPlaticas()
        {
            this.isPlaticas_loading = true;
            axios.get(this.url + "obtenerplaticas").then(res =>
            {
                if (res.data.status)
                {
                    this.isPlaticas_loading = false;
                    this.list_platicas = res.data.platicas;
                }
                else toastr.error(res.data.status);
            })
        },

        AbrirModalPlaticas(nuevo = true, platica = {})
        {
            this.ver_modal_platicas = true;
            if (nuevo)
            {
                this.tipoAccion = 1;
                this.tituloModal = "Registar plática";
            }
            else
            {
                this.tipoAccion = 2;
                this.tituloModal = "Actualizar plática";
                this.platica = {
                    ...platica
                };
                this.platica.responsable = {
                    id: platica.responsable_id,
                    nombre: platica.nombre,
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
        GuardarPlatica(nuevo = false)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                this.isGuardarpl_loading = true;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.platica.id);
                data.append("fecha", this.platica.fecha);
                data.append("ubicacion", this.platica.ubicacion);
                data.append("tema", this.platica.tema);
                data.append("responsable_id", this.platica.responsable.id);
                axios.post(this.url + "guardar", data).then(res =>
                {
                    this.isGuardarpl_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Plática guardada correctamente");
                        this.CerrarModalPlaticas();
                        this.ObtenerPlaticas();
                    }
                    else
                    {
                        toastr.error(res.data.mensaje);
                    }
                })
            })

        },
        CerrarModalPlaticas()
        {
            this.ver_modal_platicas = 0;
            this.platica = {
                responsable:
                {}
            }
        },
        VerParticipantes(platica)
        {
            this.tipoCard = 2;
            this.platica_id = platica.id;
            this.isParticipantes_loading = true;
            this.ObtenerParticipantes(platica.id);
        },

        // Obtener los participante de la platica selecionada
        ObtenerParticipantes()
        {
            this.isParticipantes_loading = true;
            axios.get(this.url + "participantes/obtener/" + this.platica_id).then(res =>
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
         * Guardar los participantes en la platica seleccinoada
         */
        GuadarParticipantes()
        {
            if (this.participantes.length == 0) return;
            this.isGuardarparticip_loading = true;
            axios.post(this.url + "participantes/guardar",
            {
                "platica_id": this.platica_id,
                "participantes": this.participantes
            }).then(res =>
            {
                this.isGuardarparticip_loading = false;
                if (res.data.status)
                {
                    this.participantes = [];
                    toastr.success("Guardado correctamente");
                    this.ObtenerParticipantes();
                }
                else toastr.error(res.data.mensaje);
            })
        },

        /**
         * Genera la plantilla para las platicas
         */
        DescargarPlantilla(id)
        {
            window.open(this.url + "descargarplantilla/" + id, "_blank");
        },

        /**
         * Subir pdf de platica
         */
        SubirEvidencia(id)
        {
            Swal.fire(
            {
                title: "Cargar documento de la plática",
                input: "file",
                confirmButtonText: "Cargar",
            }).then(result =>
            {
                console.error(result);
                if (result.value == null) return;
                if (result.value.type === "application/pdf")
                {
                    let data = new FormData();
                    data.append("platica_id", id);
                    data.append("evidencia", result.value);
                    axios.post(this.url + "subirevidencia", data).then(res =>
                    {
                        if (res.data.status)
                        {
                            toastr.success("Documento subido correctamente");
                            this.ObtenerPlaticas();
                        }
                        else
                        {
                            toastr.error(res.data.mensaje);
                        }
                    }).catch(r =>
                    {
                        console.error(r);
                        toastr.error("Error al subir el documento");
                    })
                }
                else
                {
                    toastr.warning("Seleccione un PDF");
                }
            })
        },
        DescargarEvidencia(id)
        {
            window.open(this.url + 'descargarevidencia/' + id, '_blank');
        }
    },
    mounted()
    {
        this.ObtenerPlaticas();
        this.ObtenerEmpleados();
    }
}
</script>
