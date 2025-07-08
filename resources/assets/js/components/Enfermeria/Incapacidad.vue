<template>
<main class="main">
    <!-- Listado Incapacidad -->
    <div class="card" v-show="tipoCardIncapacidad==1">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Incapacidad
            <template v-if="PermisosCRUD.Create">
                <button type="button" @click="AbrirModalIncapacidad(true)" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                </button>
            </template>
            <template v-if="PermisosCRUD.Download">
                <button type="button" @click="Descargar()" class="btn btn-dark float-sm-right mr-1">
                    <i class="fas fa-download"></i>&nbsp;Descargar
                </button>
            </template>
        </div>
        <div class="card-body">
            <vue-element-loading :active="isObtenerincapacidad_loading" />
            <v-client-table :columns="columns_incapacidad" :data="list_incapacidad" :options="options_incapacidad">
                <template slot="id" slot-scope="props">
                    <div class="btn-group" role="group">
                        <div class="btn-group dropup" role="group">
                            <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-grip-horizontal"></i> Acciones
                            </button>
                            <div class="dropdown-menu">
                                <button v-if="PermisosCRUD.Update" type="button" class="dropdown-item" @click="AbrirModalIncapacidad(false,props.row)">
                                    <i class="fas fa-edit"></i> Actualizar
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                <template slot="subsecuente" slot-scope="props">
                    <p>{{props.row.subsecuente}}A</p>
                </template>
                <template slot="tipo" slot-scope="props">

                    <button class="btn btn-success" v-if="props.row.tipo==1"> Accidente Menor </button>
                    <button class="btn btn-danger" v-if="props.row.tipo==2"> Accidente de Trabajo </button>
                    <button class="btn btn-warning" v-if="props.row.tipo==3"> Enfermedad General </button>
                    <button class="btn btn-secondary" v-if="props.row.tipo==4"> Fallecimiento Familiar </button>
                    <button class="btn btn-primary" v-if="props.row.tipo==5"> Maternidad </button>
                    <button class="btn btn-primary" v-if="props.row.tipo==6"> Paternidad </button>
                    <button class="btn btn-dark" v-if="props.row.tipo==7"> Trayecto Trabajo </button>
                </template>
            </v-client-table>
        </div>
    </div>

    <!--Inicio del modal Incapacidad-->
    <div v-if="ver_modal_incapacidad" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_incapacidad}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_incapacidad"></h4>
                        <button type="button" class="close" @click="CerrarModalIncapacidad()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardarincapacidad_loading" />
                        <div>
                            <!-- Formulario -->

                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Empleado</label>
                                <div class="col-md-7">
                                    <v-select :disabled="tipoAccion_incapacidad==2" label="nombre" :options="list_empleados" v-validate="'required'" v-model="incapacidad.empleado" data-vv-name="Empleado"></v-select>
                                    <span class="text-danger">{{ errors.first('Empleado') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Puesto</label>
                                <div class="col-md-6">
                                    <input disabled class="form-control" type="text" v-model="incapacidad.empleado.puesto" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Días totales</label>
                                <div class="col-md-3">
                                    <input type="number" v-validate="'required'" v-model="incapacidad.total_dias" class="form-control" data-vv-name="Días totales" />
                                    <span class="text-danger">{{ errors.first('Días totales') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">

                                <label class="col-md-2 form-control-label">Fecha Inicio</label>
                                <div class="col-md-3">
                                    <input type="date" v-validate="'required'" v-model="incapacidad.fecha_inicio" class="form-control" data-vv-name="Fecha Inicio" />
                                    <span class="text-danger">{{ errors.first('Fecha Inicio') }}</span>
                                </div>
                                <label class="col-md-2 form-control-label">Fecha Término</label>
                                <div class="col-md-3">
                                    <input type="date" v-validate="'required'" v-model="incapacidad.fecha_termino" class="form-control" data-vv-name="Fecha Termino" />
                                    <span class="text-danger">{{ errors.first('Fecha Termino') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Subsecuente</label>
                                <div class="col-md-3">
                                    <select v-validate="'required'" v-model="incapacidad.subsecuente" class="form-control" data-vv-name="Subsecuente">
                                        <option value="1">1A</option>
                                        <option value="2">2A</option>
                                        <option value="3">3A</option>
                                        <option value="4">4A</option>
                                        <option value="5">5A</option>
                                        <option value="6">6A</option>
                                        <option value="7">7A</option>
                                        <option value="8">8A</option>
                                        <option value="9">9A</option>
                                        <option value="10">10A</option>
                                    </select>
                                    <span class="text-danger">{{ errors.first('Subsecuente') }}</span>
                                </div>

                                <label class="col-md-2 form-control-label">Tipo</label>
                                <div class="col-md-4">
                                    <select v-validate="'required'" v-model="incapacidad.tipo" class="form-control" data-vv-name="Tipo">
                                        <option value="1">Accidente Menor</option>
                                        <option value="2">Accidente de Trabajo</option>
                                        <option value="3">Enfermedad General</option>
                                        <option value="4">Fallecimiento Familiar</option>
                                        <option value="5">Maternidad</option>
                                        <option value="6">Paternidad</option>
                                        <option value="7">Trayecto Trabajo</option>
                                    </select>
                                    <span class="text-danger">{{ errors.first('Tipo') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Causa</label>
                                <div class="col-md-9">
                                    <textarea rows="4" v-validate="'required'" v-model="incapacidad.causa" class="form-control" data-vv-name="Causa">
                                    </textarea>
                                    <span class="text-danger">{{ errors.first('Causa') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Dias de incapacidad</label>
                                <div class="col-md-3">
                                    <input type="number" v-validate="'required'" v-model="incapacidad.dias_incapacidad" class="form-control" data-vv-name="Dias de incapacidad" />
                                    <span class="text-danger">{{ errors.first('Dias de incapacidad') }}</span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-12">
                                    <label class="form-control-label">Estado</label>
                                    <div class="form-control">
                                        <label v-show="tipoAccion_incapacidad==2" for="estado">{{estado_anterior}}</label>
                                        <textarea style="border:none" rows="4" name="estado" v-validate="'required'" v-model="incapacidad.estado" class="form-control" data-vv-name="Estado"></textarea>
                                    </div>
                                    <span class="text-danger">{{ errors.first('Estado') }}</span>
                                </div>
                            </div>

                            <!-- Formulario -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardarincapacidad_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalIncapacidad()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                            <button type="button" v-if="tipoAccion_incapacidad==1" class="btn btn-secondary" @click="RegistrarIncapacidad(true)">
                                <i class="fas fa-save"></i>&nbsp;Guardar
                            </button>
                            <button type="button" v-if="tipoAccion_incapacidad==2" class="btn btn-secondary" @click="RegistrarIncapacidad(false)">
                                <i class="fas fa-save"></i>&nbsp;Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal Incapacidad-->

</main>
</template>

<script>
import Utilerias from '../Herramientas/utilerias.js';
var config = require('../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            //// Incapacidad
            url_incapacidad: "enfermeria/incapacidad",
            tipoAccion_incapacidad: 1,
            tipoCardIncapacidad: 1,
            PermisosCRUD:
            {},
            ver_modal_incapacidad: false,
            tituloModal_incapacidad: "",
            incapacidad_id: 0,
            isGuardarincapacidad_loading: false,
            isObtenerincapacidad_loading: false,
            columns_incapacidad: [
                "id",
                "empleado",
                "fecha_inicio",
                "fecha_termino",
                "subsecuente",
                "tipo",
            ],
            list_incapacidad: [],
            list_empleados: [],
            estado_anterior: "",
            incapacidad:
            {
                empleado:
                {},
            },
            options_incapacidad:
            {
                headings:
                {
                    id: "Acciones",
                    empleado: "Empleado",
                    total_dias: "Días totales",
                    fecha_termino: "Fecha Termino",
                    dias_incapacidad: "Dias de incapacidad",
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
    methods:
    {
        // Metodos

        /**
         * Obtener empleados
         */
        ObtenerEmpleados()
        {
            axios.get("generales/empleadosgenerales").then(res =>
            {
                if (res.data.status)
                {
                    this.list_empleados = res.data.empleados;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Obtener todos los registros
         */
        ObtenerIncapacidad()
        {
            this.isObtenerincapacidad_loading = true;
            axios.get(this.url_incapacidad + "/obtener").then(res =>
            {
                this.isObtenerincapacidad_loading = false;
                if (res.data.status)
                {
                    this.list_incapacidad = res.data.incapacidad;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Abrir modal Incapacidad
         */
        AbrirModalIncapacidad(nuevo, data = {})
        {
            this.ObtenerEmpleados();
            this.ver_modal_incapacidad = true;
            if (nuevo)
            {
                this.tituloModal_incapacidad = "Registrar Incapacidad";
                this.tipoAccion_incapacidad = 1;
            }
            else
            {
                this.tituloModal_incapacidad = "Actualizar Incapacidad";
                this.tipoAccion_incapacidad = 2;
                this.incapacidad = {
                    ...data,
                    estado: "",
                    empleado:
                    {
                        id: data.empleado_id,
                        nombre: data.empleado,
                        puesto: data.puesto,
                        puesto_id: data.puesto_id,
                    }
                };
                this.estado_anterior = data.estado;
            }
        },

        /**
         * Registrar Incapacidad
         */
        RegistrarIncapacidad(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.incapacidad.id);
                data.append("empleado_id", this.incapacidad.empleado.id);
                data.append("puesto_id", this.incapacidad.empleado.puesto_id);
                data.append("total_dias", this.incapacidad.total_dias);
                data.append("fecha_inicio", this.incapacidad.fecha_inicio);
                data.append("fecha_termino", this.incapacidad.fecha_termino);
                data.append("subsecuente", this.incapacidad.subsecuente);
                data.append("tipo", this.incapacidad.tipo);
                data.append("causa", this.incapacidad.causa);
                data.append("dias_incapacidad", this.incapacidad.dias_incapacidad);
                data.append("estado", this.incapacidad.estado);

                // this.isGuardarincapacidad_loading = true;
                axios.post(this.url_incapacidad + "/guardar", data).then(res =>
                {
                    this.isGuardarincapacidad_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.ObtenerIncapacidad();
                        this.CerrarModalIncapacidad();
                    }
                    else
                    {
                        toastr.error(res.data.mensaje);
                    }
                })

            })
        },

        /**
         * Cerrar modal
         */
        CerrarModalIncapacidad()
        {
            this.ver_modal_incapacidad = false;
            this.estado_anterior = "";
            this.incapacidad = {
                empleado:
                {}
            };
        },

        /**
         * Descargar reporte de incapacidad
         */
        Descargar()
        {
            window.open(this.url_incapacidad + "/descargar");
        }

    },
    mounted()
    {
        this.ObtenerIncapacidad();
        this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
    }
}
</script>
