<template>
<main class="main">
    <div class="container-fluid">

        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Equipos de Calibración
                <button type="button" class="btn btn-dark float-sm-right" @click="AbrirModal(true)">
                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <div class="card-body">
                <vue-element-loading :active="isEquipos_Loading" />
                <v-client-table :data="list_equipos" :columns="columns_equipos" :options="options_equipos">
                    <template slot="equipos.frecuencia" slot-scope="props">
                        <template v-if="props.row.equipos.frecuencia == 1">
                            <span class="text-success">Anual</span>
                        </template>
                        <template v-if="props.row.equipos.frecuencia == 3">
                            <span class="text-warning">Semestral</span>
                        </template>
                        <template v-if="props.row.equipos.frecuencia == 2">
                            <span class="text-danger">Sin Info.</span>
                        </template>
                    </template>

                    <template slot="equipos.id" slot-scope="props">
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group dropup" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i> Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <button v-if="props.row.equipos.condicion==1" type="button" @click="AbrirModal(false,props.row)" class="dropdown-item">
                                        <i class="fas fa-edit"></i>&nbsp; Actualizar
                                    </button>
                                    <button type="button" class="dropdown-item" @click="VerHistorial(props.row.equipos)">
                                        <i class="fas fa-eye"></i> Ver Resguardos
                                    </button>
                                    <button v-if="props.row.equipos.condicion==1" type="button" class="dropdown-item" @click="Desactivar(props.row.equipos.id)">
                                        <i class="fas fa-times"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template slot="equipos.condicion" slot-scope="props">
                        <template>
                            <button v-if="props.row.equipos.condicion==1" class="btn btn-outline-success">Activo</button>
                            <button v-if="props.row.equipos.condicion==0" class="btn btn-outline-danger">Inactivo</button>
                            <button v-if="props.row.equipos.condicion==2" class="btn btn-outline-warning">En resguardo</button>
                        </template>
                    </template>

                </v-client-table>
            </div>
        </div>

        <!--Inicio del modal agregar almacen-->
        <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-dark modal-lg" role="document">

                <div class="modal-content">
                    <div>
                        <vue-element-loading :active="isGuardar_Loading" />
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                                <span aria-hidden="true">x</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-row">
                                <div class="col-md-10 mb-3">
                                    <label>Descripción</label>
                                    <input type="text" class="form-control" v-validate="'required'" data-vv-name="Descripción" v-model="equipos.descripcion">
                                    <span class="text-danger">{{errors.first('Descripción')}}</span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4 mb-3">
                                    <label>Tipo</label>
                                    <select class="form-control" v-validate="'required'" data-vv-name="Tipo" v-model="equipos.tipo">
                                        <option value="1">Máquina de Medición</option>
                                        <option value="2">Herramientas Mecánicas</option>
                                    </select>
                                    <span class="text-danger">{{errors.first('Tipo')}}</span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4 mb-3">
                                    <label>Marca</label>
                                    <input type="text" class="form-control" v-validate="'required'" data-vv-name="Marca" v-model="equipos.marca">
                                    <span class="text-danger">{{errors.first('Marca')}}</span>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Modelo</label>
                                    <input type="text" class="form-control" v-validate="'required'" data-vv-name="Modelo" v-model="equipos.modelo">
                                    <span class="text-danger">{{errors.first('Modelo')}}</span>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>No. Serie</label>
                                    <input type="text" class="form-control" v-validate="'required'" data-vv-name="No. serie" v-model="equipos.numero_serie">
                                    <span class="text-danger">{{errors.first('No. serie')}}</span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-10 mb-3">
                                    <label>Rango Medición</label>
                                    <input type="text" class="form-control" v-validate="'required'" data-vv-name="Rango Medición" v-model="equipos.rango_medicion">
                                    <span class="text-danger">{{errors.first('Rango Medición')}}</span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4 mb-3">
                                    <label>Frecuencia</label>
                                    <select class="form-control" v-model="equipos.frecuencia" v-validate="'required'" data-vv-name="Frecuencia" @change="vaciar()">
                                        <option value="1">Anual</option>
                                        <option value="2">Sin Info.</option>
                                        <option value="3">Semenstral</option>
                                        <option value="4">Bimestral</option>
                                        <option value="5">Mensual</option>
                                    </select>
                                    <span class="text-danger">{{errors.first('Frecuencia')}}</span>
                                </div>
                                <template v-if="equipos.frecuencia != '' && equipos.frecuencia != '2'">
                                    <div class="col-md-4 mb-3">
                                        <label>Fecha Servicio</label>
                                        <input type="date" class="form-control" v-validate="'required'" data-vv-name="Fecha Servicio" v-model="equipos.fecha_servicio" @change="cambiarFecha()">
                                        <span class="text-danger">{{errors.first('Fecha Servicio')}}</span>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label>Proxima fecha</label>
                                        <input type="date" class="form-control" v-validate="'required'" data-vv-name="Proxima Fecha" v-model="equipos.proxima_fecha">
                                        <span class="text-danger">{{errors.first('Proxima Fecha')}}</span>
                                    </div>
                                </template>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label>Resguardo</label>
                                    <input type="text" class="form-control" v-model="equipos.resguardo">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Observaciones</label>
                                    <input type="text" class="form-control" v-model="equipos.observaciones">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" v-if="tipoAccion == 1" class="btn btn-secondary" @click="Guardar(1)"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                            <button type="button" v-if="tipoAccion == 2" class="btn btn-secondary" @click="Guardar(0)"><i class="fas fa-save"></i>&nbsp;Actualizar</button>
                            <button type="button" class="btn btn-outline-dark" @click="cerrarModal()"><i class="fas fa-window-close"></i>&nbsp;Cerrar</button>
                        </div>
                    </div>
                </div>

                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Fin del modal agregar almacen-->

        <!--Inicio del modal agregar almacen-->
        <div class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_historial}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-dark modal-lg" role="document">

                <div class="modal-content">
                    <div>
                        <div class="modal-header">
                            <h4 class="modal-title">Historial de resguardos</h4>
                            <button type="button" class="close" @click="CerrarModalHistorial()" aria-label="Close">
                                <span aria-hidden="true">x</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <vue-element-loading :active="isHistorial_Loading" />
                            <table class="table table-sm table-responsive">
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Solicita</th>
                                    <th>Entrega</th>
                                    <th># Solicitado</th>
                                    <th># Retornado</th>
                                </tr>
                                <tr :key="i" v-for="(r,i) in list_historial">
                                    <td>{{i+1}}</td>
                                    <td>{{r.fecha_solicitud}}</td>
                                    <td>{{r.empleado_solicita}}</td>
                                    <td>{{r.empleado_entrega}}</td>
                                    <td>{{r.solicitado}}</td>
                                    <td>{{r.retornado}}</td>
                                </tr>
                            </table>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalHistorial()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Fin del modal agregar almacen-->

    </div>
</main>
</template>

<script>
import Utilerias from '../Herramientas/utilerias.js'
var config = require('../Herramientas/config-vuetables-client').call(this)

export default
{
    data()
    {
        return {
            list_equipos: [],
            isGuardar_Loading: false,
            isEquipos_Loading: false,
            columns_equipos: [
                'equipos.id',
                'equipos.descripcion', 'equipos.marca',
                'equipos.modelo',
                'equipos.rango_medicion',
                'equipos.numero_serie',
                'equipos.frecuencia',
                'equipos.condicion'
            ],
            options_equipos:
            {
                headings:
                {
                    'equipos.id': 'Acciones',
                    'equipos.descripcion': 'Descripcion',
                    'equipos.marca': 'Marca',
                    'equipos.modelo': 'Modelo',
                    'equipos.rango_medicion': 'Rango Medición',
                    'equipos.numero_serie': '# Serie',
                    'equipos.frecuencia': 'Frecuencia',
                    'equipos.condicion': "Estado"
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            },
            isLoading: false,
            modal: 0,
            tituloModal: '',
            tipoAccion: 0,
            equipos:
            {
                id: 0,
                frecuencia: '',
                descripcion: '',
                marca: '',
                modelo: '',
                rango_medicion: '',
                fecha_servicio: '',
                proxima_fecha: '',
                resguardo: '',
                observaciones: '',
                numero_serie: '',
                servicio_id: 0,
                tipo: 1,
            },
            // historial
            list_historial: [],
            isHistorial_Loading: false,
            ver_modal_historial: false,
        }
    },
    methods:
    {
        /**
         * Obtener todos los equipos de calibración
         */
        ObtenerEquipos()
        {
            this.isEquipos_Loading = true;
            axios.get("calidad/calib/obtener").then(res =>
            {
                this.isEquipos_Loading = false;
                if (res.data.status)
                {
                    this.list_equipos = res.data.equipos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        AbrirModal(nuevo, data = {})
        {

            this.modal = 1;
            if (nuevo)
            {
                this.tituloModal = 'Registrar equipo de calibración';
                this.tipoAccion = 1;

            }
            else
            {
                this.tituloModal = 'Actualizar equipo de calibración';
                this.tipoAccion = 2;
                this.equipos = {
                    ...data.equipos,
                };
                if (data.equipos.servicio != null)
                    this.equipos.servicio_id = data.servicios.servicios.id;

            }

        },

        cerrarModal()
        {
            this.equipos = {
                tipo: 1
            };
            this.modal = 0;
        },

        /**
         * Registra o actualiza el equipo de calibración
         */
        Guardar(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;

                this.isGuardar_Loading = true;
                axios(
                {
                    method: nuevo ? 'POST' : 'PUT',
                    url: nuevo ? 'calidad/calib/guardar' : 'calidad/calib/actualizar',
                    data:
                    {
                        id: this.equipos.id,
                        descripcion: this.equipos.descripcion,
                        marca: this.equipos.marca,
                        modelo: this.equipos.modelo,
                        tipo: this.equipos.tipo,
                        numero_serie: this.equipos.numero_serie,
                        rango_medicion: this.equipos.rango_medicion,
                        frecuencia: this.equipos.frecuencia,
                        resguardo: this.equipos.resguardo,
                        observaciones: this.equipos.observaciones,
                        fecha_servicio: this.equipos.fecha_servicio,
                        proxima_fecha: this.equipos.proxima_fecha,
                        servicio_id: this.equipos.servicio_id,
                    }
                }).then(res =>
                {
                    this.isGuardar_Loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.ObtenerEquipos();
                        this.cerrarModal();
                    }
                    else
                    {
                        toastr.error(res.data.mensaje);
                    }
                });

            });
        },

        /**
         * Genera la siguiente fecha
         */
        cambiarFecha()
        {
            var TuFecha = new Date(this.equipos.fecha_servicio);

            if (this.equipos.frecuencia == 1) //anual
            {
                var dias = parseInt(365);
            }
            else if (this.equipos.frecuencia == 3) //Semestral
            {
                var dias = parseInt(182);
            }
            else if (this.equipos.frecuencia == 4) //Bimestral
            {
                var dias = parseInt(60);
            }
            else if (this.equipos.frecuencia == 5) //Mensual
            {
                var dias = parseInt(30);
            }

            //nueva fecha sumada
            TuFecha.setDate(TuFecha.getDate() + dias);
            //formato de salida para la fecha
            this.equipos.proxima_fecha = TuFecha.getFullYear() + '-' +
                this.str_pad((TuFecha.getMonth() + 1), 2, '0', 'STR_PAD_LEFT') + '-' +
                this.str_pad((TuFecha.getDate() + 1), 2, '0', 'STR_PAD_LEFT');

        },

        str_pad(str, pad_length, pad_string, pad_type)
        {
            console.log(String(str).length);
            var len = pad_length - String(str).length;

            if (len < 0) return str;

            var pad = new Array(len + 1).join(pad_string);

            if (pad_type == "STR_PAD_LEFT") return pad + str;

            return str + pad;

        },
        /**
         * Limpiar los campos de fecha
         */
        vaciar()
        {
            this.equipos.fecha_servicio = '';
            this.equipos.proxima_fecha = '';
        },

        /**
         * Mostrar el historial de los resguardos
         */
        VerHistorial(equipo)
        {
            console.error(equipo);
            this.ver_modal_historial = true;
            this.isHistorial_Loading = true;
            axios.get("calidad/calib/resguardos/" + equipo.articulo_id).then(res =>
            {
                this.isHistorial_Loading = false;
                if (res.data.status)
                {
                    this.list_historial = res.data.resguardos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })

        },

        CerrarModalHistorial()
        {
            this.ver_modal_historial = false;
            this.list_historial = [];
        },

        /**
         * Desactiva el equipo
         */
        Desactivar(id)
        {
            axios.post("calidad/calib/eliminar/",
            {
                id
            }).then(res =>
            {
                if (res.data.status)
                {
                    toastr.success("Eliminado correctamente");
                    this.ObtenerEquipos();
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        }

    },
    mounted()
    {
        this.ObtenerEquipos();
    }
}
</script>
