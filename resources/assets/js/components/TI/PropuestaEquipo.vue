<template>
<main class="main">
    <!-- Listado propuesta -->
    <div class="card" v-show="tipoCardpropuesta==1">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> PROPUESTA DE EQUIPO
            <template v-if="PermisosCRUD.Create">
                <button type="button" @click="AbrirModalpropuesta(true)" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                </button>
            </template>
        </div>
        <div class="card-body">
            <vue-element-loading :active="isObtenerpropuesta_loading" />
            <v-client-table :columns="columns_propuesta" :data="list_propuesta" :options="options_propuesta">
                <template slot="id" slot-scope="props">
                    <div class="btn-group" role="group">
                        <div class="btn-group dropup" role="group">
                            <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-grip-horizontal"></i> Acciones
                            </button>
                            <div class="dropdown-menu">
                                <button type="button" class="dropdown-item" @click="AbrirModalpropuesta(false,props.row)">
                                    <i class="fas fa-edit"></i> Detalles
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                <template slot="tipo" slot-scope="props">
                    <button class="btn btn-outline-primary" v-if="props.row.tipo==1">Cómputo</button>
                    <button class="btn btn-outline-success" v-if="props.row.tipo==2">Accesorios</button>
                    <button class="btn btn-outline-dark" v-if="props.row.tipo==3">Impresión</button>
                    <button class="btn btn-outline-warning" v-if="props.row.tipo==4">Vídeo</button>
                </template>
                <template slot="descargar" slot-scope="props">
                    <button type="button" class="btn  btn-dark" @click="Descargar(props.row.id)">
                        <i class="fas fa-download"></i>
                    </button>
                </template>
            </v-client-table>
        </div>
    </div>

    <!--Inicio del modal propuesta-->
    <div v-if="ver_modal_propuesta" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_propuesta}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_propuesta"></h4>
                        <button type="button" class="close" @click="CerrarModalpropuesta()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardarpropuesta_loading" />
                        <div>
                            <!-- Formulario -->

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Fecha</label>
                                <div class="col-md-3">
                                    <input type="date" v-validate="'required'" v-model="propuesta.fecha" class="form-control" data-vv-name="Fecha" />
                                    <span class="text-danger">{{ errors.first('Fecha') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Puesto</label>
                                <div class="col-md-9">
                                    <v-select label="nombre" :options="list_puestos" v-validate="'required'" v-model="propuesta.puesto" data-vv-name="Puesto"></v-select>
                                    <span class="text-danger">{{ errors.first('Puesto') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Necesidad Especial</label>
                                <div class="col-md-9">
                                    <input type="text" maxlength="75" minlength="3" v-validate="'required'" v-model="propuesta.necesidad_especial" class="form-control" data-vv-name="Necesidad Especial" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Necesidad Especial') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Tipo</label>
                                <div class="col-md-4">
                                    <select class="form-control" v-validate="'required'" v-model="propuesta.tipo" data-vv-name="Tipo">
                                        <option value="1">Cómputo</option>
                                        <option value="2">Accesorios</option>
                                        <option value="3">Impresión</option>
                                        <option value="4">Vídeo</option>
                                    </select>
                                    <span class="text-danger">{{ errors.first('Tipo') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Marca</label>
                                <div class="col-md-4">
                                    <input type="text" maxlength="45" minlength="2" v-validate="'required'" v-model="propuesta.marca" class="form-control" data-vv-name="Marca" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Marca') }}</span>
                                </div>
                                <label class="col-md-1 form-control-label">Modelo</label>
                                <div class="col-md-4">
                                    <input type="text" maxlength="45" minlength="3" v-validate="'required'" v-model="propuesta.modelo" class="form-control" data-vv-name="Modelo" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Modelo') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Almacenamiento</label>
                                <div class="col-md-4">
                                    <input type="text" maxlength="45" minlength="3" v-validate="'required'" v-model="propuesta.almacenamiento" class="form-control" data-vv-name="Almacenamiento" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Almacenamiento') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Procesador</label>
                                <div class="col-md-4">
                                    <input type="text" maxlength="45" minlength="3" v-validate="'required'" v-model="propuesta.procesador" class="form-control" data-vv-name="Procesador" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Procesador') }}</span>
                                </div>

                                <label class="col-md-1 form-control-label">RAM</label>
                                <div class="col-md-4">
                                    <input type="text" maxlength="45" minlength="3" v-validate="'required'" v-model="propuesta.ram" class="form-control" data-vv-name="RAM" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('RAM') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Comentarios</label>
                                <div class="col-md-9">
                                    <textarea rows="4" maxlength="45" minlength="1" v-validate="'required'" v-model="propuesta.comentarios" class="form-control" data-vv-name="Comentarios"></textarea>
                                    <span class="text-danger">{{ errors.first('Comentarios') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Accesorios Adicionales</label>
                                <div class="col-md-9">
                                    <textarea rows="5" maxlength="75" minlength="1" v-validate="'required'" v-model="propuesta.accesorios" class="form-control" data-vv-name="Accesorios Adicionales"></textarea>
                                    <span class="text-danger">{{ errors.first('Accesorios Adicionales') }}</span>
                                </div>
                            </div>

                            <div v-show="tipoAccion_propuesta==2" class="">
                                <h4>Cotizaciones</h4>
                                <template v-if="PermisosCRUD.Create">
                                    <button type="button" @click="AbrirModalcotizacion(true)" class="btn btn-dark float-sm-right">
                                        <i class="fas fa-plus"></i>&nbsp;Nuevo
                                    </button>
                                </template>
                                <br>
                                <br>
                                <vue-element-loading :active="isObtenercotizacion_loading" />
                                <v-client-table :columns="columns_cotizacion" :data="list_cotizacion" :options="options_cotizacion">
                                    <template slot="id" slot-scope="props">
                                        <div class="btn-group" role="group">
                                            <div class="btn-group dropup" role="group">
                                                <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-grip-horizontal"></i> Acciones
                                                </button>
                                                <div class="dropdown-menu">
                                                    <button type="button" class="dropdown-item" @click="AbrirModalcotizacion(false,props.row)">
                                                        <i class="fas fa-edit"></i> Actualizar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </v-client-table>
                            </div>

                            <!-- Formulario -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardarpropuesta_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalpropuesta()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                            <button type="button" v-if="tipoAccion_propuesta==1" class="btn btn-secondary" @click="Registrarpropuesta(true)">
                                <i class="fas fa-save"></i>&nbsp;Guardar
                            </button>
                            <button type="button" v-if="tipoAccion_propuesta==2" class="btn btn-secondary" @click="Registrarpropuesta(false)">
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
    <!--Fin del modal propuesta-->

    <!--Inicio del modal cotizacion-->
    <div v-if="ver_modal_cotizacion" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_cotizacion}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_cotizacion"></h4>
                        <button type="button" class="close" @click="CerrarModalcotizacion()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardarcotizacion_loading" />
                        <div>
                            <!-- Formulario -->

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Proveedor</label>
                                <div class="col-md-9">
                                    <input type="text" maxlength="45" minlength="2" v-validate="'required'" v-model="cotizacion.proveedor" class="form-control" data-vv-name="Proveedor" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Proveedor') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Marca</label>
                                <div class="col-md-9">
                                    <input type="text" maxlength="45" minlength="2" v-validate="'required'" v-model="cotizacion.marca" class="form-control" data-vv-name="Marca" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Marca') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Costo</label>
                                <div class="col-md-9">
                                    <input type="number" min="0" max="99999" v-validate="'required'" v-model="cotizacion.costo" class="form-control" data-vv-name="Costo" />
                                    <span class="text-danger">{{ errors.first('Costo') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Forma de pago</label>
                                <div class="col-md-9">
                                    <input type="text" maxlength="45" minlength="3" v-validate="'required'" v-model="cotizacion.forma_pago" class="form-control" data-vv-name="Forma de pago" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Forma de pago') }}</span>
                                </div>
                            </div>

                            <!-- Formulario -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardarcotizacion_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalcotizacion()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                            <button type="button" v-if="tipoAccion_cotizacion==1" class="btn btn-secondary" @click="Registrarcotizacion(true)">
                                <i class="fas fa-save"></i>&nbsp;Guardar
                            </button>
                            <button type="button" v-if="tipoAccion_cotizacion==2" class="btn btn-secondary" @click="Registrarcotizacion(false)">
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
    <!--Fin del modal cotizacion-->

</main>
</template>

<script>
import Utilerias from "../Herramientas/utilerias.js";
var config = require('../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            //// propuesta
            url_propuesta: "ti/propuesta",
            tipoAccion_propuesta: 1,
            tipoCardpropuesta: 1,
            PermisosCRUD:
            {},
            ver_modal_propuesta: false,
            tituloModal_propuesta: "",
            propuesta_id: 0,
            isGuardarpropuesta_loading: false,
            isObtenerpropuesta_loading: false,
            columns_propuesta: [
                "id",
                "fecha",
                "puesto_nombre",
                "necesidad_especial",
                "tipo",
                "marca",
                "modelo",
                "descargar"
            ],
            list_propuesta: [],
            list_puestos: [],
            propuesta:
            {
                tipo: 1,
                puesto:
                {},
            },
            options_propuesta:
            {
                headings:
                {
                    id: "Acciones",
                    fecha: "Fecha",
                    necesidad_especial: "Necesidad Especial",
                    tipo: "Tipo",
                    marca: "Marca",
                    modelo: "Modelo",
                    puesto_nombre:"Puesto",
                    accesorios: "Accesorios Adicionales",

                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            },

            //// cotizacion
            url_cotizacion: "ti/cotizacion",
            tipoAccion_cotizacion: 1,
            tipoCardcotizacion: 1,
            ver_modal_cotizacion: false,
            tituloModal_cotizacion: "",
            cotizacion_id: 0,
            isGuardarcotizacion_loading: false,
            isObtenercotizacion_loading: false,
            columns_cotizacion: [
                "proveedor",
                "marca",
                "costo",
                "forma_pago"
            ],
            list_cotizacion: [],
            cotizacion:
            {},
            options_cotizacion:
            {
                headings:
                {
                    proveedor: "Proveedor",
                    marca: "Marca",
                    costo: "Costo",
                    forma_pago: "Forma de pago",

                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterable: [],
                sortable: [],
                filterByColumn: true,
                texts: config.texts
            },

        }
    },
    methods:
    {
        // Metodos
        /**
         * Obtener todos los registros
         */
        Obtenerpropuesta()
        {
            this.isObtenerpropuesta_loading = true;
            axios.get(this.url_propuesta + "/obtener").then(res =>
            {
                this.isObtenerpropuesta_loading = false;
                if (res.data.status)
                {
                    this.list_propuesta = res.data.propuestas;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },
        ObtenerPuestos()
        {
            axios.get("generales/puestos").then(res =>
            {
                if (res.data.status)
                {
                    this.list_puestos = res.data.puestos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Abrir modal propuesta
         */
        AbrirModalpropuesta(nuevo, data = {})
        {
            this.ObtenerPuestos();
            this.ver_modal_propuesta = true;
            if (nuevo)
            {
                this.tituloModal_propuesta = "Registrar Propuesta de equipo";
                this.tipoAccion_propuesta = 1;
            }
            else
            {
                this.tituloModal_propuesta = "Actualizar Propuesta de equipo";
                this.tipoAccion_propuesta = 2;
                this.propuesta = {
                    ...data,
                    puesto:
                    {
                        id:data.puesto_id,
                        nombre:data.puesto_nombre
                    }
                };
                this.Obtenercotizacion();
            }
        },

        /**
         * Registrar propuesta
         */
        Registrarpropuesta(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;

                if (this.propuesta.puesto.id == null)
                {
                    toastr.warning("Seleccione un puesto");
                    return;
                }

                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.propuesta.id);
                data.append("fecha", this.propuesta.fecha);
                data.append("necesidad_especial", this.propuesta.necesidad_especial);
                data.append("tipo", this.propuesta.tipo);
                data.append("marca", this.propuesta.marca);
                data.append("puesto_id", this.propuesta.puesto.id);
                data.append("modelo", this.propuesta.modelo);
                data.append("almacenamiento", this.propuesta.almacenamiento);
                data.append("procesador", this.propuesta.procesador);
                data.append("ram", this.propuesta.ram);
                data.append("comentarios", this.propuesta.comentarios);
                data.append("accesorios", this.propuesta.accesorios);

                this.isGuardarpropuesta_loading = true;
                axios.post(this.url_propuesta + "/guardar", data).then(res =>
                {
                    this.isGuardarpropuesta_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.Obtenerpropuesta();
                        this.CerrarModalpropuesta();
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
        CerrarModalpropuesta()
        {
            this.ver_modal_propuesta = false;
            this.propuesta = {};
        },

        // Metodos
        /**
         * Obtener todos los registros
         */
        Obtenercotizacion()
        {
            this.isObtenercotizacion_loading = true;
            axios.get(this.url_cotizacion + "/obtener/" + this.propuesta.id).then(res =>
            {
                this.isObtenercotizacion_loading = false;
                if (res.data.status)
                {
                    this.list_cotizacion = res.data.cotizacion;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Abrir modal cotizacion
         */
        AbrirModalcotizacion(nuevo, data = {})
        {
            this.ver_modal_cotizacion = true;
            if (nuevo)
            {
                this.tituloModal_cotizacion = "Registrar Cotizaciones";
                this.tipoAccion_cotizacion = 1;
            }
            else
            {
                this.tituloModal_cotizacion = "Actualizar Cotizaciones";
                this.tipoAccion_cotizacion = 2;
                this.cotizacion = {
                    ...data
                };
            }
        },

        /**
         * Registrar cotizacion
         */
        Registrarcotizacion(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.cotizacion.id);
                data.append("propuesta_id", this.propuesta.id);
                data.append("proveedor", this.cotizacion.proveedor);
                data.append("marca", this.cotizacion.marca);
                data.append("costo", this.cotizacion.costo);
                data.append("forma_pago", this.cotizacion.forma_pago);

                this.isGuardarcotizacion_loading = true;
                axios.post(this.url_cotizacion + "/guardar", data).then(res =>
                {
                    this.isGuardarcotizacion_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.Obtenercotizacion();
                        this.CerrarModalcotizacion();
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
        CerrarModalcotizacion()
        {
            this.ver_modal_cotizacion = false;
            this.cotizacion = {};
        },

        /**
         * Descargar propuesta
         */
        Descargar(id)
        {
            window.open(this.url_propuesta + "/descargar/" + id);
        },

    },
    mounted()
    {
        this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
        this.Obtenerpropuesta();
    }
}
</script>
