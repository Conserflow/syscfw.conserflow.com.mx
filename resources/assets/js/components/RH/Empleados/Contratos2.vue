<template>
<main>
    <div>
        <!-- Contratos del empleado -->
        <div class="card" v-show="tipoCard==2">
            <div class="card-header bg-white">
                <button type="button" @click="AbrirModalContrato(true)" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus mr-1"></i>Nuevo
                </button>
            </div>
            <div class="">
                <vue-element-loading :active="isObtenerContratosLoading" />
                <v-client-table :columns="columns_contratos" :data="list_contratos" :options="options_contratos">
                    <template slot="id" slot-scope="props">
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group dropup" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal mr-1"></i>Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <template v-if="props.row.condicion">
                                        <button type="button" @click="AbrirModalContrato(false,props.row)" class="dropdown-item">
                                            <i class="fas fa-edit mr-1"></i>Actualizar Contrato
                                        </button>
                                        <button type="button" class="dropdown-item" @click="DesactivarContrato(props.row)">
                                            <i class="fas fa-times"></i> Finalizar Contrato
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template slot="asd" slot-scope="props">
                        <button class="btn btn-outline-dark" @click="DescargarNuevoContrato(props.row.id)">
                            <i class="fas fa-file-pdf"></i>
                            <i class="fas fa-download"></i>
                        </button>
                    </template>
                    <template slot="condicion" slot-scope="props">
                        <template v-if="props.row.condicion">
                            <button type="button" class="btn btn-outline-success">Activo</button>
                        </template>
                        <template v-else>
                            <button type="button" class="btn btn-outline-danger">
                                {{props.row.motivo_fin}}
                            </button>
                        </template>
                    </template>
                </v-client-table>
            </div>
        </div>

        <!-- Contrato -->
        <div class="card" v-show="tipoCard==3">
            <div class="card-header bg-white border-0 mb-3">
                <span class="h4">{{tituloModal}}</span>
                <button type="button" @click="CerrarModalContrato()" class="btn btn-secondary float-sm-right mr-1">
                    <i class="fa fa-arrow-left mr-1"></i>Regresar
                </button>
            </div>
            <vue-element-loading :active="isGuardaContrato_loading" />
            <div class="container-fluid" style="min-height:80vh">
                <div class="form-group row">
                    <label class="col-md-2 form-control-label">Proyecto</label>
                    <div class="col-md-4">
                        <v-select v-validate="'required'" :options="list_proyectos" label="nombre_corto" v-model="contrato.proyecto" data-vv-name="Proyecto"></v-select>
                        <span class="text-danger">{{ errors.first("Proyecto") }}</span>
                    </div>
                    <label class="col-md-3 form-control-label">Tipo de Contrato</label>
                    <div class="col-md-3">
                        <select class="form-control" id="tipo_contrato_id" name="tipo_contrato_id" v-model="contrato.tipo_contrato_id" v-validate="'excluded:0'" data-vv-as="Tipo de Contrato">
                            <option v-for="item in list_tiposcontato" :value="item.id" :key="item.id">{{ item.nombre }}</option>
                        </select>
                        <span class="text-danger">{{ errors.first("Tipo de Contrato") }}</span>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 form-control-label" for="tipo_nomina_id">Tipo de Nómina</label>
                    <div class="col-md-4">
                        <input type="text" disabled readonly v-validate="'required'" class="form-control" v-model="contrato.nomina" data-vv-name="Tipo de Nómina">
                        <span class="text-danger">{{ errors.first("Tipo de Nómina") }}</span>
                    </div>
                    <label class="col-md-3 form-control-label" for="horario_id">Horario de Trabajo</label>
                    <div class="col-md-3">
                        <select v-validate="'required'" class="form-control" id="horario_id" name="horario_id" v-model="contrato.horario_id" data-vv-name="Horario de Trabajo">
                            <option v-for="item in list_horarios" :value="item.id" :key="item.id">{{ item.nombre }}</option>
                        </select>
                        <span class="text-danger">{{ errors.first("Horario de Trabajo") }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2">Inicio de Contrato</label>
                    <div class="col-md-4">
                        <input disabled readonly type="date" v-validate="'required'" name="fecha_ingreso" v-model="contrato.fecha_ingreso" class="form-control disabled" data-vv-name="Fecha de Inicio">
                        <span class="text-danger">{{ errors.first("Fecha de Inicio") }}</span>
                    </div>
                    <label class="col-md-3" for="text-input">Fin de Contrato</label>
                    <div class="col-md-3">
                        <input id="fechafin" type="date" v-validate="'required'" name="fecha_fin" v-model="contrato.fecha_fin" class="form-control" data-vv-name="Fecha de Fin">
                        <span class="text-danger">{{ errors.first("Fecha de Fin") }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 form-control-label">Puesto</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" disabled readonly v-validate="'required'" data-vv-name="Puesto" v-model="contrato.puesto">
                        <span class="text-danger">{{ errors.first("Puesto") }}</span>
                    </div>
                    <label class="col-md-3 form-control-label" for="tipo_ubicacion_id">Ubicación</label>
                    <div class="col-md-3">
                        <input type="text" disabled readonly v-validate="'required'" class="form-control" v-model="contrato.ubicacion" data-vv-name="Ubicación">
                        <span class="text-danger">{{ errors.first("Ubicación") }}</span>
                    </div>
                </div>

                <hr>
                <sueldo-2 ref="sueldo2"></sueldo-2>
                <br>
                <!-- Testigos -->
                <hr class="mx-3">
                <div class="form-group row">
                    <div class="col-6">
                        <label class="form-control-label col-3">Testigo 1</label>
                        <v-select v-validate="'required'" class="col" :options="list_empleados_testigos" v-model="testigo1" data-vv-name="Testigo 1" label="nombre_completo"></v-select>
                        <span class="text-danger">{{errors.first("Testigo 1")}}</span>
                    </div>
                    <div class="col-6">
                        <label class="form-control-label col-3">Testigo 2</label>
                        <v-select v-validate="'required'" class="col" :options="list_empleados_testigos" v-model="testigo2" data-vv-name="Testigo 2" label="nombre_completo"></v-select>
                        <span class="text-danger">{{errors.first("Testigo 2")}}</span>
                    </div>
                </div>
                <br>
                <div class="float-sm-right">
                    <button type="button" v-show="tipoAccionContrato==1" class="btn btn-secondary" @click="GuardarContrato(true)"> <i class="fas fa-save mr-1"></i>Guardar</button>
                    <button type="button" v-show="tipoAccionContrato==2" class="btn btn-secondary" @click="GuardarContrato()"><i class="fas fa-save mr-1"></i>Actualizar</button>
                </div>
                <br>
                <br>
            </div>
        </div>
    </div>
</main>
</template>

<script>
var config = require("../../Herramientas/config-vuetables-client").call(this);
const Sueldo2 = r => require.ensure([], () => r(require("./Sueldo2.vue")), "rh");

export default
{
    components:
    {
        "sueldo2": Sueldo2,
    },
    data()
    {
        return {
            empleado:
            {},
            tipoCard: 2,
            tipoAccionContrato: 1,
            PermisosCRUD:
            {},
            activeTab: " tab1",
            // Inicio
            url_contratos: "rh/contratos",
            list_empleados: [],
            columns_empleados: [
                "id",
                "nombre",
                "ap_paterno",
                "ap_materno",
                "inicio",
                "fin",
                "condicion"
            ],
            options_empleados:
            {
                headings:
                {
                    "id": "Acciones",
                },
                perPage: 20,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts,
                listColumns:
                {
                    condicion: [
                    {
                        id: 0,
                        text: "Sin Contrato"
                    },
                    {
                        id: 1,
                        text: "Activo"
                    },
                    {
                        id: 2,
                        text: "Por Vencer"
                    },
                    {
                        id: 3,
                        text: "Vencido"
                    }, ]
                },
            },
            empresa_id: 1,
            isEmpleadosContratoLoading: false,
            // Contratos
            isObtenerContratosLoading: false,
            nombre_empleado: "",
            empleado_id: 0,
            list_contratos: [],
            tituloModal: "",
            columns_contratos: [
                "id",
                "ubicacion",
                "proyecto",
                "puesto",
                "fecha_ingreso",
                "fecha_fin",
                "tipo_nomina",
                "tipo_contrato",
                "asd",
                "condicion",
            ],
            options_contratos:
            {
                headings:
                {
                    "id": "Acciones",
                    "fecha_ingreso": "Inicio",
                    "fecha_fin": "Fin",
                    "asd": "Contrato",
                    "condicion": "Estado",
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            },

            // Detalles
            contrato_id: 0,
            tituloModal_contrato: "",
            isGuardaContrato_loading: false,
            list_proyectos: [],
            list_tiposcontato: [],
            list_tipos_nomina: [],
            list_horarios: [],
            list_puestos: [],
            list_ubicaciones: [],
            contrato:
            {
                id: 0,
                fecha_ingreso: "",
                fecha_fin: "",
                nomina: "",
                puesto: "",
                puesto_id: 0,
                tipo_nomina_id: 0,
                empleado_id: 0,
                tipo_ubicacion_id: 0,
                ubicacion: "",
                horario_id: 0,
                tipo_contrato_id: 0,
                proyecto_id: 0,
                motivo: "",
            },
            list_empleados_testigos: [],
            // Testigos
            testigo1:
            {},
            testigo2:
            {},

            // Sueldos
            list_sueldos: [],
        }
    },
    methods:
    {
        /**
         * Obtener los empleados de la empresa seleccionada, para los testigos
         */
        ObtenerEmpleados()
        {
            this.isEmpleadosContratoLoading = true;
            axios.get("rh/contratos/obtenerempleados/" + this.empresa_id).then(res =>
            {
                this.isEmpleadosContratoLoading = false;
                if (res.data.status)
                {
                    this.list_empleados = res.data.empleados;
                    this.list_empleados_testigos = this.list_empleados.map(e =>
                    {
                        return {
                            "id": e.id,
                            "nombre_completo": `${e.nombre} ${e.ap_paterno} ${e.ap_materno}`
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
         * Obtener los contratos del empleado seleccionado
         */
        VerContratosEmpleado(emp, crud)
        {
            this.empleado = emp;
            this.PermisosCRUD = crud;
            this.empleado_id = emp.id;
            this.tipoCard = 2; // Mostrar contratos
            this.nombre_empleado = `${emp.nombre} ${emp.ap_paterno} ${emp.ap_materno}`;
            this.isObtenerContratosLoading = true;
            let emp_id = this.empleado.id_checador <= 2 ? 1 : 2; // 1. Conser 2. CSCT
            this.ObtenerEmpleados(emp_id);
            this.CargarContratosAux(emp.id);
        },

        /**
         * Obtener los contratos del empleado 
         */
        CargarContratosAux(emp_id)
        {
            axios.get("rh/contratos/obtener/" + emp_id).then(res =>
            {
                this.isObtenerContratosLoading = false;
                if (res.data.status)
                {
                    this.list_contratos = res.data.contratos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Desactivar el contrato seleccionado
         */
        async DesactivarContrato(contrato)
        {
            // Confirmar
            let res_eliminar = await swal(
            {
                title: "¿Esta seguro de desactivar este contrato?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#4dbd74",
                cancelButtonColor: "#f86c6b",
                confirmButtonText: "Aceptar",
                cancelButtonText: "Cancelar",
                reverseButtons: true
            });

            if (!res_eliminar.value) return; // Cancelado

            // Obtener los motivos de renuncia
            let tipos_renuncia = {
                "3": "ABANDONO DE EMPLEO",
                "1": "RENUNCIA",
                "2": "TERMINO DE PROYECTO",
            };
            // Motivo
            let motivo = await swal(
            {
                title: "Motivo de la Baja",
                input: "select",
                inputOptions: tipos_renuncia,
                confirmButtonText: "Continuar <i class='fas fa-arrow-right'> </i>",
                showCancelButton: true,
                customClass: "form-check form-check-inline",
                inputValidator: (result) =>
                {
                    return !result && "Se Requiere Elegir un Elemento"
                }
            });

            if (!motivo.value) return; // Cancelado

            axios.post(this.url_contratos + "/finalizar",
            {
                "id": contrato.id,
                "motivo_fecha_fin": motivo.value,
            }).then(res =>
            {
                if (res.data.status)
                {
                    toastr.success("Contrato Finalizado Correctamente");
                    this.CargarContratosAux(contrato.empleado_id);
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        CerrarModalContrato()
        {
            this.tipoCard = 2; // Contratos del empleados
            this.contrato = {
                proyecto:
                {},
                testigo_1:
                {},
                testigo_2:
                {},
                puesto:
                {},
            };
        },

        /**
         * Abrir modal para registro/actualización del contrato
         */
        AbrirModalContrato(nuevo, data = {})
        {
            this.isGuardaContrato_loading = true;
            this.CargarCatalogos(); // Cargar todos los catalogos
            this.tipoCard = 3; // Contrato
            if (nuevo)
            {
                // obtener fecha de alta de imss para fecha de ingreso
                this.tituloModal = "Registrar Contrato";
                this.tipoAccionContrato = 1;
                // Obtener puesto
                axios.get("rh/catalogos/puestos/" + this.empleado.puesto_id).then(res =>
                {
                    if (res.data.status)
                    {
                        this.contrato_id = res.data.puesto.id;
                        this.contrato.puesto = res.data.puesto.nombre;
                    }
                    else toastr.error(res.data.mensaje);
                })

                this.contrato.fecha_ingreso = this.empleado.fech_alta_imss;
                this.contrato.puesto_id = this.empleado.puesto_id;
                this.contrato.tipo_ubicacion_id = this.empleado.ubicacion_id;
                this.contrato.ubicacion = this.contrato.tipo_ubicacion_id == 1 ?
                    "Tehuacán, Puebla" : "Coatzacoalcos, Veracruz";
            }
            else
            {
                this.contrato_id = data.id;
                // Testigos
                this.tituloModal = "Actualizar Contrato";
                this.tipoAccionContrato = 2;
                this.contrato = {
                    ...data
                };
                this.contrato.fecha_ingreso = this.empleado.fech_alta_imss;
                this.contrato.nomina = data.tipo_nomina;
                this.tipo_nomina_id = data.tipo_nomina_id;

                this.contrato.puesto_id = data.puesto_id;
                this.contrato.puesto = data.puesto;

                this.contrato.proyecto = {
                    id: data.proyecto_id,
                    nombre_corto: data.proyecto
                };
                this.testigo1 = {
                    id: data.testigo1_id,
                    nombre_completo: data.testigo1
                };
                this.testigo2 = {
                    id: data.testigo2_id,
                    nombre_completo: data.testigo2
                };
            }
            // Tipo nomina 1 semal 2. quinceal
            if (this.empleado.id_checador == 1 || this.empleado.id_checador == 3)
            {
                this.contrato.tipo_nomina_id = 1;
                this.contrato.nomina = "Semanal";
            }
            else
            {
                this.contrato.tipo_nomina_id = 2;
                this.contrato.nomina = "Quincenal";
            }
            // Salarios
            this.$refs.sueldo2.CargarSueldos(nuevo, this.contrato.id, this.contrato.tipo_nomina_id,
                this.empleado.salario_neto);
            this.isGuardaContrato_loading = false;
        },

        /**
         * Cargar los catalogos del contrato
         */
        async CargarCatalogos()
        {
            // Proyectos
            let res = await axios.get("generales/proyectos/1");
            if (res.data.status) this.list_proyectos = res.data.proyectos;
            else toastr.errors(res.data.mensaje);
            // Tipos de contrato
            res = await axios.get("rh/catalogos/tiposcontrato/obtener");
            if (res.data.status) this.list_tiposcontato = res.data.tipocontrato;
            else toastr.errors(res.data.mensaje);
            // Horarios
            res = await axios.get("rh/catalogos/horarios/obtener");
            if (res.data.status) this.list_horarios = res.data.horarios;
            else toastr.errors(res.data.mensaje);
        },

        /**
         * Guarddar o actualizar contrato
         */
        GuardarContrato(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;

                // VAlidar campos
                let error = this.CamposIncompletos();

                if (error != "ok")
                {
                    toastr.warning("Ingrese el campo " + error);
                    return;
                }

                this.isGuardaContrato_loading = true;

                let data = new FormData();
                if (!nuevo) data.append("id", this.contrato.id);
                data.append("proyecto_id", this.contrato.proyecto.id);
                data.append("tipo_contrato_id", this.contrato.tipo_contrato_id);
                data.append("tipo_nomina_id", this.contrato.tipo_nomina_id);
                data.append("horario_id", this.contrato.horario_id);
                data.append("fecha_ingreso", this.contrato.fecha_ingreso);
                data.append("fecha_fin", this.contrato.fecha_fin);
                data.append("puesto_id", this.contrato.puesto_id);
                data.append("tipo_ubicacion_id", this.contrato.tipo_ubicacion_id);
                data.append("testigo1_id", this.testigo1.id);
                data.append("testigo2_id", this.testigo2.id);
                data.append("empleado_id", this.empleado_id);
                data.append("empresa_id", this.empresa_id);

                axios.post(this.url_contratos + "/guardar/", data).then(res =>
                {
                    this.isGuardaContrato_loading = false;
                    if (res.data.status)
                    {
                        if (nuevo)
                        {
                            toastr.success("Contrato registrato correctamente");
                            toastr.info("Ahora debe registrar un sueldo");
                            // Obtener ID del contrato para registrar
                            let c_id = res.data.c_id;
                            this.$refs.sueldo2.CargarSueldos(0, c_id, this.contrato.tipo_nomina_id,
                                this.empleado.salario_neto);
                        }
                        else
                        {
                            toastr.success("Contrato actualizado correctamente");
                        }
                        this.CargarContratosAux(this.empleado_id)
                    }
                    else
                    {
                        toastr.errors(res.data.mensaje);
                    }
                }).catch(x =>
                {
                    toastr.error("Error al registrar el contrato");
                })
            });
        },
        /**
         * Comprueba que todos los campos tengan información
         */
        CamposIncompletos()
        {
            if (this.contrato.proyecto == null) return "Proyecto";
            if (this.contrato.proyecto.id == null) return "Proyecto";
            if (this.testigo1 == null) return "Testigo 1";
            if (this.testigo1.id == null) return "Testigo 1";
            if (this.testigo2 == null) return "Testigo 2";
            if (this.testigo2.id == null) return "Testigo 2";
            return "ok";
        },

        /**
         * Descarga el nuevo contrato
         */
        DescargarNuevoContrato(id)
        {
            window.open(this.url_contratos + "/nuevocontrato/" + id);
        },
    },
    mounted()
    {

    }
}
</script>
