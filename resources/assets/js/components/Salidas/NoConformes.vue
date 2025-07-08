<template>
<main class="main">
    <!-- Listado SalidaNC -->
    <div class="card" v-show="tipoCardSalidaNC==1">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> SALIDAS NO CONFORMES - {{anio}}
            <button v-if="PermisosCRUD.Download" type="button" @click="DescargarBitacora" class="btn btn-secondary float-sm-right mr-1">
                <i class="fas fa-download"></i> Bitácora
            </button>
            <div class="dropdown float-sm-right mx-1">
                <button class="btn btn-outline-dark dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Año
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                    <button class="dropdown-item" type="button" @click="anio=2024;ObtenerSalidaNC()">2024</button>
                </div>
            </div>
            <button type="button" @click="AbrirModalSalidaNC(true)" class="btn btn-dark float-sm-right">
                <i class="fas fa-plus"></i>&nbsp;Nuevo
            </button>
        </div>
        <div class="card-body">
            <vue-element-loading :active="isObtenersalidanc_loading" />
            <v-client-table :columns="columns_salidanc" :data="list_salidanc" :options="options_salidanc">
                <template slot="id" slot-scope="props">
                    <div class="btn-group" role="group">
                        <div class="btn-group dropup" role="group">
                            <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-grip-horizontal"></i> Acciones
                            </button>
                            <div class="dropdown-menu">
                                <button v-if="PermisosCRUD.Update" type="button" class="dropdown-item" @click="AbrirModalSalidaNC(false,props.row)">
                                    <i class="fas fa-edit"></i> Actualizar
                                </button>
                                <button v-if="PermisosCRUD.Download" type="button" class="dropdown-item" @click="DescargarSalida(props.row.id)">
                                    <i class="fas fa-download"></i> Descargar
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>
        </div>
    </div>

    <!-- Registrar SalidaNC -->
    <div class="card" v-show="tipoCardSalidaNC==2">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> SALIDA NO CONFORME
            <button type="button" @click="CerrarModalSalidaNC" class="btn btn-dark float-sm-right">
                <i class="fas fa-arrow-left"></i>&nbsp;Regresar
            </button>
        </div>
        <div class="card-body">
            <vue-element-loading :active="isGuardarsalidanc_loading" />
            <!-- Formulario -->
            <div class="form-row mx-3">
                <div class="col-md-6 mb-2">
                    <label class="">Elaborado por</label>
                    <v-select label="nombre" :options="list_empleados" v-validate="'required'" v-model="salidanc.empleado_elabora" data-vv-name="Elaborado por">
                    </v-select>
                    <span class="text-danger">{{ errors.first('Elaborado por') }}</span>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="">Fecha de elaboración</label>
                    <input type="date" v-validate="'required'" v-model="salidanc.fecha_elaboracion" class="form-control" data-vv-name="Fecha de elaboración" />
                    <span class="text-danger">{{ errors.first('Fecha de elaboración') }}</span>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="">Detectado por</label>
                    <v-select label="nombre" :options="list_empleados" v-validate="'required'" v-model="salidanc.empleado_detecta" data-vv-name="Detectado por">
                    </v-select>
                    <span class="text-danger">{{ errors.first('Detectado por') }}</span>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="">Área</label>
                    <v-select label="nombre" :options="list_departamentos_salidas" v-validate="'required'" v-model="salidanc.area" data-vv-name="Área">
                    </v-select>
                    <span class="text-danger">{{ errors.first('Área') }}</span>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="">Fecha de detección</label>
                    <input type="date" v-validate="'required'" v-model="salidanc.fecha_deteccion" class="form-control" data-vv-name="Fecha de detección" />
                    <span class="text-danger">{{ errors.first('Fecha de detección') }}</span>
                </div>
                <div class="col-md-6 mb-2" v-if="salidanc.id">
                    <label class="">Número de reporte</label>
                    <input type="text" maxlength="1" minlength="" v-validate="'required'" v-model="salidanc.folio" class="form-control disabled" readonly disabled data-vv-name="Número de reporte" autocomplete="off" />
                    <span class="text-danger">{{ errors.first('Número de reporte') }}</span>
                </div>
            </div>

            <div class="accordion mt-4" id="accordionSalida">
                <div class="card">
                    <div class="card-header" id="headingIdentificacion">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseIdentificacion" aria-expanded="true" aria-controls="collapseIdentificacion">
                                1. IDENTIFICACIÓN
                            </button>
                        </h2>
                    </div>

                    <div id="collapseIdentificacion" class="collapse show" aria-labelledby="headingIdentificacion" data-parent="#accordionSalida">
                        <div class="card-body">
                            <div class="form-group">
                                <p>Descripción de la Salida No Conforme Detectada</p>
                                <div class="col-md-12">
                                    <textarea rows="8" v-validate="'required'" v-model="salidanc.descripcion" class="form-control" data-vv-name="Descripción" autocomplete="off"></textarea>
                                    <span class="text-danger">{{ errors.first('') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Proyecto /Servicio</label>
                                <div class="col-md-9">
                                    <v-select label="nombre_corto" :options="list_proyectos" v-validate="'required'" v-model="salidanc.proyecto" data-vv-name="Proyecto /Servicio">
                                    </v-select>
                                    <span class="text-danger">{{ errors.first('Proyecto /Servicio') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Nombre del cliente, proveedor o proceso</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" v-validate="'required'" v-model="salidanc.cliente_proveedor" data-vv-name="Nombre del cliente, proveedor o proceso">
                                    <span class="text-danger">{{ errors.first('Nombre del cliente, proveedor o proceso') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Número de comunicado</label>
                                <div class="col-md-6">
                                    <input type="text" maxlength="50" minlength="1" v-validate="'required'" v-model="salidanc.no_comunicado" class="form-control" data-vv-name="Número de comunicado" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Número de comunicado') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Orden de Compra</label>
                                <div class="col-md-6">
                                    <input type="text" v-model="salidanc.no_oc" class="form-control" data-vv-name="Número de OC" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Número de OC') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingTratamiento">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTratamiento" aria-expanded="false" aria-controls="collapseTratamiento">
                                2. TRATAMIENTO
                            </button>
                        </h2>
                    </div>
                    <div id="collapseTratamiento" class="collapse" aria-labelledby="headingTratamiento" data-parent="#accordionSalida">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" v-model="salidanc.tratamiento" name="rdntratamiento" id="rdntratamiento1" value="1">
                                    <label class="form-check-input-label" for="rdntratamiento1">Corrección</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" v-model="salidanc.tratamiento" name="rdntratamiento" id="rdntratamiento2" value="2">
                                    <label class="form-check-input-label" for="rdntratamiento2">Separación</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" v-model="salidanc.tratamiento" name="rdntratamiento" id="rdntratamiento3" value="3">
                                    <label class="form-check-input-label" for="rdntratamiento3">Contención</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" v-model="salidanc.tratamiento" name="rdntratamiento" id="rdntratamiento4" value="4">
                                    <label class="form-check-input-label" for="rdntratamiento4">Contención</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" v-model="salidanc.tratamiento" name="rdntratamiento" id="rdntratamiento5" value="5">
                                    <label class="form-check-input-label" for="rdntratamiento5">Devolución</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" v-model="salidanc.tratamiento" name="rdntratamiento" id="rdntratamiento6" value="6">
                                    <label class="form-check-input-label" for="rdntratamiento6">Suspensión</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" v-model="salidanc.tratamiento" name="rdntratamiento" id="rdntratamiento7" value="7">
                                    <label class="form-check-input-label" for="rdntratamiento7">Desecho</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" v-model="salidanc.tratamiento" name="rdntratamiento" id="rdntratamiento8" value="8">
                                    <label class="form-check-input-label" for="rdntratamiento8">Información al cliente</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" v-model="salidanc.tratamiento" name="rdntratamiento" id="rdntratamiento9" value="9">
                                    <label class="form-check-input-label" for="rdntratamiento9">Otro</label>
                                </div>
                                <div class="form-check form-check-inline" v-show="salidanc.tratamiento==9">
                                    <input type="text" placeholder="Otro" v-model="salidanc.tratamiento_otro" class="form-control" data-vv-name="Tratamiento">
                                </div>
                            </div>
                            <hr>

                            <p class="h4 text-center">Acciones</p>
                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Actividad</label>
                                <div class="col-md-5">
                                    <input type="text" maxlength="150" class="form-control" v-model="acciones.actividad" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Responsable</label>
                                <div class="col-md-5">
                                    <v-select label="nombre" :options="list_empleados" v-model="acciones.responsable"></v-select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Fecha</label>
                                <div class="col-md-3">
                                    <input type="date" class="form-control ml-2" v-model="acciones.fecha" />
                                </div>
                                <button class="btn btn-sm" @click="agregarActividadTemp"><i class="fas fa-plus"></i></button>
                            </div>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Actividad</th>
                                        <th scope="col">Responsable</th>
                                        <th scope="col">Fecha</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <template v-if="list_acciones_temp.length!=0">
                                    <tbody>
                                        <tr :key="i" v-for="(a,i) in list_acciones_temp">
                                            <td>{{i+1}}</td>
                                            <td>{{a.actividad}}</td>
                                            <td>{{a.responsable.nombre}}</td>
                                            <td>{{a.fecha}}</td>
                                            <td>
                                                <button class="btn btn-sm" @click="eliminarAccionTemp(i)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </template>
                            </table>
                            <template v-if="list_acciones_temp.length==0">
                                <p class="text-center h5">Sin actividades</p>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingVerificacion">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseVerificacion" aria-expanded="false" aria-controls="collapseVerificacion">
                                3. VERIFICACIÓN
                            </button>
                        </h2>
                    </div>
                    <div id="collapseVerificacion" class="collapse" aria-labelledby="headingVerificacion" data-parent="#accordionSalida">
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Resultado de las actividades</label>
                                <div class="col-md-9">
                                    <textarea rows="6" v-model="salidanc.resultado" class="form-control" data-vv-name="Resultado de las actividades" autocomplete="off"></textarea>
                                    <span class="text-danger">{{ errors.first('Resultado de las actividades') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="container form-row">
                            <div class="col-md-6 mb-2">
                                <label class="">Nombre de quien verifica</label>
                                <v-select label="nombre" :options="list_empleados" v-model="salidanc.empleado_verifica" data-vv-name="Nombre y firma de quien verifica">
                                </v-select>
                                <span class="text-danger">{{ errors.first('Nombre y firma de quien verifica') }}</span>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="">Fecha de verificación</label>
                                <input type="date" v-model="salidanc.fecha_verificacion" class="form-control" data-vv-name="Fecha de verificación" />
                                <span class="text-danger">{{ errors.first('Fecha de verificación') }}</span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="">¿Se requiere una Acción Correctiva?</label>
                                <select class="form-control" v-model="salidanc.require_correccion" data-vv-name="¿La Salida No Conforme requiere una Acción Correctiva?">
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                                <span class="text-danger">{{ errors.first('¿La Salida No Conforme requiere una Acción Correctiva?') }}</span>
                            </div>
                            <div class="col-md-6 mb-2" v-show="salidanc.require_correccion==1">
                                <label class="">Número de acción correctiva correspondiente</label>
                                <input type="text" v-model="salidanc.no_accion_correctiva" class="form-control" data-vv-name="Número de acción correctiva correspondiente" />
                                <span class="text-danger">{{ errors.first('Número de acción correctiva correspondiente') }}</span>
                            </div>
                        </div>
                        <div class="text-right mx-2 my-2">
                            <!-- Formulario -->
                            <button type="button" v-if="tipoAccion_salidanc==1" class="btn btn-secondary" @click="RegistrarSalidaNC(true)">
                                <i class="fas fa-save"></i>&nbsp;Guardar
                            </button>
                            <button type="button" v-if="tipoAccion_salidanc==2" class="btn btn-secondary" @click="RegistrarSalidaNC(false)">
                                <i class="fas fa-save"></i>&nbsp;Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Fin del modal SalidaNC-->

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
            //// SalidaNC
            url_salidanc: "salidassgi/salidanc",
            tipoAccion_salidanc: 1,
            tipoCardSalidaNC: 1,
            PermisosCRUD:
            {},
            ver_modal_salidanc: false,
            tituloModal_salidanc: "",
            salidanc_id: 0,
            isGuardarsalidanc_loading: false,
            isObtenersalidanc_loading: false,
            columns_salidanc: [
                "id",
                "folio",
                "fecha_elaboracion",
                "area.nombre",
                "proyecto.nombre_corto",
                "cliente_proveedor",
            ],
            list_salidanc: [],
            list_empleados: [],
            list_proyectos: [],
            list_clientes_proveedores: [],
            list_departamentos_salidas: [],
            list_acciones_temp: [],
            acciones:
            {},
            salidanc:
            {},
            actividades:
            {},
            options_salidanc:
            {
                headings:
                {
                    id: "Acciones",
                    folio: "Número de reporte",
                    "area.nombre": "Área",
                    "proyecto.nombre_corto": "Proyecto /Servicio",
                    cliente_proveedor: "Cliente / Proveedor",
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            },
            anio: 2024,
        }
    },
    methods:
    {
        ObtenerDatos()
        {
            axios.get("generales/empleadoactivos").then(res =>
            {
                if (res.data.status)
                    this.list_empleados = res.data.empleados;
                else
                    toastr.error(res.data.mensaje);
            });

            axios.get(`${this.url_salidanc}/departamentos`).then(res =>
            {
                if (res.data.status)
                    this.list_departamentos_salidas = res.data.departamentos;
                else
                    toastr.error(res.data.mensaje);
            });

            axios.get("generales/proyectos/1").then(res =>
            {
                if (res.data.status)
                    this.list_proyectos = res.data.proyectos;
                else
                    toastr.error(res.data.mensaje);
            });
        },

        /**
         * Obtener todos los registros
         */
        ObtenerSalidaNC()
        {
            this.isObtenersalidanc_loading = true;
            axios.get(`${this.url_salidanc}/${this.anio}`).then(res =>
            {
                this.isObtenersalidanc_loading = false;
                if (res.data.status)
                    this.list_salidanc = res.data.salidanc;
                else
                    toastr.error(res.data.mensaje);
            })
        },

        /**
         * Abrir modal SalidaNC
         */
        AbrirModalSalidaNC(nuevo, data = {})
        {
            this.tipoCardSalidaNC = 2;
            if (nuevo)
            {
                this.tituloModal_salidanc = "Registrar Salida No Conforme";
                this.tipoAccion_salidanc = 1;
            }
            else
            {
                this.tituloModal_salidanc = "Actualizar Salida No Conforme";
                this.tipoAccion_salidanc = 2;
                const acciones = JSON.parse(data.acciones);
                this.list_acciones_temp = acciones;
                this.salidanc = {
                    ...data,
                };
            }
        },

        /**
         * Registrar SalidaNC
         */
        RegistrarSalidaNC(nuevo)
        {
            this.$validator.validateAll().then(isValid =>
            {
                if (!isValid)
                {
                    toastr.warning("Ingrese todos los campos");
                    return;
                }
                if (this.list_acciones_temp.length == 0)
                {
                    toastr.warning("Ingrese una acción a realizar");
                    return;
                }
                if (this.salidanc.tratamiento != 9) this.salidanc.tratamiento_otro = "";
                let data = new FormData();
                const resultado = this.salidanc.resultado ? this.salidanc.resultado : "";
                if (!nuevo)
                    data.append("id", this.salidanc.id);
                data.append("empleado_elabora_id", this.salidanc.empleado_elabora.id);
                data.append("fecha_elaboracion", this.salidanc.fecha_elaboracion);
                data.append("empleado_detecta_id", this.salidanc.empleado_detecta.id);
                data.append("area_id", this.salidanc.area.id);
                data.append("fecha_deteccion", this.salidanc.fecha_deteccion);
                data.append("descripcion", this.salidanc.descripcion);
                data.append("proyecto_id", this.salidanc.proyecto.id);
                data.append("cliente_proveedor", this.salidanc.cliente_proveedor);
                data.append("no_oc", this.salidanc.no_oc ? this.salidanc.no_oc : "");
                data.append("no_comunicado", this.salidanc.no_comunicado ? this.salidanc.no_comunicado : "");
                data.append("acciones", JSON.stringify(this.list_acciones_temp));
                data.append("tratamiento", this.salidanc.tratamiento);
                data.append("tratamiento_otro", this.salidanc.tratamiento_otro);
                data.append("resultado", resultado);
                if (this.salidanc.empleado_verifica)
                    data.append("empleado_verifica_id", this.salidanc.empleado_verifica.id);
                if (this.salidanc.fecha_verificacion)
                    data.append("fecha_verificacion", this.salidanc.fecha_verificacion);
                if (this.salidanc.require_correccion)
                    data.append("require_correccion", this.salidanc.require_correccion);
                if (this.salidanc.no_accion_correctiva)
                    data.append("no_accion_correctiva", this.salidanc.no_accion_correctiva);

                // const method = nuevo ? "POST" : "PUT";
                // const url = nuevo ? this.url_salidanc : `${this.url_salidanc}/${this.salidanc.id}`;
                this.isGuardarsalidanc_loading = true;
                axios.post(this.url_salidanc, data).then(res =>
                {
                    this.isGuardarsalidanc_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.ObtenerSalidaNC();
                        this.CerrarModalSalidaNC();
                    }
                    else
                    {
                        toastr.error(res.data.mensaje);
                    }
                })
            })
        },

        agregarActividadTemp()
        {
            if (!this.acciones.actividad || this.acciones.actividad.trim() == "")
            {
                toastr.warning("Ingrese la actividad");
                return;
            }
            if (this.acciones.responsable == null || this.acciones.responsable.id == null)
            {
                toastr.warning("Ingrese el responsable");
                return;
            }
            if (this.acciones.fecha == null || this.acciones.fecha == "")
            {
                toastr.warning("Selecciona la fecha");
                return;
            }
            this.list_acciones_temp.push(
            {
                ...this.acciones
            });
            this.acciones = {};
        },

        eliminarAccionTemp(index)
        {
            this.list_acciones_temp.splice(index, 1);
        },

        /**
         * Cerrar modal
         */
        CerrarModalSalidaNC()
        {
            this.list_acciones_temp = [];
            this.acciones = {};
            this.tipoCardSalidaNC = 1;
            this.salidanc = {
                empleado_elabora:
                {},
                empleado_detecta:
                {},
                empleado_verifica:
                {},
                require_correccion: 1,
            };
            this.$validator.reset();
        },

        DescargarBitacora()
        {
            window.open(`${this.url_salidanc}/bitacora/${this.anio}`, "_blank");
        },
        
        DescargarSalida(id)
        {
            window.open(`${this.url_salidanc}/descargar/${id}`, "_blank");
        },
    },
    mounted()
    {
        this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
        this.ObtenerDatos();
        this.ObtenerSalidaNC();
    }
}
</script>

<style scoped>
.form-check-input-label {
    margin-left: 0;
    margin-right: 1.5rem;
}
</style>
