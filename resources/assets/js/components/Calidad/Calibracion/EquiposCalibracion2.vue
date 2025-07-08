<template>
<main class="main">
    
    <!-- Listado Equipos -->
    <div class="card" v-show="tipoCardEquipos==1">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> EQUIPOS DE CALIBRACIÓN
            <template v-if="PermisosCRUD.Create">
                <button type="button" @click="AbrirModalEquipos(true)" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                </button>
                <button v-if="PermisosCRUD.Download" type="button" class="btn btn-dark float-sm-right mr-1" data-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">
                    <i class="fas fa-download mr-1"></i> Descargar
                </button>
            </template>
        </div>
        <div class="card-body">
            <div class="collapse multi-collapse" id="multiCollapseExample1">
                <div class="container mb-4 row">
                    <div class="col-md-6">
                        <label>Tipo</label>
                        <v-select v-model="tipos_descargar" multiple :options="tipo_equipos" label="nombre"></v-select>
                        <button @click="DescargarReporte" class="btn btn-dark mt-2">Generar</button>
                    </div>
                </div>
            </div>

            <vue-element-loading :active="isObtenerequipos_loading" />
            <v-client-table :columns="columns_equipos" :data="list_equipos" :options="options_equipos">
                <template slot="id" slot-scope="props">
                    <div class="btn-group" role="group">
                        <div class="btn-group dropup" role="group">
                            <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-grip-horizontal"></i> Acciones
                            </button>
                            <div class="dropdown-menu">
                                <button v-if="PermisosCRUD.Update" type="button" class="dropdown-item" @click="AbrirModalEquipos(false,props.row)">
                                    <i class="fas fa-edit"></i> Actualizar
                                </button>
                                <button v-if="PermisosCRUD.Create" type="button" class="dropdown-item" @click="AbrirModalCalibraciones(props.row)">
                                    <i class="fas fa-plus"></i> Registrar Calibración
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                <template slot="condicion" slot-scope="props">
                    <template v-if="props.row.condicion==1">
                        <button class="btn btn-outline-success">Activo</button>
                    </template>
                    <template v-else>
                        <button class="btn btn-outline-danger">Inactivo</button>
                    </template>
                </template>
                <template slot="proxima_fecha" slot-scope="props">
                    <template v-if="props.row.proxima_fecha==0">
                        <p>N/D</p>
                    </template>
                    <template v-else>
                        <p>{{props.row.proxima_fecha}}</p>
                    </template>
                </template>
                <template slot="fecha_servicio" slot-scope="props">
                    <template v-if="props.row.fecha_servicio==0">
                        <p>N/D</p>
                    </template>
                    <template v-else>
                        <p>{{props.row.fecha_servicio}}</p>
                    </template>
                </template>
                <template slot="certificado" slot-scope="props">
                    <div class="text-center">
                        <template v-if="props.row.certificado">
                            <button class="btn btn-outline-dark" @click="DescargarCertificado(props.row.certificado)">
                                <i class="fas fa-download"></i>
                            </button>
                        </template>
                        <template v-else>
                            <button class="btn btn-outline-dark">N/D</button>
                        </template>
                    </div>
                </template>
            </v-client-table>
        </div>
    </div>

    <!--Inicio del modal Equipos-->
    <div v-if="ver_modal_equipos" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_equipos}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_equipos"></h4>
                        <button type="button" class="close" @click="CerrarModalEquipos()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardarequipos_loading" />
                        <div>
                            <!-- Formulario -->
                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Equipo</label>
                                <div class="col-md-9">
                                    <input type="text" maxlength="125" minlength="3" v-validate="'required'" v-model="equipos.equipo" class="form-control" data-vv-name="Equipo" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Equipo') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Marca</label>
                                <div class="col-md-4">
                                    <input type="text" maxlength="50" minlength="3" v-validate="'required'" v-model="equipos.marca" class="form-control" data-vv-name="Marca" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Marca') }}</span>
                                </div>
                                <label class="col-md-1 form-control-label">Modelo</label>
                                <div class="col-md-4">
                                    <input type="text" maxlength="50" minlength="3" v-validate="'required'" v-model="equipos.modelo" class="form-control" data-vv-name="Modelo" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Modelo') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">NS</label>
                                <div class="col-md-3">
                                    <input type="text" maxlength="50" minlength="3" v-validate="'required'" v-model="equipos.ns" class="form-control" data-vv-name="NS" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('NS') }}</span>
                                </div>
                                <label class="col-md-2 form-control-label">Rango Medicion</label>
                                <div class="col-md-4">
                                    <input type="text" maxlength="80" minlength="3" v-validate="'required'" v-model="equipos.rango_medicion" class="form-control" data-vv-name="Rango Medicion" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Rango Medicion') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Tipo</label>
                                <div class="col-md-6">
                                    <select v-model="equipos.tipo" class="form-control" va-validate="'required'" data-vv-name="Tipo">
                                        <option :key="i" v-for="(t,i) in tipo_equipos" :value="t">{{t.nombre}}</option>
                                    </select>
                                    <span class="text-danger">{{ errors.first('Tipo') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Frecuencia</label>
                                <div class="col-md-6">
                                    <input type="text" maxlength="5" minlength="3" v-validate="'required'" v-model="equipos.frecuencia" class="form-control" data-vv-name="Frecuencia" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Frecuencia') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Resguardo</label>
                                <div class="col-md-6">
                                    <input type="text" maxlength="50" minlength="3" v-validate="'required'" v-model="equipos.resguardo" class="form-control" data-vv-name="Resguardo" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Resguardo') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Revisa</label>
                                <div class="col-md-6">
                                    <v-select label="nombre" :options="list_empleados" v-validate="'required'" v-model="equipos.revisa" data-vv-name="Revisa">
                                    </v-select>
                                    <span class="text-danger">{{ errors.first('Revisa') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Observaciones</label>
                                <div class="col-md-9">
                                    <textarea cols="4" maxlength="50" minlength="1" v-validate="'required'" v-model="equipos.observaciones" class="form-control" data-vv-name="Observaciones" autocomplete="off"></textarea>
                                    <span class="text-danger">{{ errors.first('Observaciones') }}</span>
                                </div>
                            </div>

                            <!-- Formulario -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardarequipos_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalEquipos()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                            <button type="button" v-if="tipoAccion_equipos==1" class="btn btn-secondary" @click="RegistrarEquipos(true)">
                                <i class="fas fa-save"></i>&nbsp;Guardar
                            </button>
                            <button type="button" v-if="tipoAccion_equipos==2" class="btn btn-secondary" @click="RegistrarEquipos(false)">
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
    <!--Fin del modal Equipos-->

    <!--Inicio del modal Calibraciones-->
    <div v-if="ver_modal_calibraciones" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_calibraciones}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_calibraciones"></h4>
                        <button type="button" class="close" @click="CerrarModalCalibraciones()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardarcalibraciones_loading" />
                        <div>
                            <!-- Formulario -->

                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Fecha Servicio</label>
                                <div class="col-md-4">
                                    <input type="date" v-validate="'required'" v-model="calibraciones.fecha_servicio" class="form-control" data-vv-name="Fecha Servicio" />
                                    <span class="text-danger">{{ errors.first('Fecha Servicio') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Próxima fecha</label>
                                <div class="col-md-4">
                                    <input type="date" v-validate="'required'" v-model="calibraciones.proxima_fecha" class="form-control" data-vv-name="Próxima fecha" />
                                    <span class="text-danger">{{ errors.first('Próxima fecha') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Certificado</label>
                                <div class="col-md-6">
                                    <input ref="file_certificado" type="file" accept="application/pdf" class="form-control" />
                                </div>
                            </div>
                            <!-- Formulario -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardarcalibraciones_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalCalibraciones()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                            <button type="button" v-if="tipoAccion_calibraciones==1" class="btn btn-secondary" @click="RegistrarCalibraciones(true)">
                                <i class="fas fa-save"></i>&nbsp;Guardar
                            </button>
                            <button type="button" v-if="tipoAccion_calibraciones==2" class="btn btn-secondary" @click="RegistrarCalibraciones(false)">
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
    <!--Fin del modal Calibraciones-->

</main>
</template>

<script>
import Utilerias from '../../Herramientas/utilerias.js';
var config = require('../../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            //// Equipos
            list_empleados: [],
            url_equipos: "calidad/calibracion/equipos",
            tipoAccion_equipos: 1,
            tipoCardEquipos: 1,
            PermisosCRUD:
            {},
            ver_modal_equipos: false,
            tituloModal_equipos: "",
            equipos_id: 0,
            isGuardarequipos_loading: false,
            isObtenerequipos_loading: false,
            columns_equipos: [
                "id",
                "equipo",
                "marca",
                "modelo",
                "ns",
                "rango_medicion",
                "resguardo",
                "fecha_servicio",
                "proxima_fecha",
                "certificado",
                "condicion"
            ],
            list_equipos: [],
            equipos:
            {},
            options_equipos:
            {
                headings:
                {
                    id: "Acciones",
                    equipo: "Equipo",
                    marca: "Marca",
                    modelo: "Modelo",
                    ns: "NS",
                    rango_medicion: "Rango Medicion",
                    resguardo: "Resguardo",
                    revisa: "Revisa",
                    observaciones: "Observaciones",

                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            },

            //// Calibraciones
            url_calibraciones: "calidad/calibracion",
            tipoAccion_calibraciones: 1,
            PermisosCRUD:
            {},
            ver_modal_calibraciones: false,
            tituloModal_calibraciones: "",
            calibraciones_id: 0,
            isGuardarcalibraciones_loading: false,
            isObtenercalibraciones_loading: false,
            columns_calibraciones: [
                "id",
                "fecha_servicio",
                "proxima_fecha",
                "certificado"
            ],
            list_calibraciones: [],
            calibraciones:
            {},
            options_calibraciones:
            {
                headings:
                {
                    id: "Acciones",
                    fecha_servicio: "Fecha Servicio",
                    proxima_fecha: "Próxima fecha",
                    certificado: "Certificado",

                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            },
            tipos_descargar: [],
            tipo_equipos: [
            {
                id: 1,
                nombre: "CONFIGURADORES TREX"
            },
            {
                id: 2,
                nombre: "EQUIPOS DE MEDICIÓN DE SOLDADURA "
            },
            {
                id: 3,
                nombre: "EQUIPOS DE TORQUE"
            },
            {
                id: 4,
                nombre: "EQUIPOS POR ASME"
            },
            {
                id: 5,
                nombre: "MAQUINAS DE COMBUSTION"
            },
            {
                id: 6,
                nombre: "MAQUINAS DE SOLDAR"
            },
            {
                id: 7,
                nombre: "VARIABLE: PRESIÓN"
            },
            {
                id: 8,
                nombre: "VARIABLE: RUGOSIDAD"
            },
            {
                id: 9,
                nombre: "VARIABLE: SALES SULUBLES, POROSIDAD, ESPESORES (PINTURA)"
            },
            {
                id: 10,
                nombre: "VARIABLE: TEMPERATURA"
            },
            {
                id: 11,
                nombre: "VARIALE: VOLTAJE, CORRIENTE, RESISTENCIA Y ASLAMIENTO"
            },
            {
                id: 12,
                nombre: "TODOS"
            }, ]
        }
    },
    methods:
    {
        /**
         * Obtener los empleados activos
         */
        async ObtenerEmpleados()
        {
            const res = await axios.get("generales/empleadoactivos");
            if (!res.data.status)
            {
                toastr.error(res.data.mensaje);
                return;
            }
            this.list_empleados = res.data.empleados;
        },
        /**
         * Obtener todos los registros
         */
        ObtenerEquipos()
        {
            this.isObtenerequipos_loading = true;
            axios.get(this.url_equipos).then(res =>
            {
                this.isObtenerequipos_loading = false;
                if (res.data.status)
                {
                    this.list_equipos = res.data.equipos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Abrir modal Equipos
         */
        AbrirModalEquipos(nuevo, data = {})
        {
            this.ver_modal_equipos = true;
            if (nuevo)
            {
                this.tituloModal_equipos = "Registrar Equipos de Calibración";
                this.tipoAccion_equipos = 1;
                this.equipos.observaciones = "-";
            }
            else
            {
                this.tituloModal_equipos = "Actualizar Equipos de Calibración";
                this.tipoAccion_equipos = 2;
                const tipo = this.tipo_equipos.find(t => t.nombre == data.tipo);
                this.equipos = {
                    ...data,
                    revisa:
                    {
                        id: data.empleado_revisa_id,
                        nombre: data.revisa,
                    },
                    tipo
                };
            }
        },

        /**
         * Registrar Equipos
         */
        RegistrarEquipos(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.equipos.id);
                data.append("equipo", this.equipos.equipo);
                data.append("marca", this.equipos.marca);
                data.append("modelo", this.equipos.modelo);
                data.append("tipo", this.equipos.tipo.nombre);
                data.append("ns", this.equipos.ns);
                data.append("rango_medicion", this.equipos.rango_medicion);
                data.append("resguardo", this.equipos.resguardo);
                data.append("frecuencia", this.equipos.frecuencia);
                data.append("empleado_revisa_id", this.equipos.revisa.id);
                data.append("observaciones", this.equipos.observaciones);

                this.isGuardarequipos_loading = true;
                axios.post(this.url_equipos, data).then(res =>
                {
                    this.isGuardarequipos_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.ObtenerEquipos();
                        this.CerrarModalEquipos();
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
        CerrarModalEquipos()
        {
            this.ver_modal_equipos = false;
            this.equipos = {};
        },

        /**
         * Abrir modal Calibraciones
         */
        AbrirModalCalibraciones(data = {})
        {
            this.ver_modal_calibraciones = true;
            this.tituloModal_calibraciones = `${data.marca} - ${data.ns}`;
            this.tipoAccion_calibraciones = 1;
            this.equipos_id = data.id;
        },

        /**
         * Registrar Calibraciones
         */
        RegistrarCalibraciones(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;

                let aux_certificado = 0;
                let certificado = 0;

                if (this.calibraciones.fecha_servicio == this.calibraciones.proxima_fecha)
                {
                    toastr.warning("Las fechas no pueden ser iguales");
                    return;
                }

                if (this.$refs.file_certificado.files.length == 0)
                {
                    toastr.warning("Seleccione un documento");
                    return;
                }

                aux_certificado = 1;
                certificado = this.$refs.file_certificado.files[0];

                let data = new FormData();
                data.append("equipo_id", this.equipos_id);
                data.append("fecha_servicio", this.calibraciones.fecha_servicio);
                data.append("proxima_fecha", this.calibraciones.proxima_fecha);
                data.append("certificado", certificado);
                data.append("aux_certificado", aux_certificado);

                this.isGuardarcalibraciones_loading = true;
                axios.post(this.url_calibraciones + "/calibracion", data).then(res =>
                {
                    this.isGuardarcalibraciones_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.ObtenerEquipos();
                        this.CerrarModalCalibraciones();
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
        CerrarModalCalibraciones()
        {
            this.ver_modal_calibraciones = false;
            this.Calibraciones = {};
        },

        /**
         * Descargar el certificado seleccionado
         */
        DescargarCertificado(nombre)
        {
            window.open(this.url_calibraciones + "/certificado/" + nombre)
        },

        /**
         * Descargar el reporte de los equipos
         */
        DescargarReporte()
        {
            const ids = this.tipos_descargar.map(t => t.id).join(",");
            window.open(`${this.url_equipos}/descargar/${ids}`)
        },

    },
    mounted()
    {
        this.ObtenerEmpleados();
        this.ObtenerEquipos();
        this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
    }
}
</script>
