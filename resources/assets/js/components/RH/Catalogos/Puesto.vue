<template>
<main class="main">
    <div class="container-fluid">
        <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Puestos
                <button type="button" @click="AbrirModalPuestos()" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                </button>
                <button v-show="PermisosCRUD.Download" type="button" @click="descargar" class="btn btn-dark float-sm-right">
                    <i class="fas fa-download mr-1"></i>Descargar
                </button>
            </div>
            <div class="card-body">
                <v-client-table :columns="columns_puestos" :data="list_puestos" :options="options_puestos">
                    <template slot="id" slot-scope="props">
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group dropup" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i> Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <button type="button" @click="AbrirModalPuestos(false,props.row)" class="dropdown-item">
                                        <i class="fas fa-edit mr-1"></i>Actualizar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </v-client-table>

            </div>
        </div>
        <!-- Fin ejemplo de tabla Listado -->
    </div>

    <!--Inicio del modal agregar/actualizar-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <vue-element-loading :active="isGuardar_Loading" />
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>

                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="CerrarModalPuestos()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="nombre">Nombre</label>
                            <div class="col-md-9">
                                <input type="text" v-validate="'required'" name="nombre" v-model="puesto.nombre" class="form-control" placeholder="Nombre del puesto" autocomplete="off" id="nombre">
                                <span class="text-danger">{{ errors.first("nombre") }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="area">Area</label>
                            <div class="col-md-9">
                                <input v-validate="'required'" type="text" :maxlength="45" name="Area" v-model="puesto.area" class="form-control" placeholder="Area" autocomplete="off" id="area">
                                <span class="text-danger">{{ errors.first("Area") }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="departamento_id">Departamento</label>
                            <div class="col-md-9">
                                <select v-validate="'required'" class="form-control" id="departamento_id" name="departamento_id" v-model="puesto.departamento_id" data-vv-as="Departamento">
                                    <option v-for="item in listaDepartamentos" :value="item.id" :key="item.id">{{ item.nombre }}</option>
                                </select>
                                <span class="text-danger">{{ errors.first("departamento_id") }}</span>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" @click="CerrarModalPuestos()"><i class="fas fa-window-close"></i>&nbsp;Cerrar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-secondary" @click="GuardarPuesto(1)"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-secondary" @click="GuardarPuesto(0)"><i class="fas fa-save"></i>&nbsp;Actualizar</button>
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
var config = require("../../Herramientas/config-vuetables-client").call(this);
import Utilerias from '../../Herramientas/utilerias.js';
export default
{
    data()
    {
        return {
            PermisosCRUD:{},
            puesto:
            {
                id: 0,
                departamento_id: 1,
                nombre: "",
                area: ""
            },
            listaDepartamentos: [],
            listaP: [],
            modal: 0,
            tituloModal: "",
            tipoAccion: 0,
            isGuardar_Loading: false,
            columns_puestos: ["id", "nombre", "departamento", "area", "direccion", ],
            list_puestos: [],
            options_puestos:
            {
                headings:
                {
                    nombre: "Nombre",
                    departamento: "Departamento",
                    area: "Area",
                    direccion: "Dirección",
                    id: "Acciones",
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
    methods:
    {
        /**
         * Obtener todos los puestos
         */
        ObtenerPuestos()
        {
            axios.get("rh/catalogos/puestos").then(res =>
            {
                if (res.data.status)
                {
                    this.list_puestos = res.data.puestos;
                    this.listaP = res.data.puestos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Obtiene todos los departamentos registrados
         */
        ObtenerDepartamentos()
        {
            axios.get("rh/catalogos/departamento/obtener").then(res =>
            {
                if (res.data.status)
                    this.listaDepartamentos = res.data.departamentos;
                else toastr.error(res.data.mensaje);
            });
        },

        /**
         * Guardar puesto
         */
        async GuardarPuesto(nuevo)
        {
            let isValid = await this.$validator.validate();
            if (!isValid) return;

            this.isGuardar_Loading = true;
            let data = new FormData();
            if (!nuevo)
                data.append("id", this.puesto.id);
            data.append("nombre", this.puesto.nombre);
            data.append("area", this.puesto.area);
            data.append("departamento_id", this.puesto.departamento_id);

            axios.post("rh/catalogos/puestos/guardar", data).then(res =>
            {
                this.isGuardar_Loading = false;
                if (res.data.status)
                {
                    let msg = nuevo ? "Puesto Registrado Correctamente" : "Puesto Actualizado Correctamente";
                    toastr.success(msg);
                    this.CerrarModalPuestos();
                    this.ObtenerPuestos();
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })

        },

        /**
         * Cerrar modal
         */
        CerrarModalPuestos()
        {
            this.modal = 0;
            this.puesto = {};
        },

        /**
         * Abrir modal 
         */
        AbrirModalPuestos(nuevo = true, puesto = {})
        {
            this.modal = 1;
            this.ObtenerDepartamentos();
            if (nuevo)
            {
                this.tituloModal = "Registrar puesto";
                this.tipoAccion = 1;
            }
            else
            {
                this.tituloModal = "Actualizar puesto";
                this.tipoAccion = 2;
                this.puesto = {
                    ...puesto
                };
            }
        },
        descargar()
        {
            window.open("rh/catalogos/puestos/descargar");
        }
    },
    mounted()
    {
        this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
        this.ObtenerPuestos();
    }
}
</script>
