<template>
<div>
    <div class="card">
        <div class="card-body">
            <v-client-table :data="tableData" :columns="columns" :options="options">
                <template slot="id" slot-scope="props">
                    <div class="btn-group" role="group">
                        <button id="btn_id" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-grip-horizontal"></i>&nbsp;
                        </button>
                        <div class="dropdown-menu">
                            <template>
                                <button type="button" @click="AbrirModal(false, props.row)" class="dropdown-item">
                                    <i class="fas fa-pencil"></i>&nbsp;Actualizar
                                </button>
                                <button type="button" @click="retorno(props.row.id)" class="dropdown-item">
                                    <i class="fas fa-trash"></i>&nbsp;Retornar
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
                <template slot="descargar" slot-scope="props">
                    <button type="button" @click="descargar(props.row.id)" class="btn btn-outline-dark">
                        <i class="fas fa-download"></i>&nbsp;
                    </button>
                </template>
                <template slot="condicion" slot-scope="props">
                    <template v-if="props.row.condicion == 1">
                        <button class="btn btn-outline-success">En sitio</button>
                    </template>
                    <template v-if="props.row.condicion == 2">
                        <button class="btn btn-outline-primary">Retornado</button>
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
                        <div class="col-md-5 mb-3">
                            <label>Proyecto</label>
                            <v-select :disables="!editar" :options="listaProyectos" v-model="sitio.proyecto" label="nombre_corto" data-vv-name="Proyecto" v-validate="'required'"></v-select>
                            <span class="text-danger">{{errors.first("Proyecto")}}</span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Fecha Salida</label>
                            <input :disables="!editar" type="date" class="form-control" v-model="sitio.fecha_salida" data-vv-name="Fecha Salida" v-validate="'required'">
                            <span class="text-danger">{{errors.first("Fecha Salida")}}</span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label>Tipo</label>
                            <select class="form-control" v-model="sitio.tipo" v-on:change="ObtenerEquipos">
                                <option value="1">Computo</option>
                                <option value="2">Accesorios</option>
                                <option value="3">Impresion</option>
                                <option value="4">Video</option>
                            </select>
                            <span class="text-danger">{{errors.first("tipo")}}</span>
                        </div>
                        <div class="col-md-9 mb-3">
                            <label>Seleccione</label>
                            <v-select :options="list_equipos" v-model="catalogo" label="descripcion" data-vv-name="catalogo" @input="asignar()">
                            </v-select>
                            <!-- v-validate="'required'" -->
                            <span class="text-danger">{{errors.first("catalogo")}}</span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-2 mb-3">
                            <label>Cantidad</label>
                            <input type="text" class="form-control" v-model="cantidad_temp">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Unidad</label>
                            <input type="text" class="form-control" v-model="unidad_temp">
                        </div>
                        <div class="col-md-1 mb-3">
                            <label>&nbsp;</label><br>
                            <button @click="guardarasignacion()" class="btn btn-outline-dark" name="button">Crear</button>
                        </div>
                    </div>
                    <hr>
                    <div class="form-row">
                        <div class="form-group col-md-7">
                            <label><b>Articulo</b></label>
                        </div>
                        <div class="form-group col-md-2">
                            <label><b>Cantidad</b></label>
                        </div>
                        <div class="form-group col-md-2">
                            <label><b>Unidad</b></label>
                        </div>
                        <div class="form-group col-md-1">
                            <label><b>.</b></label>
                        </div>
                    </div>
                    <li :key="index" v-for="(vi, index) in listado_data" class="list-group-item">
                        <div class="form-row">

                            <div class="form-group col-md-7">
                                <label>{{vi.descripcion}}</label>
                            </div>
                            <div class="form-group col-md-2">
                                <label>{{vi.cantidad}}</label>
                            </div>
                            <div class="form-group col-md-2">
                                <label>{{vi.unidad}}</label>
                            </div>
                            <div class="form-group col-md-1">
                                <a v-show="vi.temp" @click="deleteu(index)">
                                    <span class="fas fa-trash" arial-hidden="true"></span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <hr>
                    <div class="form-row">

                        <div class="col-md-6 mb-3">
                            <label>Solicita</label>
                            <v-select :options="listaEmpleados" label="nombre" v-model="empleado_recibe" data-vv-name="Recibe" v-validate="'required'"></v-select>
                            <span class="text-danger">{{errors.first("Recibe")}}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" @click="CerrarModal()"><i class="fas fa-times"></i>&nbsp;Cerrar</button>
                    <button type="button" v-if="tipoAccion == 1" class="btn btn-secondary" @click="Guardar(1)"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                    <button type="button" v-if="tipoAccion == 2" class="btn btn-secondary" @click="Guardar(0)"><i class="fas fa-save"></i>&nbsp;Actualizar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- Modal -->

    <!-- Modal -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal_retorno}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Material a retornar</h4>
                    <button type="button" class="close" @click="CerrarModalRetorno()" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-7">
                            <label><b>Articulo</b></label>
                        </div>
                        <div class="form-group col-md-2">
                            <label><b>Cantidad</b></label>
                        </div>
                        <div class="form-group col-md-2">
                            <label><b>Unidad</b></label>
                        </div>
                        <div class="form-group col-md-1">
                            <label><b>.</b></label>
                        </div>
                    </div>
                    <li :key="index" v-for="(vi, index) in listado_data" class="list-group-item">
                        <div class="form-row">

                            <div class="form-group col-md-7">
                                <label>{{vi.descripcion}}</label>
                            </div>
                            <div class="form-group col-md-2">
                                <label>{{vi.cantidad}}</label>
                            </div>
                            <div class="form-group col-md-2">
                                <label>{{vi.unidad}}</label>
                            </div>
                            <div class="form-group col-md-1">

                                <button class="btn btn-outline-success" @click="retornado(vi,2)">Si&nbsp;</button>
                                <button class="btn btn-outline-danger" @click="retornado(vi,0)">No</button>

                            </div>
                        </div>
                    </li>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" @click="CerrarModalRetorno()"><i class="fas fa-times"></i>&nbsp;Cerrar</button>
                    <button type="button" class="btn btn-secondary" @click="GuardarRetorno(1)"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin modal -->

</div>
</template>

<script>
import Utilerias from "../../Herramientas/utilerias.js";
var config = require("../../Herramientas/config-vuetables-client").call(this);
import
{
    QrcodeStream
}
from "vue-qrcode-reader";

export default
{
    data()
    {
        return {
            empresa: "1",
            error: "",
            editar: true,
            salida_sitio_id: 0,
            tituloModal: "",
            modal: 0,
            tipoAccion: 0,

            modal_retorno: 0,

            sitio:
            {
                id: 0,
                proyecto: "",
                fecha_salida: "",
                fecha_retorno: "",
                solicita: "",
                check: false,
                tipo: "",
                empresa: "1",
            },
            cantidad_temp: "",
            unidad_temp: "",
            catalogo: "",
            list_equipos: [],
            empleado_recibe: "",

            listaEmpleados: [],
            listaProyectos: [],
            empleado_entrega: [],
            listaCatalogo: [],
            listado_data: [],

            tableData: [],
            columns: ["id", "fecha_salida", "nombre_corto", "solicita_empleado", "descargar", "condicion"],
            options:
            {
                headings:
                {
                    "id": "Acciones",
                    "nombre_corto": "Proyecto"
                }, // Headings,
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                listColumns:
                {
                    "condicion": [
                    {
                        id: 1,
                        text: "En sitio"
                    },
                    {
                        id: 2,
                        text: "Retornado"
                    }, ],
                },
                texts: config.texts
            }, //options
        }
    },
    methods:
    {
        async onInit(promise)
        {
            try
            {
                await promise
            }
            catch (error)
            {
                if (error.name === "NotAllowedError")
                {
                    this.error = "ERROR: you need to grant camera access permisson"
                }
                else if (error.name === "NotFoundError")
                {
                    this.error = "ERROR: no camera on this device"
                }
                else if (error.name === "NotSupportedError")
                {
                    this.error = "ERROR: secure context required (HTTPS, localhost)"
                }
                else if (error.name === "NotReadableError")
                {
                    this.error = "ERROR: is the camera already in use?"
                }
                else if (error.name === "OverconstrainedError")
                {
                    this.error = "ERROR: installed cameras are not suitable"
                }
                else if (error.name === "StreamApiNotSupportedError")
                {
                    this.error = "ERROR: Stream API is not supported in this browser"
                }
            }
        },

        onDecode(result)
        {
            this.result = result
            var porciones = this.result.split("|");
            this.empleado_recibe = {
                id: porciones[0],
                name: porciones[1]
            };
        },

        /**
         * Obtener las salidas a sitio
         */
        CargarDatos(empresa = 1)
        {
            this.empresa = empresa;
            this.CargarDatosAux();
        },

        CargarDatosAux()
        {
            axios.get("ti/sitio/obtener/" + this.empresa).then(response =>
            {
                this.tableData = response.data;
            });
        },

        /**
         * Obtener los empledos y proyectos
         */
        getData()
        {
            this.listaEmpleados = [];
            axios.get("generales/empleadoactivos").then(res =>
            {

                this.listaEmpleados = res.data.empleados;
            });

            axios.get("generales/proyectos/1").then(res =>
            {
                if (res.data.status)
                {
                    this.listaProyectos = res.data.proyectos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        vaciar()
        {
            this.sitio.proyecto = "";
            this.sitio.fecha_salida = "";
            this.empleado_recibe = "";
            this.sitio.tipo = "";
            this.listado_data = [];
        },

        AbrirModal(nuevo, data = [])
        {
            this.getData();
            this.modal = 1;
            if (nuevo)
            {
                this.tituloModal = "Guardar";
                this.tipoAccion = 1;
                this.editar = true;
            }
            else
            {
                this.editar = false;
                this.editar = false;
                this.tituloModal = "Actualizar";
                this.tipoAccion = 2;
                this.sitio = {
                    ...data,
                    proyecto:
                    {
                        id: data["proyecto_id"],
                        nombre_corto: data["nombre_corto"]
                    },
                };
                this.empleado_recibe = {
                    id: data["solicita"],
                    nombre: data["solicita_empleado"]
                };

                axios.get("ti/sitio/obtenerpartidas/" + data["id"]).then(response =>
                {
                    response.data.forEach((item, i) =>
                    {
                        this.listado_data.push(
                        {
                            id: item["data"]["material_id"],
                            descripcion: item["descripcion"],
                            cantidad: item["data"]["cantidad"],
                            unidad: item["data"]["unidad"],
                            tipo: item["data"]["tipo"],
                        });
                    });
                }).catch(e =>
                {
                    console.error(e);
                });
            }
        },

        CerrarModal()
        {
            this.modal = 0;
            this.listado_data=[];
        },

        asignar()
        {
            if (this.catalogo != "")
            {
                this.cantidad_temp = this.catalogo.cantidad;
            }
        },

        guardarasignacion()
        {
            if (this.catalogo != "")
            {
                this.listado_data.push(
                {
                    id: this.catalogo.id,
                    descripcion: this.catalogo.descripcion,
                    cantidad: this.cantidad_temp,
                    unidad: this.unidad_temp,
                    tipo: this.sitio.tipo,
                    temp: true,
                }, );

                // this.listado_id.push(this.catalogo.id);
                // this.listado_supervisor.push(this.supervisor.id);
                this.catalogo = "";
                this.cantidad_temp = "";
                this.unidad_temp = "";
                // this.sitio.tipo = "";
            }
        },

        deleteu(index)
        {
            this.listado_data.splice(index, 1);
        },

        /**
         * Guardar salida a sitio
         */
        async Guardar(nuevo)
        {
            let isValid = this.$validator.validate();
            if (!isValid) return;

            let data = new FormData();
            if (!nuevo)
                data.append("id", this.sitio.id);
            data.append("proyecto", this.sitio.proyecto.id);
            data.append("fecha_salida", this.sitio.fecha_salida);
            data.append("solicita", this.empleado_recibe.id);
            data.append("data", JSON.stringify(this.listado_data.filter(e=>e.temp)));
            data.append("empresa", this.empresa);
            axios.post("ti/sitio/guardar", data).then(res =>
            {

                if (res.data.status)
                {
                    toastr.success("Actualizado Correctamente");
                    this.CerrarModal();
                    this.CargarDatosAux();
                }
                else
                {
                    toastr.warning(res.data.mensaje);
                }
            });

        },

        descargar(id)
        {
            window.open("ti/sitio/descargar/" + id, "_blank");
        },

        retorno(id)
        {
            this.salida_sitio_id = id;
            this.listado_data = [];
            axios.get("ti/sitio/obtenerpendeintes/" + id).then(response =>
            {
                response.data.forEach((item, i) =>
                {
                    this.listado_data.push(
                    {
                        id: item["data"]["material_id"],
                        descripcion: item["descripcion"],
                        cantidad: item["data"]["cantidad"],
                        unidad: item["data"]["unidad"],
                        tipo: item["data"]["tipo"],
                    });
                });
            }).catch(e =>
            {
                console.error(e);
            });

            this.modal_retorno = 1;
        },

        CerrarModalRetorno()
        {
            this.modal_retorno = 0;
        },

        limpiar()
        {
            this.listaCatalogo = [];
        },

        retornado(data, estado)
        {
            axios.get("ti/sitio/regresarpartida/" + data.id + "&" + this.salida_sitio_id + "&" + estado).then(response =>
            {
                toastr.success("Correcto !!!");
                this.retorno(this.salida_sitio_id);
                this.CargarDatos(this.empresa);
                this.sitio.tipo = "";
                this.listaCatalogo = [];
            }).catch(e =>
            {
                console.error(e);
            });
        },

        /**
         * Obtener los equipos de acuerdo al tipo seleccionado
         */
        ObtenerEquipos()
        {
            axios.get("ti/resguardos/obtenerequipos/" + this.sitio.tipo).then(res =>
            {
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

    },
    mounted()
    {},
}
</script>
