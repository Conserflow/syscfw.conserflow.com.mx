<template>
<main class="main">
    <div>
        <!-- Inicio del contenido principal -->
        <div class="card" v-show="ver == 1">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Solicitudes {{empresa == 1 ? "CONSERFLOW" : empresa == 2 ? "CSCT" : ""}}

                <button type="button" class="btn btn-dark float-sm-right" @click="abrirModal('solicitud','registrar')">
                    <i class="fas fa-plus"></i>Nuevo
                </button>
            </div>
            <div class="card-body">
                <vue-element-loading :active="isObtener_Loading" />
                <v-server-table :columns="columns" :options="options" :url="`solicitud/viaticos/conserflow/${empresa}?query={}&limit=10&ascending=1&page=1&byColumn=1`" ref="myTable">
                    <template slot="sv__id" slot-scope="props">
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group dropup" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <template v-if="props.row.sv__eliminado == 0">
                                        <template v-if="props.row.sv__estado==1">
                                            <button type="button" @click.prevent="abrirModal('solicitud','actualizar',props.row)" class="dropdown-item">
                                                <i class="fas fa-edit"></i>Actualizar Solicitud
                                            </button>
                                            <button type="button" v-show="PermisosCrud.Delete" @click.prevent="eliminar(props.row.sv__id, empresa)" class="dropdown-item">
                                                <i class="fas fa-trash"></i>Eliminar
                                            </button>
                                            <button type="button" @click.prevent="enviaRevision(props.row.sv__id, 2)" class="dropdown-item">
                                                <i class="far fa-paper-plane"></i>Cerrar
                                            </button>
                                        </template>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template slot="sv__estado" slot-scope="props">
                        <template v-if="props.row.sv__eliminado == 1">
                            <span class="btn btn-outline-danger">Eliminado</span>
                        </template>
                        <template v-if="props.row.sv__eliminado == 0">
                            <template v-if="props.row.sv__estado == 6">
                                <span class="btn btn-outline-info">Finalizado</span>
                            </template>
                            <template v-if="props.row.sv__estado == 5">
                                <span class="btn btn-outline-info">Pagos <br> agendados</span>
                            </template>
                            <template v-if="props.row.sv__estado == 4">
                                <span class="btn btn-outline-info">En espera <br> de pagos</span>
                            </template>
                            <template v-if="props.row.sv__estado == 3">
                                <span class="btn btn-outline-warning">En Autorizacion</span>
                            </template>
                            <template v-if="props.row.sv__estado == 2">
                                <span class="btn btn-outline-warning">En revisión</span>
                            </template>
                            <template v-if="props.row.sv__estado == 1">
                                <span class="btn btn-outline-success">Nuevo</span>
                            </template>
                            <template v-if="props.row.sv__estado == 0">
                                <span class="btn btn-outline-danger">No autorizado</span>
                            </template>
                        </template>
                    </template>

                    <template slot="sv__tipo" slot-scope="props">
                        <template v-if="props.row.sv__tipo ==  0">
                            SINDICATO
                        </template>
                        <template v-if="props.row.sv__tipo ==  1">
                            REEMBOLSO
                        </template>
                        <template v-if="props.row.sv__tipo ==  2">
                            VIATICOS
                        </template>
                    </template>

                    <template slot="id2" slot-scope="props">
                        <button type="button" @click="descargaViatico(props.row.sv__id,props.row.sv__tipo)" class="btn btn-outline-dark">
                            <i class="fas fa-file-pdf"></i><i class="fas fa-download"></i>
                        </button>
                    </template>
                    <template slot="sv__total_efectivo" slot-scope="props">
                        <p>{{props.row.sv__total_efectivo.toLocaleString('es-MX',{style: 'currency',currency: 'MXN'})}}</p>
                    </template>
                    <template slot="sv__total_transferencia" slot-scope="props">
                        <p>{{props.row.sv__total_transferencia.toLocaleString('es-MX',{style: 'currency',currency: 'MXN'})}}</p>
                    </template>
                </v-server-table>
            </div>
        </div>

        <!-- Inicio del modal agregar/actualizar -->
        <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" arial-hidden="true">
            <div class="modal-dialog modal-dark modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{tituloModal}} {{empresa==1?"Conserflow":"CSCT"}}</h4>
                        <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                            <span arial-hidden="true">X</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardar_Loading" />
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Tipo</label>
                                <select :disabled="!isEnabled" class="form-control" v-model="tipo" v-validate="'required'" data-vv-name="tipo" @change="componenteBeneficiario()">
                                    <option value="0">SINDICATO</option>
                                    <option value="1">REEMBOLSO</option>
                                    <option value="2">VIATICOS</option>
                                </select>
                                <span class="text-danger">{{errors.first("tipo")}}</span>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Fecha solicitud</label>
                                <input type="date" v-validate="'required'" data-vv-name="Fecha solicitud" v-model="solicitud.fecha" class="form-control">
                                <span class="text-danger">{{errors.first("Fecha solicitud")}}</span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Fecha requerida de pago</label>
                                <input type="date" v-validate="'required'" v-model="solicitud.fecha_pago" class="form-control" data-vv-name="Fecha de pago">
                                <span class="text-danger">{{errors.first("Fecha de pago")}}</span>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Proyecto</label>
                                <v-select :disabled="!isEnabled" :options="optionsvs_proyecto" v-validate="'required'" v-model="solicitud.proyecto_id" label="nombre_corto" data-vv-name="Proyecto"></v-select>
                                <span class="text-danger">{{ errors.first("Proyecto") }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="accordion" id="accordion">
                            <div class="card">
                                <div class="card-header" id="headingOne">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            BENEFICIARIOS
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                                    <div class="card-body">
                                        <beneficiario ref="beneficiario" @enviarUno="enviouno($event)" @enviarDos="enviodos($event)" @limpiarUno="limpiarBeneficiarioUno()" @limpiarDos="limpiarBeneficiarioDos()"></beneficiario>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingTwo">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                            DETALLE DE VIATICOS
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                                    <div class="card-body">
                                        <detallesv ref="detallesv" @listado="envio_u($event)" @totales="envio_d($event)" @conceptos="envio_c($event)"></detallesv>
                                    </div>
                                </div>
                            </div>
                            <template v-if="tipo > 0">
                                <div class="card">
                                    <div class="card-header" id="headingThree">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                                                NOTAS DE ITINERIARIO Y LOGISTICA
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="form-group col-md-2">
                                                    <label>Origen</label>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <input v-validate="'required'" type="text" v-model="solicitud.origen_destino" class="form-control" data-vv-name="Origen">
                                                    <span class="text-danger">{{ errors.first("Origen") }}</span>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Destino</label>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <input v-validate="'required'" type="text" v-model="solicitud.origen_destino_destino" class="form-control" data-vv-name="Destino">
                                                    <span class="text-danger">{{ errors.first("Destino") }}</span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="form-row">
                                                <div class="form-group col-md-5">
                                                    <label>1.- Fecha salida:</label>
                                                    <label>2.- Hora de estimada de salida:</label>
                                                </div>
                                                <div class="form-group col-md-4">

                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <span class="input-group-text">1</span>
                                                        </div>
                                                        <input v-validate="'required'" type="date" class="form-control" v-model="solicitud.fecha_salida" data-vv-name="Fecha salida">
                                                    </div>
                                                    <span class="text-danger">{{ errors.first("Fecha salida") }}</span>
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <span class="input-group-text">2</span>
                                                        </div>
                                                        <input type="time" class="form-control" v-model="solicitud.hora_estimada_salida" data-vv-name="Hora estimada de salida">
                                                    </div>
                                                    <span class="text-danger">{{ errors.first("Hora estimada de salida")}}</span>
                                                </div>
                                                <div class="form-group col-md-5">
                                                    <label>3.- Fecha de operación:</label>
                                                    <label>4.- Fecha de retorno estimada:</label>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <span class="input-group-text">3</span>
                                                        </div>
                                                        <input v-validate="'required'" type="date" class="form-control" v-model="solicitud.fecha_operacion" data-vv-name="Fecha de operacion">
                                                    </div>
                                                    <span class="text-danger"> {{ errors.first("Fecha de operacion") }} </span>
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <span class="input-group-text">4</span>
                                                        </div>
                                                        <input v-validate="'required'" type="date" class="form-control" v-model="solicitud.fecha_retorno" @change="verdiferencia()" data-vv-name="Fecha de retorno">
                                                    </div>
                                                    <span class="text-danger"> {{ errors.first("Fecha de retorno")}} </span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="form-row">

                                                <div class="form-group col-md-2">
                                                    <label><b>PLACAS</b></label>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label><b>UNIDAD</b></label>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label><b>OPERADOR</b></label>
                                                </div>
                                            </div>
                                            <li :key="index" v-for="(vi, index) in vehiculos_itinerario_viaticos" class="list-group-item">
                                                <div class="form-row">

                                                    <div class="form-group col-md-2">
                                                        <label>{{vi.km_inicial}}</label>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label>{{vi.unidad}}</label>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label>{{vi.empleado_operador_name}}</label>
                                                    </div>
                                                </div>
                                            </li>

                                            <div class="form-row">
                                                <div class="form-group col-md-2">
                                                    <v-select :options="list_unidades" label="placas" v-model="unidadtemporal"></v-select>
                                                </div>
                                                <div class="form-group col">
                                                    <input disabled type="text" v-model="unidadtemporal.modelo" class="form-control" placeholder="Unidad">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <v-select v-model="vehiculos_temporal.empleado_operador_id" :options="vs_options" label="name" name="personal_servicio_viaticos_id" data-vv-name="personal_servicio_viaticos_id"></v-select>
                                                </div>
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-secondary" @click="crear()">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header" id="headingFour">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
                                                PERSONAL DESTINADO AL SERVICIO
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label>Total de personas a asistir</label>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <input class="form-control" type="number" v-model="solicitud.total_personas" name="total_personas" data-vv-name="total_personas" readonly>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label>Supervisor</label>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <v-select v-validate="'required'" v-model="solicitud.empleado_supervisor_id" :options="vs_options" label="name" data-vv-name="Supervisor" @input="sumarTotalPersona"></v-select>
                                                    <span class="text-danger"> {{ errors.first("Supervisor")}} </span>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label>Nombre del personal que asiste al servicio</label>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <v-select multiple v-model="solicitud.personal_servicio_viaticos_id" :options="vs_options" label="name" name="personal_servicio_viaticos_id" data-vv-name="personal_servicio_viaticos_id" @input="sumarTotalPersonas"></v-select>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </template>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Elaboró</label>
                                <v-select v-validate="'required'" v-model="solicitud.empleado_elabora_id" :options="vs_options" label="name" data-vv-name="Elabora"></v-select>
                                <span class="text-danger">{{ errors.first("Elabora") }}</span>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Revisó</label>
                                <v-select v-validate="'required'" v-model="solicitud.empleado_revisa_id" :options="vs_options" label="name" name="revisa" data-vv-name="Revisa"></v-select>
                                <span class="text-danger">{{ errors.first("Revisa") }}</span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Autorizó</label>
                                <v-select disabled v-validate="'required'" v-model="solicitud.empleado_autoriza_id" :options="vs_options" label="name" name="autoriza" data-vv-name="Autoriza"></v-select>
                                <span class="text-danger">{{ errors.first("Autoriza") }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardar_Loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="cerrarModal()"><i class="fas fa-times"></i>Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-secondary" @click="guardar(1)"><i class="fas fa-save"></i>Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-secondary" @click="guardar(0)"><i class="fas fa-save"></i>Actualizar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</template>

<script>
const Beneficiarios = r => require.ensure([], () => r(require("./Beneficiarios.vue")), "via");
const DetallesV = r => require.ensure([], () => r(require("./DetallesV.vue")), "via");

import Utilerias from "../../Herramientas/utilerias.js";
var config = require("../../Herramientas/config-vuetables-client").call(this);

export default
{
    data()
    {
        return {
            PermisosCrud:
            {},
            timbre_modal: 0,
            tipo: "",
            unidadtemporal:
            {},
            empresa: "1",
            ver: 1,
            tabs: 1,
            isLoading: false,
            tituloModal: "",
            modal: 0,
            tipoAccion: 0,
            isEnabled: true,
            data_detalle: "",
            isObtener_Loading: false,
            isGuardar_Loading: false,
            solicitud:
            {
                id: "",
                fecha: "",
                fecha_pago: "",
                proyecto_id: "",
                personal_servicio_viaticos_id: "",
                empleado_elabora_id: "",
                empleado_revisa_id: "",
                empleado_autoriza_id: "",
                empleado_supervisor_id: "",
                beneficiario_uno: [],
                detalles_listado: [],
                detalles_totales: [],
                detalles_conceptos: [],
                origen_destino: "",
                origen_destino_destino: "",
                fecha_salida: "",
                hora_estimada_salida: "",
                fecha_operacion: "",
                fecha_retorno: "",
                total_personas: "",
            },
            list_unidades: [],
            vehiculos_itinerario_viaticos: [],
            vehiculos_temporal:
            {
                unidad: "",
                km_inicial: "",
                empleado_operador_id: "",
                empleado_operador_name: "",
            },
            optionsvs_proyecto: [],
            vs_options: [],
            columns: [
                "sv__id",
                "sv__folio",
                "sv__tipo",
                "sv__fecha_solicitud",
                "sv__fecha_pago",
                "benef__nombre",
                "id2",
                "EE__nombre",
                "sv__total_efectivo",
                "sv__total_transferencia",
                "sv__estado",
            ],
            options:
            {
                headings:
                {
                    "sv__id": "Acciones",
                    "sv__folio": "Folio",
                    "sv__tipo": "Tipo",
                    "sv__fecha_pago": "Fecha de pago",
                    "sv__estado": "Estado",
                    "benef__nombre": "Beneficiario",
                    "sv__fecha_solicitud": "Fecha solicitada",
                    "id2": "Descargar",
                    "sv__nombre_revisa": "Revisa",
                    "EE__nombre": "Elabora",
                    "sv__nombre_autoriza": "Autoriza",
                    "sv__total_efectivo": "Total efectivo",
                    "sv__total_transferencia": "Total transferencia",
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                listColumns:
                {
                    "sv__tipo": [
                    {
                        id: 1,
                        text: "REEMBOLSO"
                    },
                    {
                        id: 2,
                        text: "VIATICOS"
                    },
                    {
                        id: 0,
                        text: "SINDICATO"
                    }, ],

                },
                texts: config.texts
            },
        }
    },
    components:
    {
        "beneficiario": Beneficiarios,
        "detallesv": DetallesV,
    },
    methods:
    {
        getData()
        {
            this.ver = 1;
            this.PermisosCrud = Utilerias.getCRUD(this.$route.path);

        },

        sumarTotalPersona()
        {
            this.solicitud.total_personas = 1;
        },

        sumarTotalPersonas()
        {
            if (this.solicitud.personal_servicio_viaticos_id.length == 1)
            {
                this.solicitud.total_personas = "";
                this.solicitud.total_personas = 1 + this.solicitud.personal_servicio_viaticos_id.length;
            }
            else
            {
                this.solicitud.total_personas = "";
                this.solicitud.total_personas = 1 + this.solicitud.personal_servicio_viaticos_id.length;
            }
        },

        diaActual()
        {
            var hoy = new Date();
            var dd = hoy.getDate();
            var mm = hoy.getMonth() + 1; //hoy es 0!
            var yyyy = hoy.getFullYear();

            if (dd < 10)
            {
                dd = "0" + dd
            }
            if (mm < 10)
            {
                mm = "0" + mm
            }

            hoy = yyyy + "-" + mm + "-" + dd;
            this.solicitud.fecha = hoy;
            this.solicitud.fecha_salida = hoy;
        },

        verdiferencia()
        {
            if (new Date(this.solicitud.fecha).getTime() > new Date(this.solicitud.fecha_retorno))
            {
                toastr.warning("La fecha de salida no puede ser menor a la fecha salida");
            }
        },

        componenteBeneficiario()
        {
            var childBeneficiario = this.$refs.beneficiario;
            childBeneficiario.getDatos(this.empresa, this.tipo, this.vs_options);

            var ChilDetallesv = this.$refs.detallesv;
            ChilDetallesv.getDatos(this.tipo);
        },

        /**
         * Cargar las unidades para el itinerario
         */
        ObtenerUnidades()
        {
            axios.get("vehiculos/combustible/obtenerunidades").then(res =>
            {
                if (res.data.status)
                {
                    this.list_unidades = res.data.unidades;
                }
                else toastr.error(res.data.mensaje);
            })
        },
        abrirModal(modelo, accion, data = [])
        {
            if (this.empresa === "")
            {
                toastr.warning("Seleccione una empresa");
            }
            else
            {
                this.ObtenerProyectos();
                this.ObtenerEmpleados();
                this.ObtenerUnidades();
                switch (modelo)
                {
                    case "solicitud":
                    {
                        switch (accion)
                        {
                            case "registrar":
                            {
                                this.isEnabled = true;
                                Utilerias.resetObject(this.solicitud);
                                this.modal = 1;
                                this.diaActual();
                                this.tituloModal = "Registrar solicitud de viaticos";
                                this.tipoAccion = 1;
                                // Autoriza Finanzas
                                this.solicitud.empleado_autoriza_id = {
                                    id: 461,
                                    name: "BRIGIDA MARTINEZ HERRERA"
                                };
                                break;
                            }
                            case "actualizar":
                            {
                                this.isEnabled = false;
                                Utilerias.resetObject(this.solicitud);
                                this.tituloModal = "Actualizar solicitud de viaticos";
                                this.modal = 1;
                                this.tipoAccion = 2;
                                axios.get("solicitud/viaticos/detalles/" + data.sv__id).then(res =>
                                {

                                    res = res.data[0];
                                    this.tipo = res["solicitud"][0]["tipo"];
                                    this.solicitud.fecha = res["solicitud"][0]["fecha_solicitud"];
                                    this.solicitud.fecha_pago = res["solicitud"][0]["fecha_pago"];
                                    this.solicitud.proyecto_id = {
                                        id: res["solicitud"][0]["proyecto_id"],
                                        nombre_corto: res["solicitud"][0]["nombre_proyecto"]
                                    };

                                    var childBeneficiarioSend = this.$refs.beneficiario;
                                    childBeneficiarioSend.setDatos(res["beneficiarios"], res["solicitud"][0]["tipo"], this.empresa);

                                    var ChilDetallesvSend = this.$refs.detallesv;
                                    ChilDetallesvSend.setDatos(res["detalles_listado"], res["solicitud"][0]["tipo"]);

                                    this.solicitud.origen_destino = res["solicitud"][0]["origen_destino"];
                                    this.solicitud.origen_destino_destino = res["solicitud"][0]["origen_destino_destino"];
                                    this.solicitud.fecha_salida = res["solicitud"][0]["fecha_salida"];
                                    this.solicitud.hora_estimada_salida = res["solicitud"][0]["hora_estimada_salida"];
                                    this.solicitud.fecha_operacion = res["solicitud"][0]["fecha_operacion"];
                                    this.solicitud.fecha_retorno = res["solicitud"][0]["fecha_retorno"];
                                    this.vehiculos_itinerario_viaticos = [];

                                    for (var i = 0; i < res["vehiculo"].length; i++)
                                    {
                                        let me = this;
                                        me.vehiculos_itinerario_viaticos.push(
                                        {
                                            unidad: res["vehiculo"][i]["unidad"],
                                            km_inicial: res["vehiculo"][i]["km_inicial"],
                                            empleado_operador_id: res["vehiculo"][i]["empleado_operador_id"],
                                            empleado_operador_name: res["vehiculo"][i]["nombre_operador"],
                                        });
                                    }

                                    this.solicitud.total_personas = res["solicitud"][0]["total_personas"];
                                    this.solicitud.empleado_supervisor_id = {
                                        id: res["solicitud"][0]["empleado_supervisor_id"],
                                        name: res["solicitud"][0]["nombre_supervisor"]
                                    };
                                    this.solicitud.empleado_elabora_id = {
                                        id: res["solicitud"][0]["empleado_elabora_id"],
                                        name: res["solicitud"][0]["nombre_elabora"]
                                    };
                                    this.solicitud.empleado_revisa_id = {
                                        id: res["solicitud"][0]["empleado_revisa_id"],
                                        name: res["solicitud"][0]["nombre_revisa"]
                                    };
                                    this.solicitud.empleado_autoriza_id = {
                                        id: res["solicitud"][0]["empleado_autoriza_id"],
                                        name: res["solicitud"][0]["nombre_autoriza"]
                                    };
                                    this.solicitud.id = res["solicitud"][0]["id"];
                                    var datos = [];
                                    for (var i = 0; i < res["empleados"].length; i++)
                                    {
                                        datos.push(
                                        {
                                            id: res["empleados"][i]["empleado_id"],
                                            name: res["empleados"][i]["nombre_empleado"]
                                        });
                                    }
                                    this.solicitud.personal_servicio_viaticos_id = datos;
                                });
                                break;
                            }
                        }
                    }
                }
            }
        },

        cerrarModal()
        {
            this.modal = 0;
            // this.tipo = "";
            var childBeneficiario = this.$refs.beneficiario;
            childBeneficiario.quitar_uno();
            this.vehiculos_itinerario_viaticos = [];
            Utilerias.resetObject(this.solicitud);
        },

        enviouno(data)
        {
            this.solicitud.beneficiario_uno = data;
        },

        envio_u(data)
        {
            this.solicitud.detalles_listado = data;
        },

        envio_d(data)
        {
            this.solicitud.detalles_totales = data;
        },

        envio_c(data)
        {
            this.solicitud.detalles_conceptos = data;
        },

        crear()
        {

            if (this.unidadtemporal == null) return;
            if (this.unidadtemporal.id == null)
            {
                toastr.warning("Seleccione una unidad");
                return;
            }
            if (this.vehiculos_temporal.empleado_operador_id.id == null)
            {
                toastr.warning("Seleccione el operador");
                return;
            }
            this.vehiculos_itinerario_viaticos.push(
            {
                unidad: this.unidadtemporal.modelo,
                km_inicial: this.unidadtemporal.placas,
                empleado_operador_id: this.vehiculos_temporal.empleado_operador_id.id,
                empleado_operador_name: this.vehiculos_temporal.empleado_operador_id.name,
                temp: true,
            });
            this.unidadtemporal = {};
            this.vehiculos_temporal = {};
        },

        guardar(nuevo)
        {
            this.$validator.validate().then(result =>
            {
                if (result)
                {
                    let me = this;
                    this.isGuardar_Loading = true;

                    // this.isLoading = true;
                    axios(
                    {
                        method: nuevo ? "POST" : "PUT",
                        url: nuevo ? "/solicitudviaticos" : "/solicitudviaticos/" + this.solicitud.id,
                        data:
                        {
                            "id": this.solicitud.id,
                            "fecha_solicitud": this.solicitud.fecha,
                            "fecha_pago": this.solicitud.fecha_pago,
                            "proyecto_id": this.solicitud.proyecto_id.id,
                            "personal_servicio_viaticos_id": this.solicitud.personal_servicio_viaticos_id == "" ? [] : this.solicitud.personal_servicio_viaticos_id,
                            "empleado_elabora_id": this.solicitud.empleado_elabora_id.id,
                            "empleado_revisa_id": this.solicitud.empleado_revisa_id.id,
                            "empleado_autoriza_id": this.solicitud.empleado_autoriza_id.id,
                            "empleado_supervisor_id": this.solicitud.empleado_supervisor_id == "" ? "" : this.solicitud.empleado_supervisor_id.id,
                            "beneficiario_uno": this.solicitud.beneficiario_uno,
                            "detalles_totales": this.solicitud.detalles_totales,
                            "detalles_listado": this.solicitud.detalles_listado,
                            "detalles_conceptos": this.solicitud.detalles_conceptos,
                            "origen_destino": this.solicitud.origen_destino,
                            "origen_destino_destino": this.solicitud.origen_destino_destino,
                            "fecha_salida": this.solicitud.fecha_salida,
                            "hora_estimada_salida": this.solicitud.hora_estimada_salida,
                            "fecha_operacion": this.solicitud.fecha_operacion,
                            "fecha_retorno": this.solicitud.fecha_retorno,
                            "total_personas": this.solicitud.total_personas,
                            "vehiculos_itinerario_viaticos": this.vehiculos_itinerario_viaticos,
                            "empresa": this.empresa,
                            "tipo": this.tipo,

                        }
                    }).then(response =>
                    {
                        this.isGuardar_Loading = false;

                        if (!response.data.status)
                        {
                            toastr.error("Error al guardar la solicitud");
                            return;
                        }
                        toastr.success(nuevo ? "Solicitud creada exitosamente" : "Solicitud actualizada exitosamente", "Correcto");

                        let me = this;
                        this.ObtenerSolicitudes();
                        me.cerrarModal();
                        me.getData();

                    }).catch(error =>
                    {
                        console.error(error);
                    });

                }
                else
                {
                    toastr.warning("Complete los campos requeridos")
                }
            });
        },

        limpiarBeneficiarioUno()
        {

            this.solicitud.beneficiario_uno = null;
        },

        limpiarBeneficiarioDos()
        {
            this.solicitud.beneficiario_dos = null;
        },

        maestro()
        {
            this.ver = 1;
        },

        enviaRevision(id, edo)
        {
            this.isLoading = true;
            swal(
            {
                title: "Esta seguro(a) de enviar?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#4dbd74",
                cancelButtonColor: "#f86c6b",
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar",
                reverseButtons: true
            }).then(result =>
            {
                if (result.value)
                {
                    axios.post("/estadosviaticos",
                    {
                        "id": id,
                        "edo": edo,
                        "empresa": this.empresa,
                    }).then(response =>
                    {
                        this.isLoading = false;
                        if (response.data.status)
                        {
                            if (edo == 2)
                            {
                                toastr.success("Solicitud de viaticos enviada a revisión", "Correcto");
                            }
                            this.getData();
                            this.ObtenerSolicitudes();
                        }
                        else
                        {
                            toastr.warning(response.data.respuesta, "Atención");
                        }

                    }).catch(error =>
                    {
                        console.log(error);
                    });
                }
                else
                {
                    this.isLoading = false;
                }
            });
        },

        decargarForVia(data, empresa)
        {
            window.open("/descargarformatoviatico/" + data + "&" + empresa, "_blank");

        },
        descargarnForFij(data, empresa)
        {
            window.open("/descargarnformatofij/" + data + "&" + empresa, "_blank");

        },

        /**
         * Obtener proyectos
         */
        ObtenerProyectos()
        {
            axios.get("generales/proyectos/1").then(res =>
            {
                this.optionsvs_proyecto = res.data.proyectos;
            });
        },

        /**
         * Obtener los empleados de la empresa seleccionada
         */
        ObtenerEmpleados()
        {
            axios.get("rh/empleados/obtenerporempresa/" + this.empresa).then(res =>
            {
                // TODO: Cambiar label de empleados
                var nombres = [];
                res.data.empleados.forEach(e =>
                {
                    nombres.push(
                    {
                        id: e.id,
                        name: e.nombre + " " + e.ap_paterno + " " + e.ap_materno,
                    });
                });
                this.vs_options = nombres;
            });
        },

        eliminar(id, empresa)
        {
            axios.get("eliminar/solicitud/viaticos/" + id + "&" + empresa).then(response =>
            {
                toastr.success("Eliminado Correctamente");
                this.ObtenerSolicitudes();
            }).catch(e =>
            {
                console.error(e);
            });
        },

        ObtenerSolicitudes()
        {
            this.$refs.myTable.refresh();
        },

        descargaViatico(id, tipo)
        {
            switch (tipo)
            {
                case 0:
                    this.descargarnForFij(id, this.empresa);
                    break;
                case 1:
                    this.decargarForVia(id, this.empresa);
                    break;
                case 2:
                    this.decargarForVia(id, this.empresa);
                    break;
            }
        },
    },

    mounted()
    {
        this.getData();
    }
}
</script>
