<template>
<main class="main">
    <div class="container-fluid">
        <div class="card">
            <vue-element-loading :active="isLoading" />
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Entradas
                <button type="button" @click="abrirModal('entrada','registrar')" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <div class="card-body">
                <v-server-table :columns="columns" :url="'entrada'" :options="options" ref="myTablePrincipal">
                    <template slot="fecha" slot-scope="props">
                        {{fecha(props.row.fecha)}}
                    </template>
                    <template slot="id" slot-scope="props">
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group dropup" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i>&nbsp;
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <button type="button" @click="cargardetalle(props.row)" class="dropdown-item">
                                        <i class="fas fa-eye"></i>&nbsp; Agregar Partidas
                                    </button>
                                    <button type="button" @click="abrirModal('entrada','actualizar',props.row)" class="dropdown-item">
                                        <i class="icon-pencil"></i>&nbsp;Actualizar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template slot="descarga" slot-scope="props">
                        <button type="button" class="btn btn-outline-dark" @click="descargarnuevo(props.row)">
                            <i class="fas fa-download"></i>&nbsp;<i class="fas fa-file-pdf"></i>
                        </button>
                    </template>
                </v-server-table>
                <br>
                <p class="h5">OC pendientes</p>
                <v-client-table :columns="columns_ocspendientes" :data="list_ocspendientes" :options="options_ocspendientes">
                </v-client-table>
            </div>
        </div>

        <!--Inicio del modal agregar/actualizar Entradas-->
        <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <vue-element-loading :active="isLoading" />
            <div class="modal-dialog modal-dark modal-lg" role="document">
                <div class="modal-content">
                    <div>
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                                <span aria-hidden="true">x</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <template v-if="tipoAccion != 2">
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="orden_compra_id">Proyecto</label>
                                    <div class="col-md-9">
                                        <v-select :options="list_proyectos" v-model="proyecto" label="nombre_corto" @input="obtenetOcs">
                                        </v-select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="orden_compra_id">Orden de compra</label>
                                    <div class="col-md-9">
                                        <v-select id="orden_compra" :options="listaOC" v-validate="'required'" v-model="orden_compra" label="folio" name="folio" data-vv-name="folio">
                                        </v-select>
                                        <span class="text-danger">{{ errors.first('orden_compra_id') }}</span>
                                    </div>
                                </div>
                            </template>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="fecha">Fecha</label>
                                <div class="col-md-3">
                                    <input type="date" v-validate="'required'" name="fecha" v-model="entrada.fecha" class="form-control" placeholder="Fecha de Entrada" autocomplete="off" id="fecha">
                                    <span class="text-danger">{{ errors.first('fecha') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="comentarios">Comentarios</label>
                                <div class="col-md-9">
                                    <input type="text" name="comentarios" v-model="entrada.comentarios" class="form-control" placeholder="Comentarios" autocomplete="off" id="comentarios">
                                    <span class="text-danger">{{ errors.first('comentarios') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-dark" @click="cerrarModal()"><i class="fas fa-window-close"></i>&nbsp;Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-secondary" @click="guardarEntrada(1)"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-secondary" @click="guardarEntrada(0)"><i class="fas fa-save"></i>&nbsp;Actualizar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Fin del modal Entradas-->

        <div v-show="modales == 1">
            <modalpartidas ref="modalpartidas" @atras:modalpartidas="cerrarmodalpartidas()"></modalpartidas>
        </div>

        <div v-show="modales == 2">
            <modalpartidasconsulta ref="modalpartidasconsulta" @atras:modalpartidasconsulta="cerrarmodalpartidasconsulta()"></modalpartidasconsulta>
        </div>

    </div>
</main>
</template>

<script>
import Utilerias from '../../Herramientas/utilerias.js';
var config = require('../../Herramientas/config-vuetables-client').call(this);
const ModalPartidas = r => require.ensure([], () => r(require('./ModalPartidas.vue')), 'alm');
const ModalPartidasConsulta = r => require.ensure([], () => r(require('./ModalPartidasConsulta.vue')), 'alm');

export default
{
    data()
    {
        return {
            modales: 0,
            modal: 0,
            tituloModal: '',
            isLoading: false,
            tipoAccion: 0,
            orden_compra:
            {},
            proyecto:
            {},
            list_proyectos: [],
            columns: ['id', 'foliocompra', 'fecha', 'comentarios', 'descarga'],
            tableData: [],
            options:
            {
                headings:
                {
                    'id': 'Acciones',
                    'fecha': 'Fecha',
                    'foliocompra': 'Folio',
                    'comentarios': 'Comentarios',
                    'formato_entrada': 'Formato de entrada',
                    'condicion': 'Condición',
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts,
                requestAdapter: function (data)
                {
                    var arr = [];
                    arr.push(
                    {
                        'OC.folio': data.query.foliocompra,
                        'fecha': data.query.fecha,
                        'comentarios': data.query.comentarios,
                    });
                    data.query = arr[0];
                    return data;
                },
            },
            entrada:
            {
                id: 0,
                fecha: '',
                comentarios: '',
                condicion: 0,
                disabled: true,
                orden_compra_id: '',
            },
            empleado_responsable: 0,
            disabled: 0,
            listaOC: [],
            listaAlmacenes: [],
            // OCs pendientes
            list_ocspendientes: [],
            columns_ocspendientes: ["oc", "cantidad"],
            options_ocspendientes:
            {
                headings:
                {
                    "oc": "Orden de Compra"
                },
                perPage: 15,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts,
            },

        }
    },
    components:
    {
        'modalpartidas': ModalPartidas,
        'modalpartidasconsulta': ModalPartidasConsulta,
    },
    methods:
    {

        ObtenerPendientes()
        {
            this.isLoading_pendientes = true
            axios.get('almacen/entradas/ocpendientes').then(res =>
            {
                if (res.data.status) this.list_ocspendientes = res.data.ocs;
                else toastr.errors(res.data.error)
            });
        },
        cargardetalle(data)
        {
            var orden_compra = {
                id: data.orden_compra_id,
                folio: data.foliocompra
            };
            this.modales = 1;
            var childpartidas = this.$refs.modalpartidas;
            childpartidas.cargar(data.id, orden_compra, this.listaAlmacenes);
        },

        getListas()
        {
            axios.get('/almacen/ver').then(response =>
            {
                this.listaAlmacenes = response.data;
                this.isLoading = false;
            });
        },

        /**
         *@return
         **/
        abrirModal(modelo, accion, data = [])
        {
            switch (modelo)
            {
                case "entrada":
                {
                    switch (accion)
                    {
                        case 'registrar':
                        {
                            let me = this;
                            Utilerias.resetObject(me.entrada);
                            this.modal = 1;
                            this.tituloModal = 'Registrar entrada';
                            this.tipoAccion = 1;
                            this.disabled = 0;
                            this.obtenerProyectos();
                            break;
                        }
                        case 'actualizar':
                        {
                            let me = this;
                            Utilerias.resetObject(me.entrada);
                            this.modal = 1;
                            this.tituloModal = 'Actualizar entrada';
                            this.entrada.id = data['id'];
                            this.tipoAccion = 2;
                            this.disabled = 1;
                            this.entrada.fecha = data['fecha'];
                            this.entrada.comentarios = data['comentarios'];
                            this.entrada.orden_compra_id = data['orden_compra_id'];
                            break;
                        }
                    }
                }
            }
        },

        /**
         * Obtener las ocs del proyecto seleccionado
         */
        obtenetOcs()
        {
            this.listaOC = [];
            if (this.proyecto == null) return;
            if (this.proyecto.id == null) return;
            axios.get('/almacen/entradas/' + this.proyecto.id).then(res =>
            {
                if (res.data.status)
                    this.listaOC = res.data.ocs;
                else
                    toastr.error(res.data.mensaje);
            })
        },

        /**
         * @return {Response} [se obtiene la]
         */
        fecha(fecha)
        {
            const meses = [
                "Enero", "Febrero", "Marzo",
                "Abril", "Mayo", "Junio", "Julio",
                "Agosto", "Septiembre", "Octubre",
                "Noviembre", "Diciembre"
            ];

            const date = new Date(fecha);
            const dia = date.getDate() + 1;
            const mes = date.getMonth();
            const ano = date.getFullYear();
            return `${dia} de ${meses[mes]} del ${ano}`;
        },

        descargar(data)
        {
            window.open('descargar-entrada/' + data.id, '_blank');
        },

        descargarnuevo(data)
        {
            window.open('descargar-entrada-nuevo-formato/' + data.id, '_blank');
        },

        /**
         * [cerrarModal description]
         */
        cerrarModal()
        {
            this.modal = 0;
            this.tituloModal = '';
        },

        /**
         * [guardarEntrada Metodo que almacen o actualiza una entrada]
         * @param  {Int} nuevo [1 = si y 0 = no]
         * @return {Response}       [status = true]
         */
        guardarEntrada(nuevo)
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
                        url: nuevo ? 'entrada' : 'entrada/' + this.entrada.id,
                        data:
                        {
                            'id': this.entrada.id,
                            'fecha': this.entrada.fecha,
                            'comentarios': this.entrada.comentarios,
                            'orden_compra_id': me.orden_compra.id
                        }
                    }).then(function (response)
                    {
                        me.isLoading = false;
                        if (response.data.status)
                        {
                            me.proyecto = {};
                            me.listaOC = [];
                            if (!nuevo)
                            {
                                me.cerrarModal();
                                toastr.success('Entrada Actualizada Correctamente');
                            }
                            else
                            {
                                toastr.success('Entrada Registrada Correctamente');
                                me.registrarPartidaEntrada(response.data.id_entrada);
                            }
                            me.$refs.myTablePrincipal.refresh();
                        }
                        else
                        {
                            swal(
                            {
                                type: 'error',
                                html: response.data.errors,
                            });
                        }
                    }).catch(function (error)
                    {
                        console.log(error);
                    });
                }
            });
        },

        registrarPartidaEntrada(data)
        {
            this.entrada.id = data;
            this.modales = 1;
            var childpartidas = this.$refs.modalpartidas;
            childpartidas.cargar(data, this.orden_compra, this.listaAlmacenes);
        },

        cerrarmodalpartidas()
        {
            this.modales = 0;
            this.modal = 0;
            this.tituloModal = '';
            this.orden_compra = {};
        },

        cerrarmodalpartidasconsulta()
        {
            this.modales = 0;
            this.modal = 0;
            this.tituloModal = '';
            this.orden_compra = {};
        },

        /**
         * Obtener todos los proyectos activos
         */
        obtenerProyectos()
        {
            axios.get("generales/proyectos/1").then(res =>
            {
                if (res.data.status)
                    this.list_proyectos = res.data.proyectos;
                else
                    toastr.error(res.data.mensaje);
            })
        },

    },

    mounted()
    {
        this.ObtenerPendientes();
        this.getListas();
    }
}
</script>
