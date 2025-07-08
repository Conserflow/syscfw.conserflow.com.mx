<template>
<main>
    <button v-show="empleado_activo" type="button" @click="AbrirModalFamiliares()" class="btn btn-dark float-sm-right">
        <i class="fas fa-plus"></i>&nbsp;Nuevo
    </button>
    <vue-element-loading :active="isObtenerFamiliares_loading" />
    <v-client-table :columns="columns_familiares" :data="list_familiares" :options="options_familiares" ref="myTabledireccion">

        <template slot="id" slot-scope="props">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <div class="btn-group dropup" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-grip-horizontal"></i>&nbsp; Acciones
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <button v-show="empleado_activo" type="button" @click="AbrirModalFamiliares(false,props.row)" class="dropdown-item">
                            <i class="fas fa-edit"></i>&nbsp;Actualizar Familiar
                        </button>
                        <button v-show="empleado_activo" type="button" @click="EliminarFamiliar(props.row)" class="dropdown-item">
                            <i class="fas fa-times"></i>&nbsp;Eliminar Familiar
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </v-client-table>

    <!--Inicio del modal agregar/actualizar-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <vue-element-loading :active="isLoading" />
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="CerrarModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardarFamiliares_loading" />
                        <div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="nombre">Nombre</label>
                                <div class="col-md-9">
                                    <input type="text" v-validate="'required|max:50'" name="Nombre" v-model="familiare.nombre" class="form-control" placeholder="Nombre Completo" autocomplete="off">
                                    <span class="text-danger">{{ errors.first("Nombre") }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="parentesco">Parentesco</label>
                                <div class="col-md-9">
                                    <select class="form-control" name="Parentesco" v-model="familiare.parentesco">
                                        <option value="PADRE">PADRE</option>
                                        <option value="MADRE">MADRE</option>
                                        <option value="HERMANO">HERMANO</option>
                                        <option value="HERMANA">HERMANA</option>
                                        <option value="CÓNYUGE">CÓNYUGE</option>

                                    </select>
                                    <span class="text-danger">{{ errors.first("Parentesco") }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="Edad">Edad</label>
                                <div class="col-md-9">
                                    <input type="number" v-validate="'required'" min="1" max="100" step="1" name="Edad" v-model="familiare.edad" class="form-control" placeholder="Edad" autocomplete="off">
                                    <span class="text-danger">{{ errors.first("Edad") }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardarFamiliares_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModal()"><i class="fas fa-window-close"></i>&nbsp;Cerrar</button>
                            <button type="button" v-show="empleado_activo" v-if="tipoAccion==1" class="btn btn-secondary" @click="GuardarFamiliares(true)"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-secondary" @click="GuardarFamiliares(false)"><i class="fas fa-save"></i>&nbsp;Actualizar</button>
                        </div>
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
import Utilerias from "../../Herramientas/utilerias.js";
var config = require("../../Herramientas/config-vuetables-client").call(this);

export default
{
    data()
    {
        return {
            url: "rh/empleados/familiares",
            empleado: null,
            empleado_activo: false,
            familiare:
            {
                nombre: "",
                parentesco: "",
                edad: 0,
                vive: "",
                condicion: 0,
                empleado_id: 0,

            },
            listaTipoNomina: [],
            listadescuento: [],
            listaTipoDescuento: [],
            listaHorarios: [],
            listaProyectos: [],
            modal: 0,
            tituloModal: "",
            tipoAccion: 0,
            disabled: 0,
            isLoading: false,
            isGuardarFamiliares_loading: false,
            isObtenerFamiliares_loading: false,
            columns_familiares: ["id", "nombre", "parentesco", "edad"],
            list_familiares: [],
            options_familiares:
            {
                headings:
                {
                    id: "Acciones",
                    nombre: "Nombre Completo",
                    parentesco: "Parentesco",
                    edad: "Edad",
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                listColumns:
                {
                    condicion: config.columnCondicion
                },
                texts: config.texts
            },
        }
    },
    computed:
    {},
    methods:
    {
        /**
         * Registra o actualiza el familiar ingresado
         */
        async GuardarFamiliares(nuevo)
        {
            let isValid = await this.$validator.validate();
            if (!isValid) return;
            this.isGuardarFamiliares_loading = true;
            let data = new FormData();

            if (!nuevo) data.append("id", this.familiare.id)
            data.append("nombre", this.familiare.nombre);
            data.append("parentesco", this.familiare.parentesco);
            data.append("edad", this.familiare.edad);
            data.append("vive", 1);
            data.append("empleado_id", this.familiare.empleado_id);
            axios.post(this.url + "/guardar", data).then(res =>
            {
                this.isGuardarFamiliares_loading = false;
                if (res.data.status)
                {
                    toastr.success("Guardado correctamente");
                    this.CargarFamiliares(
                    {
                        ...this.empleado
                    });
                    this.CerrarModal();
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Cerrar modal
         */
        CerrarModal()
        {
            this.modal = 0;
            this.tituloModal = "";
            Utilerias.resetObject(this.familiare);
        },

        /**
         * Abrir modal para registro/actualización
         */
        AbrirModalFamiliares(nuevo = true, data = [])
        {
            this.modal = 1;
            if (nuevo)
            {
                this.familiare.parentesco = "PADRE";
                this.familiare.empleado_id = this.empleado.id;
                this.tituloModal = "Registrar Familiar";
                this.tipoAccion = 1;
            }
            else
            {
                this.tituloModal = "Actualizar Familiar";
                this.tipoAccion = 2;
                this.familiare = {
                    ...data
                };
            }

        },
        /**
         * Obtener los familiares del empleado
         */
        CargarFamiliares(empleado = [])
        {
            this.empleado = empleado;
            this.empleado_activo = empleado.condicion;
            this.isObtenerFamiliares_loading = true;
            axios.get(this.url + "/obtener/" + empleado.id).then(res =>
            {
                if (res.data.status)
                {
                    this.list_familiares = res.data.familiares;
                    this.isObtenerFamiliares_loading = false;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Eliminar familiar
         */
        EliminarFamiliar(row)
        {
            this.isObtenerFamiliares_loading = true;
            let data = new FormData();
            data.append("id", row.id);
            axios.post(this.url + "/eliminar", data).then(res =>
            {
                if (res.data.status)
                {
                    toastr.success("Familiar eliminado correctamente");
                    this.CargarFamiliares(
                    {
                        id: this.empleado.id
                    });
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },
    },
    mounted()
    {}
}
</script>
