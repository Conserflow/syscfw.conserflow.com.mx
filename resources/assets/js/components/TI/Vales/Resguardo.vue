<template>
<div>
    <div class="card">
        <div class="card-body">
            <v-client-table :data="list_vales" :columns="columns_vales" :options="options_vales">
                <template slot="id" slot-scope="props">
                    <div class="btn-group" role="group">
                        <button id="btn_id" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-grip-horizontal"></i>
                        </button>
                        <div class="dropdown-menu">
                            <template>
                                <button type="button" v-if="props.row.estado <= 1" @click="AbrirModal(false, props.row)" class="dropdown-item">
                                    <i class="fas fa-edit mr-1"></i>Actualizar
                                </button>
                                <button type="button" v-if="props.row.estado <= 1" @click="Regresar(props.row)" class="dropdown-item">
                                    <i class="fas fa-undo mr-1"></i>Retorno
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
                <template slot="autorizado" slot-scope="props">
                    <button v-if="props.row.autorizado" class="btn btn-outline-success"> Autorizado</button>
                    <button v-else class="btn btn-outline-danger" @click="AbrirModalAutorizar(props.row)"> Por autorizar</button>
                </template>
                <template slot="descargar" slot-scope="props">
                    <button type="button" @click="Descargar(props.row)" class="btn btn-outline-dark">
                        <i class="fas fa-download"></i>
                    </button>
                </template>

                <template slot="estado" slot-scope="props">
                    <template v-if="props.row.estado == 1">
                        <span class="btn btn-outline-success"> EN RESGUARDO</span>
                    </template>
                    <template v-if="props.row.estado == 2">
                        <span class="btn btn-outline-primary"> RETORNADO</span>
                    </template>
                    <template v-if="props.row.estado == 3">
                        <span class="btn btn-outline-warning"> RETORNADO CON OBSERVACIONES</span>
                    </template>
                </template>

                <template slot="tipo" slot-scope="props">
                    <template v-if="props.row.tipo == 1">
                        <span class="btn btn-outline-success"> Computo</span>
                    </template>
                    <template v-if="props.row.tipo == 2">
                        <span class="btn btn-outline-primary"> Accesorios</span>
                    </template>
                    <template v-if="props.row.tipo == 3">
                        <span class="btn btn-outline-warning"> Impresión</span>
                    </template>
                    <template v-if="props.row.tipo == 4">
                        <span class="btn btn-outline-success"> Video</span>
                    </template>
                </template>
            </v-client-table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="CerrarModal()" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label>Fecha</label>
                            <input :disabled="!editar" type="date" class="form-control" v-model="resguardo.fecha" v-validate="'required'" data-vv-name="fecha">
                            <span class="text-danger">{{errors.first("fecha")}}</span>
                        </div>
                        <template v-if="tipoAccion == 3">
                            <div class="col-md-4 mb-3">
                                <label>Fecha retorno</label>
                                <input type="date" data-vv-name="Fecha Retorno" class="form-control" v-model="resguardo.fecha_retorno">
                            </div>
                        </template>
                        <div class="col-md-3 mb-3">
                            <label>Tipo</label>
                            <select :disabled="!editar" class="form-control" v-model="resguardo.tipo" v-on:change="ObtenerEquipos">
                                <option value="1">Computo</option>
                                <option value="2">Accesorios</option>
                                <option value="3">Impresion</option>
                                <option value="4">Video</option>
                            </select>
                            <span class="text-danger">{{errors.first("tipo")}}</span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <label>Seleccionar equipo</label>
                            <v-select :disabled="!editar" :options="list_equipos" v-model="resguardo.caiv" label="descripcion" data-vv-name="material">
                            </v-select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label>Cantidad</label>
                            <input :disabled="!editar" type="text" v-validate="'required|decimal:2'" data-vv-name="cantidad" class="form-control" v-model="resguardo.cantidad">
                            <span class="text-danger">{{errors.first("cantidad")}}</span>
                        </div>
                        <template v-if="tipoAccion == 3">
                            <div class="col-md-4 mb-3">
                                <label>Cantidad Defectuoso</label>
                                <input type="text" v-validate="'required|decimal:2'" data-vv-name="cantidad" class="form-control" v-model="resguardo.cantidad_defectuoso" @input="verificarCantidad()">
                            </div>
                        </template>
                    </div>
                    <div class="form-row">
                        <div class="col-md-8 mb-3">
                            <label>Enlistar Accesorios Adicionales</label>
                            <v-select :options="list_accesorios" v-model="resguardo.accesorios_data" label="descripcion" data-vv-name="material">
                            </v-select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Cantidad</label>
                            <input type="text" class="form-control" v-model="resguardo.cantidad_accesorio">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>&nbsp;</label><br>
                            <button :disabled="!editar" class="btn btn-outline-dark" @click="GuardarAccesorio()">Agregar</button>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-9">
                            <label><b>Descripción</b></label>
                        </div>
                        <div class="form-group col-md-2">
                            <label><b>Cantidad</b></label>
                        </div>
                        <div class="form-group col-md-1">
                            <label></label>
                        </div>
                    </div>
                    <li :key="index" v-for="(vi, index) in listado_data_accesorios" class="list-group-item">
                        <div class="form-row">
                            <div class="form-group col-md-9">
                                <label>{{vi.descripcion}}</label>
                            </div>
                            <div class="form-group col-md-2">
                                <label>{{vi.cantidad}}</label>
                            </div>
                            <div class="form-group col-md-1">
                                <a v-show="vi.temp" @click="EliminarTemporal(index, vi)">
                                    <span class="fas fa-trash"></span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <br>
                    <div class="form-row">
                        <div class="col-md-10 mb-3">
                            <label>Observaciones adicionales a la entrega</label>
                            <textarea name="name" rows="2" cols="80" v-model="resguardo.observacion_dos" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-10 mb-3">
                            <label>Observaciones adicionales a la recepción</label>
                            <textarea name="name" rows="2" cols="80" v-model="resguardo.observacion_uno" class="form-control"></textarea>
                        </div>
                    </div>

                    <template v-if="tipoAccion == 3">
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label>Observaciones al retorno</label>
                                <textarea name="name" rows="2" cols="80" v-model="resguardo.observacion_retorno" class="form-control"></textarea>
                            </div>
                        </div>
                    </template>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label>Empleado Entrega</label>
                            <v-select :options="listaEmpleadosEntrega" label="nombre" v-model="empleado_entrega" data-vv-name="Entrega" v-validate="'required'"></v-select>
                            <span class="text-danger">{{errors.first("Entrega")}}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Empleado Recibe</label>
                            <v-select :disabled="!editar" :options="listaEmpleados" label="nombre" v-model="empleado_recibe" data-vv-name="Recibe" v-validate="'required'"></v-select>
                            <span class="text-danger">{{errors.first("Recibe")}}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <vue-element-loading :active="isGuardarLoading" />
                    <button type="button" class="btn btn-outline-dark" @click="CerrarModal()"><i class="fas fa-times"></i>&nbsp;Cerrar</button>
                    <button type="button" v-if="tipoAccion == 1" class="btn btn-secondary" @click="GuardarResguardo()"><i class="fas fa-save mr-1"></i>Guardar</button>
                    <button type="button" v-if="tipoAccion == 2" class="btn btn-secondary" @click="ActualizarResguardo()"><i class="fas fa-save mr-1"></i>Actualizar</button>
                    <button type="button" v-if="tipoAccion == 3" class="btn btn-secondary" @click="GuardarRetorno()"><i class="fas fa-save mr-1"></i>Aceptar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- Modal -->

    <!-- Modal -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal_autorizar}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Autorizar Entrega de Equipo</h4>
                    <button type="button" class="close" @click="CerrarModalAutorizar()" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-8">
                            <qrcode-stream v-if="modal_autorizar" @decode="onDecodeAutorizar" @init="onInit" />
                        </div>
                        <div class="col-md-12">
                            <br>
                            <h2 style="font-family: 'Share Tech Mono', monospace;text-align: center;">{{empleado_autoriza}}</h2>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" @click="CerrarModalAutorizar()"><i class="fas fa-times"></i>&nbsp;Cerrar</button>
                    <button type="button" v-if="tipoAccion == 1" class="btn btn-secondary" @click="GuardarAutorizacion()"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- Modal Autorizar-->

</div>
</template>

<script>
var config = require("../../Herramientas/config-vuetables-client").call(this);

export default
{
    data()
    {
        return {
            listado_data_accesorios: [],
            list_accesorios: [],
            isObtenerDatos_Loading: false,
            url: "ti/resguardos",
            isGuardarLoading: false,
            empresa: 1,
            editar: true,
            list_equipos: [],
            error: "",
            listaCatalogo: [],
            resguardo:
            {
                id: 0,
                fecha: "",
                fecha_retorno:"",
                tipo: 0,
                caiv: "",
                observacion_uno: "",
                observacion_dos: "",
                check: false,
                cantidad: "",
                cantidad_temporal: "",
                empresa: "1",
                observacion_retorno: "",
                cantidad_defectuoso: 0,
                cantidad_accesorio: "",
                cantidad_accesorio_temporal: "",
                accesorios_data: "",
            },
            empleado_recibe: "",
            empleado_entrega: "",
            listaEmpleados: [],
            listaEmpleadosEntrega: [],
            modal: 0,
            tipoAccion: 0,
            tituloModal: "",
            list_vales: [],
            columns_vales: [
                "id",
                "fecha",
                "descripcion",
                "tipo",
                "observacion_dos",
                "cantidad",
                "empleado_r",
                "autorizado",
                "estado",
                "fecha_retorno",
                "descargar",
            ],
            options_vales:
            {
                headings:
                {
                    "id": "Acciones",
                    "fecha": "Fecha",
                    "accesorios": "Accesorios",
                    "observacion_dos": "Observaciones entrega",
                    "cantidad": "Cantidad",
                    "empleado_r": "Usuario asignado",
                    "estado": "Estado",
                    "tipo": "Tipo",
                    "autorizado": "Autorizado"

                }, // Headings,
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                isObtenerEquipos_loading: false,
                list_equipos: [],
                sortIcon: config.sortIcon,
                filterByColumn: true,
                listColumns:
                {
                    "estado": [
                    {
                        id: 1,
                        text: "En resguardo"
                    },
                    {
                        id: 2,
                        text: "Retornado"
                    }, ],
                    "tipo": [
                    {
                        id: 1,
                        text: "Computo"
                    },
                    {
                        id: 2,
                        text: "Accesorios"
                    },
                    {
                        id: 3,
                        text: "Impresión"
                    },
                    {
                        id: 4,
                        text: "Video"
                    }, ],
                },
                texts: config.texts
            }, //options
            // Modal autorizar
            modal_autorizar: false,
            empleado_autoriza: "",
            mostrar_qr_autorizar: false,
        }
    },
    methods:
    {
        CargarDatos(empresa)
        {
            this.empresa = empresa;
            this.CargarDatosAux();
        },

        CargarDatosAux()
        {
            this.isObtenerDatos_Loading = true;
            axios.get(this.url + "/obtener/" + this.empresa).then(res =>
            {
                this.isObtenerDatos_Loading = false;
                if (res.data.status)
                {
                    this.list_vales = res.data.resguardos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Obtener elos empleados de la empresa seleccionada
         */
        ObtenerEmpleados()
        {
            axios.get("generales/empleadoactivos/").then(res =>
            {
                if (res.data.status)
                {
                    this.listaEmpleados = res.data.empleados;
                    axios.get("generales/empleadoactual").then(res2 =>
                    {
                        if (res2.data.status)
                        {
                            if (res2.data.empleados[0].id == 150)
                                this.listaEmpleadosEntrega = [...this.listaEmpleados];
                            else
                            {
                                this.listaEmpleadosEntrega = res2.data.empleados;
                                this.empleado_entrega = res2.data.empleados[0];
                            }
                        }
                        else
                        {
                            toastr.error(res2.data.mensaje);
                        }
                    });
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Obtener lista de accesorios
         */
        ObtenerAccesorios()
        {
            axios.get(this.url + "/obteneraccesorios").then(res =>
            {
                if (res.data.status)
                {
                    this.list_accesorios = res.data.accesorios;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        AbrirModal(nuevo, data = [])
        {
            this.ObtenerEmpleados();
            this.ObtenerAccesorios();
            this.modal = 1;
            this.editar = nuevo;
            if (nuevo)
            {
                this.tituloModal = "Registrar resguardo";
                this.tipoAccion = 1;
            }
            else
            {
                this.tituloModal = "Actualizar resguardo";
                this.tipoAccion = 2;
                this.resguardo = {
                    ...data
                };
                this.resguardo.caiv = {
                    id: data.caiv,
                    descripcion: data.descripcion,
                    cantidad: data.cantidad
                };
                this.empleado_recibe = {
                    id: data.empleado_recibe,
                    nombre: data.empleado_r
                };
                this.empleado_entrega = {
                    id: data.empleado_entrega,
                    nombre: data.empleado_e
                };
                let asd = [];
                if (data.accesorios_listado != [])
                    asd = [];
                else
                    asd = JSON.parse(JSON.parse(data.accesorios_listado));
                this.listado_data_accesorios = asd;
            }
        },

        CerrarModal()
        {
            this.modal = 0;
            this.resguardo = {
                empleado_recibe:
                {},
                empleado_entrega:
                {}
            };
            this.listado_data_accesorios = [];
            this.empleado_recibe = "";
        },

        async onInit(promise)
        {
            try
            {
                await promise
            }
            catch (error)
            {
                if (error.name === 'NotAllowedError')
                {
                    this.error = "ERROR: you need to grant camera access permisson"
                }
                else if (error.name === 'NotFoundError')
                {
                    this.error = "ERROR: no camera on this device"
                }
                else if (error.name === 'NotSupportedError')
                {
                    this.error = "ERROR: secure context required (HTTPS, localhost)"
                }
                else if (error.name === 'NotReadableError')
                {
                    this.error = "ERROR: is the camera already in use?"
                }
                else if (error.name === 'OverconstrainedError')
                {
                    this.error = "ERROR: installed cameras are not suitable"
                }
                else if (error.name === 'StreamApiNotSupportedError')
                {
                    this.error = "ERROR: Stream API is not supported in this browser"
                }
            }
        },

        onDecodeAutorizar(result)
        {
            let dts = result.split("|");
            if (dts.length != 2)
            {
                toastr.warning("Nope");
                return;
            }
            let id = dts[0];

            let nombre = dts[1];
            this.empleado_autoriza = nombre;
            axios.post("ti/resguardo/autorizar",
            {
                "empleado_id": id,
                "resguardo_id": this.id_autoriza
            }).then(res =>
            {
                if (res.data.status)
                {
                    toastr.success("Autorizado correctamente");
                    setTimeout(() =>
                    {
                        this.CerrarModalAutorizar();
                        this.CargarDatosAux();
                    }, 2000);
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },
        /**
         * Guardar vale de resguardo
         */
        async GuardarResguardo()
        {
            let isValid = await this.$validator.validate();
            if (!isValid) return;
            this.isGuardarLoading = true;
            let data = new FormData();
            data.append("fecha", this.resguardo.fecha);
            data.append("tipo", this.resguardo.tipo);
            data.append("observacion_uno", this.resguardo.observacion_uno);
            data.append("observacion_dos", this.resguardo.observacion_dos);
            data.append("empleado_recibe", this.empleado_recibe.id);
            data.append("empleado_entrega", this.empleado_entrega.id);
            data.append("caiv", this.resguardo.caiv.id);
            data.append("cantidad", this.resguardo.cantidad);
            data.append("empresa", this.empresa);
            data.append("accesorios", JSON.stringify(this.listado_data_accesorios));
            axios.post(this.url + "/guardar", data).then(res =>
            {
                this.isGuardarLoading = false;
                if (res.data.status)
                {
                    toastr.success("Guardado Correctamente");
                    this.CerrarModal();
                    this.CargarDatosAux();
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        async ActualizarResguardo()
        {
            let isValid = await this.$validator.validate();
            if (!isValid) return;
            this.isGuardarLoading = true;
            let data = new FormData();
            data.append("id", this.resguardo.id);
            data.append("observacion_uno", this.resguardo.observacion_uno);
            data.append("observacion_dos", this.resguardo.observacion_dos);
            data.append("empleado_entrega", this.empleado_entrega.id);
            axios.post(this.url + "/actualizar", data).then(res =>
            {
                this.isGuardarLoading = false;
                if (res.data.status)
                {
                    toastr.success("Actualizado Correctamente");
                    this.CerrarModal();
                    this.CargarDatosAux();
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Obtener los equipos de acuerdo al tipo seleccionado
         */
        ObtenerEquipos()
        {
            this.isObtenerEquipos_loading = true;
            axios.get(this.url + "/obtenerequipos/" +
                this.resguardo.tipo).then(res =>
            {
                this.isObtenerEquipos_loading = false;
                if (res.data.status)
                {
                    this.list_equipos = res.data.list_equipos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /*
         * Regresa el equipo
         */
        Regresar(data)
        {
            this.editar = false;
            this.tituloModal = "Regresar equipo";
            this.modal = true;
            this.tipoAccion = 3;

            this.resguardo = {
                ...data
            };
            this.resguardo.caiv = {
                id: data.caiv,
                descripcion: data.descripcion,
                cantidad: data.cantidad
            };
            this.empleado_recibe = {
                id: data.empleado_recibe,
                nombre: data.empleado_r
            };
            this.empleado_entrega = {
                id: data.empleado_entrega,
                nombre: data.empleado_e
            };
            let asd = [];
            if (data.accesorios_listado != [])
                asd = [];
            else
                asd = JSON.parse(JSON.parse(data.accesorios_listado));
            this.listado_data_accesorios = asd;
            this.resguardo.cantidad_defectuoso = 0;
        },

        /**
         * Registra el retorno del equipo
         */
        async GuardarRetorno()
        {
            let isValid = this.$validator.validate();
            if (!isValid) return;
            this.isGuardarLoading = true;
            let data = new FormData();
            data.append("id", this.resguardo.id);
            data.append("fecha_retorno", this.resguardo.fecha_retorno);
            data.append("observacion_recepcion", this.resguardo.observacion_retorno);
            data.append("cantidad_defectuoso", this.resguardo.cantidad_defectuoso);

            axios.post(this.url + "/regresar", data).then(res =>
            {
                this.isGuardarLoading = false;
                if (res.data.status)
                {
                    toastr.success("Guardado correctamente");
                    this.CerrarModal();
                    this.CargarDatosAux();
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });

        },

        /**
         * Descargar el vale de resguardo
         */
        Descargar(data)
        {
            window.open(this.url + "/descagar/" + data.id);
        },

        /**
         * Guardar listado de accesorios de manera temporal
         **/
        GuardarAccesorio()
        {
            if (this.resguardo.cantidad_accesorio <= 0)
            {
                toastr.warning("Ingrese una cantidad válida");
                return;
            }
            if (this.resguardo.cantidad_accesorio > this.resguardo.accesorios_data.cantidad)
            {
                toastr.warning("Cantidad insufuciente");
                return;
            }

            this.listado_data_accesorios.push(
            {
                id: this.resguardo.accesorios_data.id,
                descripcion: this.resguardo.accesorios_data.descripcion,
                cantidad: this.resguardo.cantidad_accesorio,
                nuevo: 0,
                temp: true,
            });
            this.resguardo.accesorios_data = "";
            this.resguardo.cantidad_accesorio = "";
            this.resguardo.cantidad_accesorio_temporal = "";
        },

        /**
         * Eliminar el accesorio temporal
         */
        EliminarTemporal(index, vi)
        {
            this.listado_data_accesorios.splice(index, 1);
        },

        /**
         * Abre el modal para autorizar la entrega
         */
        AbrirModalAutorizar(entrega)
        {
            this.id_autoriza = entrega.id;
            this.modal_autorizar = true;
        },

        /**
         * Cerrar modal de autorización
         */
        CerrarModalAutorizar()
        {
            this.modal_autorizar = false;
        },
    },
    mounted()
    {

    }
}
</script>
