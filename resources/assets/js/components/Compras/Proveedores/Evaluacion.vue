<template>
<main class="main">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Evaluación de Proveedores - {{anio}}
                <button v-show="PermisosCrud.Download" class="btn btn-dark float-sm-right" @click="DescargarReporte">
                    <i class="fas fa-download mr-1"></i>Reporte
                </button>
                <div class="dropdown float-sm-right">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Año
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <button class="dropdown-item" type="button" @click="anio = 2020;ObtenerProveedores();">2020</button>
                        <button class="dropdown-item" type="button" @click="anio = 2021;ObtenerProveedores();">2021</button>
                        <button class="dropdown-item" type="button" @click="anio = 2022;ObtenerProveedores();">2022</button>
                        <button class="dropdown-item" type="button" @click="anio = 2023;ObtenerProveedores();">2023</button>
                        <button class="dropdown-item" type="button" @click="anio = 2024;ObtenerProveedores();">2024</button>
                    </div>
                </div>
            </div>
            <vue-element-loading :active="isLoading_proveedores" />
            <div class="card-body">
                <v-client-table :columns="columns" :data="list_proveedores" :options="options_proveedores" ref="myTable">
                    <template slot="id" slot-scope="props">
                        <button v-show="PermisosCrud.Create" type="button" class="btn btn-outline-dark" @click="AbrirModalEvaluacion(props.row)">
                            <i class="fas fa-tasks"></i>
                        </button>
                        <template v-if="props.row.total_evaluacion != null">
                            <button v-show="PermisosCrud.Download" type="button" class="btn btn-outline-dark" @click="DescargrEvaluacion(props.row)">
                                <i class="fas fa-download"></i>
                            </button>
                        </template>
                    </template>

                    <template slot="total_evaluacion" slot-scope="props">
                        <template v-if="props.row.total_evaluacion == null">
                            <span>N/D</span>
                        </template>
                        <template v-else>
                            <span v-if="props.row.total_evaluacion >= 54" class="text-success">
                                APROBADO
                            </span>
                            <span v-if="props.row.total_evaluacion >= 36 && props.row.total_evaluacion <= 53" class="text-warning">
                                CONDICIONADO
                            </span>
                            <span v-if="props.row.total_evaluacion >= 18 && props.row.total_evaluacion <= 35" class="text-danger">
                                No APTO
                            </span>
                        </template>
                    </template>
                </v-client-table>
            </div>
        </div>
        <!-- Fin ejemplo de tabla Listado -->
    </div>

    <!--Inicio del modal agregar/actualizar-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <vue-element-loading :active="isLoading" />
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="CerrarModalEvaluacion()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label></label>
                            </div>
                            <div class="form-group col-md-10">
                                <label><b>INSTRUCCIONES: Selecionar el puntaje correspondiente al desempeño observado.</b></label>
                            </div>
                        </div>
                        <div class="accordion" id="accordion">
                            <div class="card">
                                <div class="card-header" id="headingOne">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            A) ATENCIÓN TELEFÓNICA / CORREO ELECTRÓNICO
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                                    <div class="card-body">

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">
                                                Atención recibida (cortesía, amabilidad)
                                            </label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.uno">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">
                                                Rapidez en la atención
                                            </label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.dos">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">
                                                Agilidad ante un problema, duda, sugerencia o requerimiento.
                                            </label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.tres">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion" id="accordionTwo">
                            <div class="card">
                                <div class="card-header" id="headingTwo">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                            B) ATENCIÓN COMERCIAL
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionTwo">
                                    <div class="card-body">

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">
                                                Trato personal recibido (cortesía, amabilidad)
                                            </label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.cuatro">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">
                                                Actitud y atención a la hora de hacer una consulta o reclamación</label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.cinco">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">
                                                Facilidad para contactar con la persona adecuada
                                            </label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.seis">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">
                                                Nivel de información recibido sobre los servicios
                                            </label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.siete">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">
                                                Claridad de las cotizaciones, cumple con sus requisitos y requerimientos de forma
                                            </label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.ocho">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">Cotización oportuna</label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.nueve">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion" id="accordionThree">
                            <div class="card">
                                <div class="card-header" id="headingThree">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                                            C) SERVICIO DE ADMINISTRACIÓN Y FACTURACION
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionThree">
                                    <div class="card-body">

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">
                                                Tiempo de respuesta respecto a su factura
                                            </label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.diez">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">
                                                Actitud y atención a la hora de hacer una consulta o reclamación
                                            </label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.once">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">
                                                Facilidad para contactar con la persona adecuada
                                            </label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.doce">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">
                                                Nivel de información de las facturas enviadas
                                            </label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.trece">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">
                                                Nivel de satisfacción general con este servicio
                                            </label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.catorce">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion" id="accordionFour">
                            <div class="card">
                                <div class="card-header" id="headingFour">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
                                            D) PRODUCTO/SERVICIO
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionFour">
                                    <div class="card-body">

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">
                                                Cumplimiento del plazo de entrega acordado
                                            </label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.quince">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">Cumplimiento en las cantidades entregadas de acuerdo con lo acordado</label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.diesiseis">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">Cumplimiento a las especificaciones de embalaje y transporte de acuerdo con lo solicitado</label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.diesisiete">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-6 form-control-label">
                                                Cumplimiento a las especificaciones del producto de acuerdo con lo solicitado
                                            </label>
                                            <div class="col-md-3">
                                                <select class="form-control" v-model="calificacion.diesiocho">
                                                    <option value="4">Excelente</option>
                                                    <option value="3">Bien</option>
                                                    <option value="2">Regular</option>
                                                    <option value="1">Deficiente</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Evaluador</label>
                                <v-select :options="listaEmpleados" id="elabora_empleado_id" v-validate="'required'" name="elabora_empleado_id" v-model="calificacion.evaluador" label="name" data-vv-name="Elabora empleado"></v-select>
                                <span class="text-danger">{{errors.first("Elabora empleado")}}</span>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Fecha de evaluacion</label>
                                <input type="date" class="form-control" v-model="calificacion.fecha">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" @click="CerrarModalEvaluacion()"><i class="fas fa-window-close"></i>Cerrar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-secondary" @click="GuardarEvaluacion(1)"><i class="fas fa-save"></i>Guardar</button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-secondary" @click="GuardarEvaluacion(0)"><i class="fas fa-save"></i>Actualizar</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->

    <!-- Modal registro de provedor -->
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
            n_temp: 0,
            banco_edit:
            {},
            tipo_guardar: 1,
            anio: 1,
            ListBancos_temp: [],
            ListBancos: [],
            listaEmpleados: [],
            temp_proveedor_cuenta: '',
            temp_proveedor_clabe: '',
            temp_proveedor_banco: '',
            columnsProvedores: ["id", "banco", "cuenta", "clabe"],
            tableDataProveedores: [],
            optionsProveedores:
            {
                headings:
                {
                    clabe: "Clabe",
                    banco: "Banco de transferencia",
                    cuenta: "No. de Cuenta",
                    id: 'Acciones',
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                texts: config.texts,
                filterable: false,
            },
            modalProveedor: 0,
            PermisosCrud:
            {},
            Proveedor:
            {
                nombre: '',
                razon_social: '',
                direccion: '',
                banco_transferencia: '',
                // titular_cuenta : '',
                numero_cuenta: '',
                clabe: '',
                limite_credito: 0,
                condicion: 0,
                categoria: '',
                condicion_pago: '',
                giro: '',
                rfc: '',
                ciudad: '',
                estado: '',
                contacto: '',
                telefono: '',
                correo: '',
                pagina: '',
                descripcion: '',
                tipo_moneda: 0,
                tipo_cambio: '',
            },
            calificacion:
            {
                id: 0,
                uno: '',
                dos: '',
                tres: '',
                cuatro: '',
                cinco: '',
                seis: '',
                siete: '',
                ocho: '',
                nueve: '',
                diez: '',
                once: '',
                doce: '',
                trece: '',
                catorce: '',
                quince: '',
                diesiseis: '',
                diesisiete: '',
                diesiocho: '',
                fecha: '',
                evaluador: '',
            },
            listaProveedores: [],
            modal: 0,
            tituloModal: '',
            tipoAccion: 0,
            isLoading: false,
            isLoading_proveedores: false,
            columns: [
                'id',
                'nombre',
                'razon_social',
                'rfc',
                'total_evaluacion',
                'direccion'
            ],
            list_proveedores: [],
            options_proveedores:
            {
                headings:
                {
                    id: 'Acciones',
                    nombre: 'Nombre',
                    razon_social: 'Razón Social',
                    direccion: 'Dirección',
                    total_eval: 'Calificación',

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
    computed:
    {},
    methods:
    {
        getData()
        {
            let d = new Date();
            this.anio = d.getFullYear();
            this.ObtenerProveedores();
            this.PermisosCrud = Utilerias.getCRUD(this.$route.path);
            this.listaEmpleados.push(
            {
                id: 154,
                name: 'VALERIA HERNANDEZ MARTINEZ'
            },
            {
                id: 71,
                name: 'ERIKA HERNANDEZ MENDEZ'
            },
            {
                id: 1071,
                name: 'JAZMIN NAYELI LOPEZ MAVIL'
            });
        },

        /**
         * Obtiene los proveedores registrados y la evaluación del año seleccionado
         */
        ObtenerProveedores()
        {
            this.isLoading_proveedores = true;
            axios.get("compras/evlauacion/obtenerproveedores/" + this.anio).then(res =>
            {
                this.isLoading_proveedores = false;
                if (res.data.status)
                {
                    this.list_proveedores = res.data.proveedores;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Registrar evaluación de proveedor
         */
        GuardarEvaluacion(nuevo)
        {
            this.$validator.validate().then(result =>
            {
                if (!result) return;
                this.isLoading = true;
                let data = new FormData();
                if (!nuevo) data.append("id", this.calificacion.id);
                data.append("uno", this.calificacion.uno);
                data.append("dos", this.calificacion.dos);
                data.append("tres", this.calificacion.tres);
                data.append("cuatro", this.calificacion.cuatro);
                data.append("cinco", this.calificacion.cinco);
                data.append("seis", this.calificacion.seis);
                data.append("siete", this.calificacion.siete);
                data.append("ocho", this.calificacion.ocho);
                data.append("nueve", this.calificacion.nueve);
                data.append("diez", this.calificacion.diez);
                data.append("once", this.calificacion.once);
                data.append("doce", this.calificacion.doce);
                data.append("trece", this.calificacion.trece);
                data.append("catorce", this.calificacion.catorce);
                data.append("quince", this.calificacion.quince);
                data.append("diesiseis", this.calificacion.diesiseis);
                data.append("diesisiete", this.calificacion.diesisiete);
                data.append("diesiocho", this.calificacion.diesiocho);
                data.append("evaluador", this.calificacion.evaluador.id);
                data.append("fecha", this.calificacion.fecha);
                data.append("proveedor_id", this.calificacion.proveedor_id);
                axios.post('compras/evaluacion/guardar', data).then(res =>
                {
                    this.isLoading = false;
                    if (res.data.status)
                    {
                        this.CerrarModalEvaluacion();
                        this.ObtenerProveedores();
                        if (nuevo)
                            toastr.success('Calificación Agregada Correctamente');
                        else
                            toastr.success('Calificación Actualizada Correctamente');
                    }
                    else
                    {
                        toastr.error(res.data.mensaje);
                    }
                });

            });
        },

        /**
         * Cerrar modal de evaluación
         */
        CerrarModalEvaluacion()
        {
            this.modal = 0;
            this.LimpiarCalificacion();
            this.tituloModal = '';
            Utilerias.resetObject(this.calificacion);
        },

        /**
         * Borrar los datos de la calificación
         */
        LimpiarCalificacion()
        {
            let f = new Date();
            let anio = f.getFullYear();
            let mes = (f.getMonth() + 1).toString().padStart(2, "0");
            let dia = (f.getDate()).toString().padStart(2, "0");
            // TODO: Fecha temporal para terminar evaluaciones del 2021
            let fecha = `${2021}-${12}-${22}`;
            this.calificacion = {
                id: 0,
                uno: 1,
                dos: 1,
                tres: 1,
                cuatro: 1,
                cinco: 1,
                seis: 1,
                siete: 1,
                ocho: 1,
                nueve: 1,
                diez: 1,
                once: 1,
                doce: 1,
                trece: 1,
                catorce: 1,
                quince: 1,
                diesiseis: 1,
                diesisiete: 1,
                diesiocho: 1,
                fecha,
                evaluador:
                {},
            }
        },

        /**
         * Abrir modal para registro/actualización de la evaluación
         */
        AbrirModalEvaluacion(evaluacion)
        {
            this.modal = 1;
            let tipo = "";
            if (evaluacion.ep_id == null)
            {
                this.LimpiarCalificacion();
                this.calificacion.proveedor_id = evaluacion.id;
                // Nuevo
                tipo = "Registar";
                this.tipoAccion = 1;
            }
            else
            {
                axios.get("compras/evaluacion/obtener/" + evaluacion.ep_id).then(res =>
                {
                    if (res.data.status)
                    {
                        tipo = "Actualizar";
                        this.tipoAccion = 2;
                        this.calificacion = {
                            ...res.data.evaluacion
                        };
                        this.calificacion.evaluador = {
                            id: res.data.evaluacion.evaluador,
                            name: res.data.evaluacion.empleado
                        };
                    }
                    else
                    {
                        toastr.error(res.data.mensaje);
                    }
                })
            }
            this.tituloModal = `${tipo} evaluacion de ${evaluacion.razon_social}`;
        },

        /**
         * Descargar el formato de evaluación de proveedor
         */
        DescargrEvaluacion(evaluacion)
        {
            window.open("compras/evaluacion/descargar/" + evaluacion.ep_id, '_blank');
        },

        /**
         * Descarga el reporte del año seleccionado
         */
        DescargarReporte()
        {
            window.open("compras/evaluacion/descargarreporte/" + this.anio);
        }
    },
    mounted()
    {
        this.getData();
    }
}
</script>
