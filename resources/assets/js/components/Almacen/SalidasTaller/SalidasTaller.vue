<template>
<main class="main">
    <div class="">
        <!-- Formulario de busqueda -->
        <div class="card" v-show="!detalle">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Salidas a Taller
                <button type="button" @click="abrirModal('salida','registrar')" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-1">
                        <label class="form-control-label">Proyecto</label>
                    </div>
                    <div class="col-5">
                        <v-select :options="listaProyectos" label="nombre_corto" v-model="proyectoId"></v-select>
                    </div>
                    <div class="col-1">
                        <button class="btn btn-dark" @click="ObtenerSalidas()">
                            <i class="fas fa-search mr-1"></i>
                        </button>
                    </div>
                </div>
                <br>
                <vue-element-loading :active="isLoading" />
                <div v-if="tablas.taller">
                    <v-client-table :columns="columns" :data="tableData" :options="options" ref="myTable">
                        <template slot="descargar" slot-scope="props">
                            <button v-show="PermisosCRUD.Download" type="button" class="btn btn-outline-dark" @click="descargarnew(props.row)">
                                <i class="fas fa-file-pdf"></i> <i class="fas fa-download"></i>&nbsp;
                            </button>
                        </template>
                        <template slot="condicion" slot-scope="props">
                            <template v-if="props.row.condicion == 1">
                                <button type="button" class="btn btn-outline-success">Activo</button>
                            </template>
                            <template v-if="props.row.condicion == 0">
                                <button type="button" class="btn btn-outline-danger">Dado de Baja</button>
                            </template>
                        </template>
                        <template slot="id" slot-scope="props">
                            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                <div class="btn-group dropup" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-grip-horizontal"></i>&nbsp;
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        <button v-if="PermisosCRUD.Update" type="button" @click="abrirModal('salida','actualizar',props.row)" class="dropdown-item">
                                            <i class="fas fa-edit"></i>&nbsp; Actualizar Salida
                                        </button>
                                        <button v-if="PermisosCRUD.Read" type="button" @click="cargardetallesalida(props.row)" class="dropdown-item">
                                            <i class="fas fa-eye"></i>&nbsp; Ver detalle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </v-client-table>
                </div>
            </div>
        </div>

        <div v-show="detalle">
            <salidastallerdetalle ref="salidastallerdetalle" @salidastallerdetalle:change="maestro"></salidastallerdetalle>
        </div>
    </div>

    <!--Inicio del modal agregar/actualizar-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <vue-element-loading :active="isLoading" />
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                            <span aria-hidden="true">X</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id">
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="proyecto_id">Proyecto</label>
                            <div class="col-md-9">
                                <v-select :options="listaProyectos" label="nombre_corto" v-model="salida.proyecto_id" v-validate="'required'" data-vv-name="Proyecto"></v-select>
                                <span class="text-danger">{{ errors.first('Proyecto') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="fecha">Fecha</label>
                            <div class="col-md-9">
                                <input type="date" name="fecha" v-model="salida.fecha" class="form-control" placeholder="Fecha de Entrada" autocomplete="off" id="fecha">
                                <span class="text-danger">{{ errors.first('fecha') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="ubicacion">Folio Interno</label>
                            <div class="col-md-9">
                                <input type="text" name="ubicacion" v-validate="'required'" v-model="salida.ubicacion" class="form-control" placeholder="Folio Interno" autocomplete="off" id="ubicacion">
                                <span class="text-danger">{{ errors.first('ubicacion') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="tiposalida_id">Tipo Salida</label>
                            <div class="col-md-9">
                                <select class="form-control" id="tiposalida_id" name="tiposalida_id" v-model="salida.tiposalida_id" v-bind:disabled="desabilitado == 1" v-validate="'excluded:0'" data-vv-as="Salida">
                                    <option v-for="item in listaTipoSalida" :value="item.id" :key="item.id">{{item.nombre}}</option>
                                </select>
                                <span class="text-danger">{{ errors.first('tiposalida_id') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="empleado_id">Nombre solicitante:</label>
                            <div class="col-md-9">
                                <v-select :options="optionsvs" v-model="salida.empleado_id" name="empleado_id" label="nombre" v-validate="'excluded:0'" data-vv-as="Empleado"></v-select>
                                <span class="text-danger">{{ errors.first('empleado_id') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-ouline-dark" @click="cerrarModal()"><i class="fas fa-window-close"></i>&nbsp;Cerrar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-secondary" @click="guardarSalida(1)"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-secondary" @click="guardarSalida(0)"><i class="fas fa-save"></i>&nbsp;Actualizar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</template>

<script>
const SalidasTallerDetalle = r => require.ensure([], () => r(require('./SalidasTallerDetalle.vue')), 'alm');

import Utilerias from '../../Herramientas/utilerias.js';
var config = require('../../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            PermisosCRUD:
            {},
            url: '/salida',
            proyectoId: 0,
            salidav: null,
            detalle: false,
            tituloprincipal: '',
            optionsvs: [],
            desabilitado: 0,
            tituloempleado: '',
            valorfecha: '',
            tablas:
            {
                taller: true,
                sitio: false,
                resguardo: false,
            },
            salida:
            {
                fecha: '',
                folio: '',
                format_salida: '',
                ubicacion: '',
                proyecto_id: 0,
                tiposalida_id: 0,
                empleado_id: '',
                condicion: 0,
            },
            listaProyectos: [],
            listaTipoSalida: [],
            modal: 0,
            tituloModal: '',
            tipoAccion: 0,
            isLoading: false,
            isLoadingg: false,
            columns: ['id', 'fecha',"ubicacion", 'folio', 'nombrec', 'salidan', 'empleado', 'descargar', 'condicion'],
            tableData: [],

            options:
            {
                headings:
                {
                    id: 'Acciones',
                    fecha: 'Fecha',
                    folio: 'Folio',
                    ubicacion: 'Folio Int.',
                    nombrec: ' Proyecto',
                    salidan: 'Tipo de Salida',
                    empleado: 'Solicita',
                    condicion: 'Estado',
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                listColumns:
                {
                    condicion: [
                    {
                        id: 1,
                        text: 'Activo'
                    },
                    {
                        id: 0,
                        text: 'Dado de Baja'
                    }]
                },
                texts: config.texts
            },

        }
    },
    computed:
    {},
    components:
    {
        'salidastallerdetalle': SalidasTallerDetalle,
    },
    methods:
    {
        /**
         * Obtener las salidas del proyecto seleccionado
         */
        ObtenerSalidas()
        {
            if (this.proyectoId == null) return;
            if (this.proyectoId.id == null) return;
            this.isLoading = true;
            axios.get(this.url + '/' + this.proyectoId.id).then(res =>
            {
                this.isLoading = false;
                this.tableData = res.data;
            });
        },

        /**
         * Obtiene todos los empleado activos
         */
        ObtenerEmpleados()
        {
            let me = this;
            axios.get('/generales/empleadoactivos').then(res =>
            {
                this.optionsvs = res.data.empleados;
            });
        },

        /**
         * Obtiene todos los proyectos
         */
        ObtenerProyectos()
        {
            this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
            axios.get('/generales/proyectos/2').then(res =>
            {
                this.listaProyectos = res.data.proyectos;
            }, );
        },

        /**
         * [getTipoSalida Obtiene los tipos de salidas exixtentes]
         * @return {[type]} [description]
         */
        getTipoSalida()
        {
            this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
            let me = this;
            axios.get('/tiposalida').then(response =>
                {
                    me.listaTipoSalida = response.data;
                })
                .catch(function (error)
                {
                    console.log(error);
                });
        },

        fechaActual()
        {
            let me = this;
            axios.get('/FechaActual').then(response =>
                {
                    this.valorfecha = response.data;
                })
                .catch(function (error)
                {
                    console.log(error);
                });
        },

        /**
         * [validarFecha Valida que la fecha seleccionada no se menor a la actual]
         * @param  {Date} dato [description]
         * @return {[type]}      [description]
         */
        validarFecha(dato)
        {
            if (dato < this.valorfecha)
            {
                toastr.error('La Fecha no Puede ser Anterior a la Actual');
                this.salida.fecha = this.valorfecha;
            }
        },

        /**
         * [guardarSalida Guardado y actualizacion de los encabezados de las salidas a taller]
         * @param  {String} nuevo [description]
         * @return {Response}       [description]
         */
        guardarSalida(nuevo)
        {
            this.$validator.validate().then(result =>
            {
                if (result)
                {
                    this.isLoading = true;
                    let me = this;
                    axios(
                    {
                        method: nuevo ? 'POST' : 'PUT',
                        url: nuevo ? me.url : me.url + '/' + this.id,
                        data:
                        {
                            'id': this.salida.id,
                            'fecha': this.salida.fecha,
                            'folio': this.salida.folio,
                            'ubicacion': this.salida.ubicacion,
                            'proyecto_id': this.salida.proyecto_id.id,
                            'tiposalida_id': this.salida.tiposalida_id,
                            'empleado_id': this.salida.empleado_id.id,
                        }
                    }).then(function (response)
                    {
                        me.isLoading = false;
                        if (response.data.status)
                        {
                            me.proyectoId = me.salida.proyecto_id,
                                me.cerrarModal();
                            me.ObtenerSalidas();
                            toastr.success('Correcto', nuevo ? 'Salida registrada correctamente' : 'Salida actualizada correctamente');
                        }
                        else
                        {
                            swal(
                            {
                                type: 'error',
                                html: response.data.errors.join('<br>'),
                            });
                        }
                    }).catch(function (error)
                    {
                        console.log(error);
                    });
                }
            });
        },

        cerrarModal()
        {
            this.modal = 0;
            this.modala = 0;
            this.tituloModal = '';
            Utilerias.resetObject(this.salida);
        },

        /**
         * [cargardetallesalida Se carga el componente salidastallerdetalle]
         * @param  {Array}  [dataSalida=[]] [description]
         * @return {[type]}                 [description]
         */
        cargardetallesalida(dataSalida = [])
        {
            this.detalle = true;
            var ChildDetalleSalidaTaller = this.$refs.salidastallerdetalle;
            ChildDetalleSalidaTaller.cargardetallesalida(dataSalida);

        },

        maestro()
        {
            this.detalle = false;
            // this.cancelar();
        },

        /**
         * [abrirModal Abre los modales de actualizacion y llenado dependiendo de la acción especificada]
         * @param  {String} modelo    [description]
         * @param  {String} accion    [description]
         * @param  {Array}  [data=[]] [description]
         * @return {[type]}           [description]
         */
        abrirModal(modelo, accion, data = [])
        {
            var ts = data['tiposalida_id'];
            switch (modelo)
            {
                case "salida":
                {
                    switch (accion)
                    {
                        case 'registrar':
                        {
                            let me = this;
                            this.modal = 1;
                            this.tituloModal = 'Registrar Salida';
                            Utilerias.resetObject(this.salida);
                            this.tipoAccion = 1;
                            this.desabilitado = 1;
                            this.salida.tiposalida_id = 1;
                            ///me.fechaActual();
                            this.salida.fecha = this.valorfecha;
                            break;
                        }
                        case 'actualizar':
                        {
                            this.modal = 1;
                            this.desabilitado = 1;
                            this.tituloModal = 'Actualizar Salida';
                            this.salida.id = data['id'];
                            this.tipoAccion = 2;
                            this.salida.fecha = data['fecha'];
                            this.salida.folio = data['folio'];
                            this.salida.ubicacion = data['ubicacion'];
                            this.salida.proyecto_id = {
                                id: data['proyecto_id'],
                                nombre_corto: data['nombrec']
                            };
                            this.salida.tiposalida_id = data['tiposalida_id'];

                            this.salida.empleado_id = {
                                nombre: data.empleado,
                                id: data.empleado_id
                            };

                            break;
                        }
                    }
                }
            }
        },

        /**
         * [descargar Descarga el respectivo pdf de la salida]
         * @param  {Array} data [description]
         * @return {[type]}      [description]
         */
        descargarnew(data)
        {
            window.open('descargar-salida-new/' + data.id, '_blank');
            let me = this;
            me.ObtenerSalidas();
        }

    },
    mounted()
    {
        this.ObtenerProyectos();
        this.getTipoSalida();
        this.fechaActual();
        this.ObtenerEmpleados();
    }
}
</script>
