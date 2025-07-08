<template>
<main class="main">
    <div class="container-fluid" style="min-height:40vh">
        <!-- Ejemplo de tabla Listado -->
        <div class="card" v-if="tipoCard==1">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Pruebas de Alcoholimetría
                <button type="button" @click="AbrirModalPruebas()" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <div class="card-body">
                <vue-element-loading :active="isPruebas_loading" />
                <v-client-table :columns="columns_pruebas" :data="list_pruebas" :options="options_pruebas">
                    <template slot="id" slot-scope="props">
                        <div class="btn-group" role="group">
                            <div class="btn-group dropup" role="group">
                                <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i>&nbsp; Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <button type="button" @click="AbrirModalPruebas(false,props.row)" class="dropdown-item">
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

        <!-- Participantes de prueba -->
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
                </div>
                <div class="form-group row">
                    <label class="col-md-2 form-control-label">Resultado</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control col-9" v-model="resultado" data-vv-name="Resultado" maxlength="50" v-validate="'required'" />
                        <span class="text-danger">{{ errors.first('Resultado') }}</span>
                    </div>
                    <button class=" btn btn-dark" @click="GuadarParticipantes()">
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
        <!-- Participantes de prueba -->
    </div>

    <!--Inicio del modal agregar/actualizar-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_pruebas}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <vue-element-loading :active="isGuardarpl_loading" />
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="CerrarModalPruebas()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Fecha</label>
                            <div class="col-md-3">
                                <input type="date" v-validate="'required'" data-vv-name="Fecha" v-model="prueba.fecha" class="form-control">
                                <span class="text-danger">{{ errors.first('Fecha') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Ubicación</label>
                            <div class="col-md-4">
                                <input type="text" v-validate="'required|max:50'" data-vv-name="Ubicación" v-model="prueba.ubicacion" class="form-control" autocomplete="off">
                                <span class="text-danger">{{ errors.first('Ubicación') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Responsable</label>
                            <div class="col-md-6">
                                <v-select :options="list_empleados" v-model="prueba.responsable" data-vv-name="Responsable" label="nombre" v-validate="'required'"></v-select>
                                <span class="text-danger">{{ errors.first('Responsable') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 form-control-label">Observaciones</label>
                            <div class="col-md-6">
                                <textarea v-validate="'required|max:100'" data-vv-name="Observaciones" v-model="prueba.observaciones" class="form-control">
                                </textarea>
                                <span class="text-danger">{{ errors.first('Observaciones') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" @click="CerrarModalPruebas()"><i class="fas fa-times"></i>&nbsp;Cerrar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-secondary" @click="GuardarPrueba(true)"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-secondary" @click="GuardarPrueba(false)"><i class="fas fa-save"></i>&nbsp;Actualizar</button>
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
            url: "seguridad/pruebasalcohol/",
            ver_modal_pruebas: 0,
            tituloModal: '',
            prueba:
            {

            },
            list_empleados: [],
            tipoAccion: 0,
            isPruebas_loading: false,
            isGuardarpl_loading: false,
            columns_pruebas: ["id", "fecha", "ubicacion", "responsable", "observaciones","documento"],
            list_pruebas: [],
            options_pruebas:
            {
                headings:
                {
                    id: 'Acción',
                    nombre: 'Nombre',
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
            resultado: "",
            list_participantes: [],
            participantes: [],
            tipoCard: 1,
            isParticipantes_loading: false,
            isGuardarparticip_loading: false,
            columns_participantes: ["nombre", "puesto", "resultado"],
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
         * Obtener todas las pruebaalcohol
         */
        ObtenerPruebas()
        {
            this.isPruebas_loading = true;
            axios.get(this.url + "obtenerpruebas").then(res =>
            {
                if (res.data.status)
                {
                    this.isPruebas_loading = false;
                    this.list_pruebas = res.data.pruebas;
                }
                else toastr.error(res.data.status);
            })
        },

        AbrirModalPruebas(nuevo = true, prueba = {})
        {
            this.ver_modal_pruebas = true;
            if (nuevo)
            {
                this.tipoAccion = 1;
                this.tituloModal = "Registar plática";
            }
            else
            {
                this.tipoAccion = 2;
                this.tituloModal = "Actualizar plática";
                this.prueba = {
                    ...prueba
                };
                this.prueba.responsable = {
                    id: prueba.responsable_id,
                    nombre: prueba.responsable,
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
        GuardarPrueba(nuevo = false)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                this.isGuardarpl_loading = true;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.prueba.id);
                data.append("fecha", this.prueba.fecha);
                data.append("ubicacion", this.prueba.ubicacion);
                data.append("observaciones", this.prueba.observaciones);
                data.append("responsable_id", this.prueba.responsable.id);
                axios.post(this.url + "guardar", data).then(res =>
                {
                    this.isGuardarpl_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Plática guardada correctamente");
                        this.CerrarModalPruebas();
                        this.ObtenerPruebas();
                    }
                    else
                    {
                        toastr.error(res.data.mensaje);
                    }
                })
            })

        },
        CerrarModalPruebas()
        {
            this.ver_modal_pruebas = 0;
            this.prueba = {
                responsable:
                {}
            }
        },
        VerParticipantes(prueba)
        {
            this.tipoCard = 2;
            this.prueba_id = prueba.id;
            this.isParticipantes_loading = true;
            this.ObtenerParticipantes(prueba.id);
        },

        // Obtener los participante de la prueba selecionada
        ObtenerParticipantes()
        {
            this.isParticipantes_loading = true;
            axios.get(this.url + "participantes/obtener/" + this.prueba_id).then(res =>
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
         * Guardar los participantes en la prueba seleccinoada
         */
        GuadarParticipantes()
        {
            if (this.participantes.length == 0) return;
            this.isGuardarparticip_loading = true;
            axios.post(this.url + "participantes/guardar",
            {
                "prueba_id": this.prueba_id,
                "participantes": this.participantes,
                "resultado": this.resultado
            }).then(res =>
            {
                this.isGuardarparticip_loading = false;
                if (res.data.status)
                {
                    this.participantes = [];
                    this.resultado = "";
                    toastr.success("Guardado correctamente");
                    this.ObtenerParticipantes();
                }
                else toastr.error(res.data.mensaje);
            })
        },

        DescargarPlantilla(p_id)
        {
            window.open(this.url + 'descargarplantilla/'+ p_id, '_blank');
        },

        /**
         * Subir pdf de platica
         */
        SubirEvidencia(id)
        {
            Swal.fire(
            {
                title: "Cargar documento de la prueba",
                input: "file",
                confirmButtonText: "Cargar",
            }).then(result =>
            {
                console.error(result);
                if (result.value == null) return;
                if (result.value.type === "application/pdf")
                {
                    let data = new FormData();
                    data.append("prueba_id", id);
                    data.append("evidencia", result.value);
                    axios.post(this.url + "subirevidencia", data).then(res =>
                    {
                        if (res.data.status)
                        {
                            toastr.success("Documento subido correctamente");
                            this.ObtenerPruebas();
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
        this.ObtenerPruebas();
        this.ObtenerEmpleados();
    }
}
</script>
