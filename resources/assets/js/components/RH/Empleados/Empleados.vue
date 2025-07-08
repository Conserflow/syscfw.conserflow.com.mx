<template>
<main class="main">
    <div class="">
        <div class="card" v-show="tipo_card==1">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Empleados
                <button v-show="PermisosCRUD.Create" type="button" @click="AbrirModalEmpleado()" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus mr-1"></i>Nuevo
                </button>
                <button v-show="PermisosCRUD.Download" type="button" @click="DescargarEmpleados()" class="btn btn-dark float-sm-right mr-1">
                    <i class="fas fa-file-excel mr-1"></i>Descargar
                </button>
            </div>
            <div class="card-body">
                <vue-element-loading :active="isObtenerEmpleados_loading" />
                <v-client-table :columns="columns_empleados" :data="list_empleados" :options="options_empleados">
                    <template slot="id" slot-scope="props">
                        <div class="btn-group" role="group">
                            <div class="btn-group dropup" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <template v-if="props.row.condicion">
                                        <button v-show="PermisosCRUD.Read" type="button" class="dropdown-item" @click="FormatoAlta(props.row)">
                                            <i class="far fa-file-pdf"></i>Formato Alta Empleado</button>
                                        <button v-show="PermisosCRUD.Read" type="button" class="dropdown-item" @click="AbrirModalEmpleado(false,props.row)">
                                            <i class="fas fa-diagnoses"></i>Detalles Empleado</button>
                                        <button v-show="PermisosCRUD.Delete" v-if="props.row.condicion" type="button" class="dropdown-item" @click="ActivarDesactivar(props.row.id,0)">
                                            <i class="fas fa-ban"></i>Desactivar</button>
                                        <button type="button" class="btn btn-dark dropdown-item" @click="DescargarQR(props.row.id)">
                                            <i class="fas fa-qrcode"></i>QR</button>
                                    </template>
                                    <template v-else>
                                        <button v-show="PermisosCRUD.Read" type="button" class="dropdown-item" @click="AbrirModalEmpleado(false,props.row)">
                                            <i class="fas fa-diagnoses"></i>Detalles Empleado</button>
                                        <button v-show="PermisosCRUD.Update" type="button" class="dropdown-item" @click="ActivarDesactivar(props.row.id,1)">
                                            <i class="icon-check"></i>Activar
                                        </button>
                                    </template>
                                    <button v-show="PermisosCRUD.Read" type="button" class="dropdown-item" @click="VerHistorial(props.row)">
                                        <i class="fas fa-list"></i>Historial</button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template slot="condicion" slot-scope="props">
                        <button v-if="props.row.condicion" type="button" class="btn btn-outline-success">Activo</button>
                        <button v-else type="button" class="btn btn-outline-danger">Baja</button>
                    </template>
                    <template slot="id_checador" slot-scope="props">
                        <button v-if="props.row.id_checador==1" type="button" class="btn btn-outline-primary">Conserflow</button>
                        <button v-else-if="props.row.id_checador==2" type="button" class="btn btn-outline-warning">Conserflow</button>
                        <button v-else-if="props.row.id_checador==3" type="button" class="btn btn-outline-success">CSCT</button>
                        <button v-else-if="props.row.id_checador==4" type="button" class="btn btn-outline-info">CSCT</button>
                        <button v-else type="button" class="btn btn-outline-danger">No asignado</button>
                    </template>
                    <template slot="c_condicion" slot-scope="props">
                        <button v-if="props.row.c_condicion==0" class="btn btn-secondary">
                            <i class="mr-1"></i>Sin contrato
                        </button>
                        <button v-if="props.row.c_condicion==1" class="btn btn-success">
                            <i class="fas fa-check mr-1"></i>Activo
                        </button>
                        <button v-if="props.row.c_condicion==2" class="btn btn-warning">
                            <i class="fas fa-exclamation mr-1"></i>Por vencer
                        </button>
                        <button v-if="props.row.c_condicion==3" class="btn btn-danger">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Vencido
                        </button>
                    </template>
                </v-client-table>
            </div>
        </div>

        <!-- Fin ejemplo de tabla Listado -->
        <div class="card" v-show="tipo_card==2">
            <div class="card-header">
                <h6><i class="fa fa-align-justify"></i> {{nombre_empleado}}</h6>
                <button style="margin-top:-2rem" type="button" @click="Regresar()" class="btn btn-secondary float-sm-right">
                    <i class="fa fa-arrow-left mr-1"></i>Atrás
                </button>
            </div>
            <div class="accordion">
                <div class="card ">
                    <div class="card-header bg-dark" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-dark btn callout callout-light btn-lg btn-block text-left " data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Datos Generales
                            </button>
                        </h5>
                    </div>
                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <alta ref="alta" @regresar="RegresarEmpleados"></alta>
                        </div>
                    </div>
                </div>
                <div v-show="tipoAccion==2">
                    <div class="card" ref="collapseContratos">
                        <div class="card-header  bg-dark" id="headingContratos">
                            <h5 class="mb-0">
                                <button class="btn btn-dark btn callout callout-light btn-lg btn-block text-left" data-toggle="collapse" data-target="#collapseContratos" aria-expanded="false" aria-controls="collapseContratos">
                                    Contratos
                                </button>
                            </h5>
                        </div>
                        <div id="collapseContratos" class="collapse" aria-labelledby="headingContratos" data-parent="#accordion">
                            <div class="card-body">
                                <contratos ref="contratos"></contratos>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header  bg-dark" id="headingTwo">
                            <h5 class="mb-0">
                                <button class="btn btn-dark btn callout callout-light btn-lg btn-block text-left" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Contacto
                                </button>
                            </h5>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                            <div class="card-body">
                                <contacto ref="contacto"></contacto>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header bg-dark" id="headingThree">
                            <h5 class="mb-0">
                                <button class="btn btn-dark btn callout callout-light btn-lg btn-block text-left" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Datos Bancarios
                                </button>
                            </h5>
                        </div>
                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                            <div class="card-body">
                                <datosbancarios ref="datosbancarios"></datosbancarios>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header bg-dark" id="headingFour">
                            <h5 class="mb-0">
                                <button class="btn btn-dark btn callout callout-light btn-lg btn-block text-left" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    Familiares
                                </button>
                            </h5>
                        </div>
                        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
                            <div class="card-body">
                                <familiares ref="familiares"></familiares>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header bg-dark" id="headingSeven">
                            <h5 class="mb-0">
                                <button class="btn btn-dark btn callout callout-light btn-lg btn-block text-left" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                    Expedientes
                                </button>
                            </h5>
                        </div>
                        <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion">
                            <div class="card-body">
                                <expedientes ref="expedientes"></expedientes>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header bg-dark" id="headingEight">
                            <h5 class="mb-0">
                                <button class="btn btn-dark btn callout callout-light btn-lg btn-block text-left" data-toggle="collapse" data-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                                    Dirección
                                </button>
                            </h5>
                        </div>
                        <div id="collapseEight" class="collapse" aria-labelledby="headingEight" data-parent="#accordion">
                            <div class="card-body">
                                <direccion ref="direccion"></direccion>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header bg-dark" id="headingNine">
                            <h5 class="mb-0">
                                <button class="btn btn-dark btn callout callout-light btn-lg btn-block text-left" data-toggle="collapse" data-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                                    Beneficiarios
                                </button>
                            </h5>
                        </div>
                        <div id="collapseNine" class="collapse" aria-labelledby="headingNine" data-parent="#accordion">
                            <div class="card-body">
                                <beneficiarios ref="beneficiarios"></beneficiarios>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin de listado componentes -->
    </div>

    <!--Inicio del modal agregar/actualizar-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_historial}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Historial de {{nombre_empleado}}</h4>
                    <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                        <span aria-hidden="true">X</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">CURP</th>
                                <th scope="col">NSS</th>
                                <th scope="col">Fecha Alta</th>
                                <th scope="col">Fecha Baja</th>
                                <th scope="col">Proyecto</th>
                                <th scope="col">Puesto</th>
                                <th scope="col">Salario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr :key="i" v-for="(h,i) in list_historial">
                                <td scope="col">{{i+1}}</td>
                                <td scope="col">{{h.curp}}</td>
                                <td scope="col">{{h.nss}}</td>
                                <td scope="col">{{h.fecha_alta=="0001-01-01"?"N/D":h.fecha_alta}}</td>
                                <td scope="col">{{h.fecha_baja=="0001-01-01"?"N/D":h.fecha_baja}}</td>
                                <td scope="col">{{h.proyecto}}</td>
                                <td scope="col">{{h.puesto}}</td>
                                <td scope="col">{{h.salario_neto}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" @click="CerrarHistorial()">
                        <i class="fas fa-times mr-1"></i>Cerrar
                    </button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</main>
</template>

<script>
import Utilerias from "../../Herramientas/utilerias.js";
var config = require("../../Herramientas/config-vuetables-client").call(this);
const Contratos = r => require.ensure([], () => r(require("./Contratos2.vue")), "rh");
const Familiares = r => require.ensure([], () => r(require("./../Familiares/Familiares.vue")), "rh");
const Beneficiarios = r => require.ensure([], () => r(require("./../Beneficiarios/Beneficiarios.vue")), "rh");
const Contacto = r => require.ensure([], () => r(require("./Contactos.vue")), "rh");
const DatosBancarios = r => require.ensure([], () => r(require("./Datos-ban-emp.vue")), "rh");
const Expedientes = r => require.ensure([], () => r(require("./Expedientes.vue")), "rh");
const Alta = r => require.ensure([], () => r(require("./Alta.vue")), "rh");
const Direccion = r => require.ensure([], () => r(require("./Direccion-Empleado.vue")), "rh");

export default
{
    components:
    {
        "contratos": Contratos,
        "familiares": Familiares,
        "contacto": Contacto,
        "datosbancarios": DatosBancarios,
        "expedientes": Expedientes,
        "alta": Alta,
        "direccion": Direccion,
        "beneficiarios": Beneficiarios,
    },
    data()
    {
        return {
            PermisosCRUD:
            {},
            isObtenerEmpleados_loading: false,
            tipo_card: 1,
            url: "rh/empleados",
            nombre_empleado: "",
            empleado:
            {},
            tipoAccion: 0,
            columns_empleados: [
                "id",
                "nombre",
                "ap_paterno",
                "ap_materno",
                "c_inicio",
                "c_fin",
                "c_condicion",
                "id_checador",
                "fech_alta_imss",
                "updated_at",
                "condicion",
            ],
            list_empleados: [],
            options_empleados:
            {
                headings:
                {
                    "id": "Acciones",
                    "nombre": "Nombre",
                    "ap_paterno": "Apellido Paterno",
                    "ap_materno": "Apellido Materno",
                    "fech_alta_imss": "Alta IMSS",
                    "c_inicio": "Inicio",
                    "c_fin": "Fin",
                    "c_condicion": "Contrato",
                    "condicion": "Estado",
                    "id_checador": "Empresa",
                    "updated_at": "Ultima Act.",
                },
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
                        text: "ACTIVO"
                    },
                    {
                        id: 0,
                        text: "DADO DE BAJA"
                    }],
                    "sexo": [
                    {
                        id: 1,
                        text: "MASCULINO"
                    },
                    {
                        id: 0,
                        text: "FEMENINO"
                    },
                    {
                        id: null,
                        text: "SIN DATO"
                    }],
                    "id_checador": [
                    {
                        id: 1,
                        text: "Conserflow Semanal"
                    },
                    {
                        id: 2,
                        text: "Conserflow Quincenal"
                    },
                    {
                        id: 3,
                        text: "CSCT Semanal"
                    },
                    {
                        id: 4,
                        text: "CSCT Quincenal"
                    }]
                },
                texts: config.texts
            },
            list_historial: [],
            isHistorial_loading: false,
            ver_modal_historial: false,
        }
    },
    methods:
    {

        /**
         * Mostrar card para registro/actualización del empleado
         * @nuevo bool Registro o actualización
         * @empleado Object Datos del empleado
         * @reciente bool Si es recien creado o ya existe en DB
         */
        AbrirModalEmpleado(nuevo = true, empleado = {}, reciente = false)
        {
            this.tipo_card = 2;
            var childAlta = this.$refs.alta;
            if (nuevo)
            {
                this.nombre_empleado = " Registrar empleado";
                this.tipoAccion = 1;
                this.empleado = {};
            }
            else
            {
                this.nombre_empleado = `${empleado.nombre} ${empleado.ap_paterno} ${empleado.ap_materno}`;
                this.tipoAccion = 2;
                this.empleado = empleado;
                childAlta.CargarEmpleado(empleado, this.PermisosCRUD, reciente);
                this.VerCardsDetalles();

            }
        },

        /**
         * Descargar formato de alta del empleado
         */
        FormatoAlta(empleado)
        {
            window.open(`${this.url}/formatoalta/${empleado.id}`);
        },

        /**
         * Generar el código QR
         */
        DescargarQR(empleado_id)
        {
            window.open(this.url + "/qr/" + empleado_id, "_blank");
        },

        /**
         * Mostrar las cards de los detalles del empleado
         */
        VerCardsDetalles()
        {
            var childContratos = this.$refs.contratos;
            childContratos.VerContratosEmpleado(this.empleado, this.PermisosCRUD);

            var childContacto = this.$refs.contacto;
            childContacto.CargarContacto(this.empleado, this.PermisosCRUD);

            var childDatosBan = this.$refs.datosbancarios;
            childDatosBan.CargarDatosBancarios(this.empleado, this.PermisosCRUD);

            var childFamiliares = this.$refs.familiares;
            childFamiliares.CargarFamiliares(this.empleado);

            var childExpedientes = this.$refs.expedientes;
            childExpedientes.CargarExpediente(this.empleado);
            // TODO:
            var childDirecciones = this.$refs.direccion;
            childDirecciones.cargarDirecciones(this.empleado);
            // TODO:
            var childBeneficiarios = this.$refs.beneficiarios;
            childBeneficiarios.cargarbeneficiario(this.empleado);
        },

        /**
         * Obtener todos los empleados registrados
         */
        ObtenerEmpleados()
        {
            this.isObtenerEmpleados_loading = true;
            axios.get(this.url).then(res =>
            {
                this.isObtenerEmpleados_loading = false;
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
         * Activar o desactivar el empleado
         */
        async ActivarDesactivar(id, condicion)
        {
            let mensaje = condicion ? "Activado correctamente" : "Deactivado correctamente";
            let fecha_baja = "";
            if (!condicion)
            {
                let resp = await Swal.fire(
                {
                    title: "Fecha de Baja",
                    html: "<input id='txtFechaBaja' type='date' class='form-control'>",
                    showCancelButton: true,
                    confirmButtonText: "Aceptar",
                    cancellButtonText: "Aceptar",
                });
                if (resp.value)
                {
                    fecha_baja = $("#txtFechaBaja").val();
                    var hoy = new Date();
                    let fecha_hoy = new Date();
                    let fecha_fin = new Date(fecha_baja);
                    if (fecha_fin > fecha_hoy)
                    {
                        toastr.warning("Fecha fin no puede ser mayor a hoy");
                        return;
                    }
                }
                else return;
            }
            // Enviar
            axios.post(this.url + "/actdesact/",
            {
                id,
                condicion,
                fecha_baja
            }).then(res =>
            {
                if (res.data.status)
                {
                    toastr.success(mensaje);
                    this.ObtenerEmpleados();
                }
                else toastr.error(res.data.mensaje);
            })
        },

        /**
         * Cerrar modal de registro de empleados
         */
        Regresar()
        {
            this.tipo_card = 1;
            this.empleado = {};
            var childAlta = this.$refs.alta;
            childAlta.Limpiar();
        },

        /**
         * Descarga el reporte general de los empleados
         */
        DescargarEmpleados()
        {
            window.open("rh/empleados/reportegeneral");
        },

        /**
         * Asignar un checador al empleado seleccionado
         */
        AsignarChecador(empleado)
        {
            let x = this;
            Swal.fire(
            {
                title: "Asignar checador",
                input: "select",
                inputOptions:
                {
                    "1": "Conserflow - Semanal",
                    "2": "Conserflow - Quincenal",
                    "3": "CSCT - Semanal",
                    "4": "CSCT - Quincenal"
                },
                showCancelButton: true
            }).then(function (result)
            {
                if (result.value)
                {
                    if (result.value == null) return;
                    if (result.value != "")
                    {
                        axios(
                        {
                            method: "POST",
                            url: x.url + "/asignarchecador",
                            data:
                            {
                                id_checador: result.value,
                                empleado_id: empleado.id
                            },
                        }).then((res) =>
                        {
                            if (res.data.status)
                            {
                                toastr.success("", "Guardado correctamente");
                                x.ObtenerEmpleados();
                            }
                            else
                            {
                                toastr.error("", "Error");
                            }
                        });
                    }
                }
            });
        },

        /**
         * Muestra el historial de las moficicaciones del empleado
         */
        VerHistorial(empleado)
        {
            this.nombre_empleado = `${empleado.nombre} ${empleado.ap_paterno} ${empleado.ap_materno}`;
            this.ver_modal_historial = true;
            this.isHistorial_loading = true;
            axios.get("rh/empleado/obtenerhistorial/" + empleado.id).then(res =>
            {
                this.isHistorial_loading = true;
                if (res.data.status)
                {
                    this.list_historial = res.data.historial;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        CerrarHistorial()
        {
            this.list_historial = [];
            this.ver_modal_historial = false;
        },
        RegresarEmpleados(emp)
        {
            this.ObtenerEmpleados();
            this.AbrirModalEmpleado(false, emp, true);
        },

        /**
         * Descarga el reporte general de los empleados
         */
        DescargarEmpleados()
        {
            window.open("rh/empleados/reportegeneral");
        },

    },
    mounted()
    {
        this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
        this.ObtenerEmpleados();
    }
}
</script>
