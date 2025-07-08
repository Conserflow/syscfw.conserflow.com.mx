<template>
<!-- Ejemplo de tabla Listado -->
<div class="card">
    <div class="card-header">
        <i class="fa fa-align-justify"></i> Salidas
        <button type="button" @click="maestro()" class="btn btn-secondary float-sm-right">
            <i class="fas fa-arrow-left"></i>&nbsp;Atras
        </button>
    </div>
    <div class="card-body">
        <vue-element-loading :active="isLoading" />

        <v-client-table :columns="columnspartida" :data="tableDataPartida" :options="optionspartida" ref="myTable">
            <template slot="id" slot-scope="props">
                <button v-show="PermisosCRUD.Delete" type="button" class="btn btn-outline-dark" @click="eliminar(props.row.id)">
                    <i class="fas fa-trash"></i>&nbsp;
                </button>
            </template>
        </v-client-table>

    </div>
    <!-- Nuevo y editar partidas requisiciones -->
    <div class="card" ref="formLote">
        <vue-element-loading />
        <div class="card-header">
        </div>
        <div class="card-body">
            <div class="row justify-content-center">
                <h4 class="col-sm-12 form-control-label">Registro de salidas a : {{titulosalidas}}</h4>
            </div>
            <hr><br>
            <form>
                <!-- <input type="text" class="form-control" id="stocke_id" name="stocke_id" v-model="partidasalida.stocke_id" placeholder="Stoke"  > -->
                <input type="text" class="form-control" id="salida_id" name="salida_id" v-model="partidasalida.salida_id" placeholder="Salida" hidden>
                <input type="text" class="form-control" id="lote_id" name="lote_id" v-model="partidasalida.lote_id" placeholder="Lote" hidden>
                <input type="text" class="form-control" id="tiposalida_id" name="tiposalida_id" v-model="partidasalida.tiposalida_id" placeholder="tiposalida_id" hidden>
                <input type="text" class="form-control" id="lote_temporal_id" name="lote_temporal_id" v-model="partidasalida.lote_temporal_id" hidden>

                <!-- <input type="text" class="form-control" id="proveedore_id" name="proveedore_id" v-model="" hidden> -->
                <div class="form-group row">
                    <label for="inputArticulo" class="col-md-1 form-control-label">Artículo</label>
                    <div class="col-md-8">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" v-model="partidasalida.articulo" placeholder="Articulo" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-secondary" v-bind:disabled="desabilitarBuscarM" type="button" @click="abrirModalA('articulo','registrar')">Buscar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- {{lotes_TGP}}
          {{partidasalida.lotes_tgp}} -->
                <div class="form-group row">
                    <label for="inputArticulo" class="col-md-1 form-control-label">Lote</label>
                    <div class="col-md-4">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" v-model="partidasalida.lote_nombre_input" placeholder="Lote">
                            <div class="input-group-append">
                                <button class="btn btn-secondary" type="button" @click="buscarLoteNombre()">Buscar</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group mb-3">
                            <select class="form-control" v-model="partidasalida.lotes_tgp" v-validate="'excluded:0'" v-bind:disabled="!desabilitarBuscarM" data-vv-as="Resguardo" @change="seleccionarLote()">
                                <option value="0">---Seleccione---</option>
                                <option v-for="item in lotes_TGP" :value="item" :key="item.id">{{ item.nombre }}</option>
                            </select>
                            <!-- <input type="text" class="form-control"  v-model="partidasalida.articulo" placeholder="Articulo" readonly> -->
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-1 form-control-label" for="cantidad_existente">Cantidad existente</label>
                    <div class="col-md-4">
                        <input type="text" name="cantidad_existente" v-model="partidasalida.cantidad_existente" class="form-control" readonly>
                        <span class="text-danger">{{ errors.first('cantidad_existente') }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-1 form-control-label" for="cantidad">Cantidad</label>
                    <div class="col-md-4">
                        <input type="text" name="cantidad" v-model="partidasalida.cantidad" v-bind:class="'form-control '+clasescantidad" @blur="validacioncantidad" placeholder="Cantidad" autocomplete="off" id="cantidad">
                        <span class="text-danger">{{ errors.first('cantidad') }}</span>
                    </div>
                </div>

            </form>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-secondary" @click="cancelar()"><i class="fas fa-times"></i>&nbsp;Cancelar</button>
            <button type="button" v-if="tipoAccionpr==1" class="btn btn-dark" @click="validacioncantidaduno(); guardarPR();"><i class="fas fa-plus"></i>&nbsp;Agregar</button>
        </div>
    </div>
    <!-- Fin  de Nuevo y editar detalle requisicion correspondiente ala tabla partidas_requisiciones -->

    <!--Inicio del modal agregar/actualizar articulos-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modala}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-xl" role="document">
            <div class="modal-content">
                <div>

                    <!-- <vue-element-loading :active="isLoadingg"/> -->
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModala"></h4>
                        <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <v-server-table ref="tblPartidas" url="almacen/salidas/partidasserver/1?limit=10&page=1" :columns="columns_partidas" :options="options_partidas" @row-click="seleccionarArticulo">
                        </v-server-table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal()"><i class="fas fa-window-close"></i>&nbsp;Cerrar</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</div>
</template>

<style>
.modal-xl {
    max-width: 1100px;
}
</style>

<script>
import Utilerias from '../../Herramientas/utilerias.js';
var config = require('../../Herramientas/config-vuetables-client').call(this);
export default
{
    data()
    {
        return {
            titulosalidas: '',
            validado: 0,
            partidasalida:
            {
                stocke_id: '',
                salida_id: '',
                lote_id: '',
                cantidad: '',
                cantidad_existente: '',
                articulo: '',
                tiposalida_id: '',
                lote_temporal_id: 0,
                lote_nombre_input: '',
                lotes_tgp: 0,
            },
            clasescantidad: '',
            desabilitarBuscarM: false,
            lotes_TGP: [],
            tipoAccionpr: 0,
            modala: 0,
            salidas: null,
            dataTableArticulo: [],
            dataTableArticulodos: [],
            tituloModala: '',
            isLoading: false,
            columnspartida: ['id', 'cantidad', 'anombre', 'adescripcion', 'amarca', 'aunidad', 'alnombre', 'sknombre'],
            columns_partidas: ["a__id", 'la__cantidad', 'p__nombre_corto',"a__nombre", 'a__descripcion', 'a__marca', 'a__unidad'],
            columnsados: ['cantidad', 'lote_nombre', 'nombre_stock', 'anombre', 'acodigo', 'adescripcion', 'amarca', 'aunidad'],
            tableDataPartida: [],
            options_partidas:
            {
                headings:
                {
                    a__id: "#",
                    la__cantidad: "Cantidad",
                    a__nombre: 'Nombre',
                    a__descripcion: 'Descripción',
                    a__marca: 'Marca',
                    a__unidad: 'Unidad',
                    p__nombre_corto: 'Proyecto',
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                sortable: [],
                // filterable: [], // TODO: Order by no funciona??
                filterByColumn: true,
                texts: config.texts
            },
            optionspartida:
            {
                headings:
                {
                    id: "Acciones",
                    adescripcion: "Descripcion",
                    amarca: "Marca",
                    aunidad: "Unidad",
                    alnombre: "Almacén",
                    sknombre: "Stock"

                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                listColumns:
                {

                },
                texts: config.texts
            },
        }
    },
    methods:
    {
        cargardetallesalida(data = [])
        {
            this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
            this.salidas = data;
            let me = this;
            this.isLoading = true;
            this.partidasalida.salida_id = data.id;
            this.partidasalida.tiposalida_id = data.tiposalida_id;
            this.titulosalidas = data.salidan;
            axios.get('/partidasalida/' + data.id + '&' + data.tiposalida_id + '/ver').then(response =>
                {
                    me.tableDataPartida = response.data;
                    this.isLoading = false;
                })
                .catch(function (error)
                {
                    console.error();
                });
        },

        validacioncantidaduno()
        {
            if (this.partidasalida.cantidad == '')
            {
                swal('Error!', 'Complete los campos', 'warning')
                this.validado = 0;
                this.clasescantidad = ' is-invalid';
            }
            else
            {
                this.validado = 1;
            }
        },

        validacioncantidad()
        {
            let salida = parseFloat(this.partidasalida.cantidad);
            let existe = parseFloat(this.partidasalida.cantidad_existente);
            this.validado = 0;
            if (salida > existe)
            {
                swal('Error', 'No se puede pedir una cantidad mayor a la existente', 'warning');
            }
            else if (salida <= 0)
            {
                swal('Error', 'Ingrese una cantidad válida', 'warning');
            }
            else
            {
                this.validado = 1;
            }
        },

        /**
         * selecciona la fila de la tabla articulos y carga los datos o los inputs correspondientes
         * @param  {Array} data [description]
         * @return {[type]}      [description]
         */
        seleccionarArticulo(data)
        {
            let me = this;
            this.partidasalida.lote_id = data.row.la_id;
            this.partidasalida.articulo = data.row.a__descripcion;
            this.partidasalida.cantidad_existente = data.row.la__cantidad;
            this.partidasalida.lote_temporal_id = 0;
            this.modala = 0;
        },

        /**
         * [seleccionarArticulo selecciona la fila de la tabla articulos y carga los datos o los inputs correspondientes]
         * @param  {Array} data [description]
         * @return {[type]}      [description]
         */
        seleccionarArticulodos(data)
        {
            let me = this;
            this.partidasalida.lote_id = data.row.id;
            this.partidasalida.articulo = data.row.adescripcion;
            this.partidasalida.cantidad_existente = data.row.cantidad;
            this.partidasalida.cantidad = data.row.cantidad;
            this.partidasalida.lote_temporal_id = data.row.lt_id;
            this.modala = 0;
        },

        /**
         * Abre el modal donde se eligen los articulos para la salida ya sea del stock general o de un proyecto especifico
         * @param  {String} modelo [description]
         * @param  {String} accion [description]
         * @return {[type]}        [description]
         */
        abrirModalA(modelo, accion)
        {
            switch (modelo)
            {
                case "articulo":
                {
                    switch (accion)
                    {
                        case 'registrar':
                        {
                            let me = this;
                            me.cancelar();
                            this.modala = 1;
                            this.tipoAccionpr = 1;
                            this.tituloModala = 'Registrar artículo a la salida de taller';
                            break;
                        }
                    }
                }
            }
        },

        /**
         * Guarda las partidas de la salidas a taller y verifica que el artículo sea o no suseptible a resguardo
         */
        guardarPR()
        {
            let me = this;
            me.isLoading = true;
            if (this.validado == 1)
            {
                axios.post('/registroresguardo',
                    {
                        salida_id: this.partidasalida.salida_id,
                        tiposalida_id: this.partidasalida.tiposalida_id,
                        lote_id: this.partidasalida.lote_id,
                        cantidad: this.partidasalida.cantidad,
                    })
                    .then(function (response) {})
                    .catch(function (error)
                    {
                        console.log(error);
                    });
                /**/
                if (this.partidasalida.lote_temporal_id != 0)
                {
                    axios.post('/partidasalidaapartados',
                    {
                        lote_temporal_id: this.partidasalida.lote_temporal_id,
                        cantidad: this.partidasalida.cantidad,
                        salida_id: this.partidasalida.salida_id,
                        tiposalida_id: this.partidasalida.tiposalida_id,
                        lote_id: this.partidasalida.lote_id,
                    }).then(function (response)
                    {
                        me.cargardetallesalida(me.salidas);
                        me.cancelar();
                        toastr.success('Correcto', 'Partida agregada correctamente');
                        me.isLoading = false;
                    }).catch(function (error)
                    {
                        console.error(error);
                    });
                }
                else
                {
                    axios.post('/partidasalida',
                        {
                            salida_id: this.partidasalida.salida_id,
                            tiposalida_id: this.partidasalida.tiposalida_id,
                            lote_id: this.partidasalida.lote_id,
                            cantidad: this.partidasalida.cantidad,
                        })
                        .then(res =>
                        {
                            me.cargardetallesalida(me.salidas);
                            me.cancelar();
                            toastr.success('Correcto', 'Partida agregada correctamente');
                            me.isLoading = false;
                            this.$refs.tblPartidas.refresh();
                        });
                }
            }
        },

        buscarLoteNombre()
        {
            let me = this;
            this.desabilitarBuscarM = true;
            this.partidasalida.lotes_tgp = 0;
            axios.post('/buscarlotenombre',
            {
                'lotenombreinput': this.partidasalida.lote_nombre_input,
                'proyecto_id': me.salidas.proyecto_id,
            }).then(response =>
            {
                this.lotes_TGP = response.data;

            }).catch(error =>
            {
                console.log(error);
            });
        },

        seleccionarLote()
        {
            if (this.partidasalida.lotes_tgp.nombre != 'Apartado por requisicion')
            {
                axios.get('/obtenerarticulog/' + this.partidasalida.lotes_tgp.id).then(response =>
                {
                    this.tipoAccionpr = 1;
                    this.partidasalida.lote_id = response.data.id;
                    this.partidasalida.articulo = response.data.adescripcion;
                    this.partidasalida.cantidad_existente = response.data.cantidad;
                    this.partidasalida.lote_temporal_id = 0;
                }).catch(error =>
                {
                    console.log(error);
                });

            }

            else
            {
                this.tipoAccionpr = 1;
                axios.get('/obtenerarticuloa/' + this.partidasalida.lotes_tgp.id).then(response =>
                {
                    this.partidasalida.lote_id = response.data.id;
                    this.partidasalida.articulo = response.data.adescripcion;
                    this.partidasalida.cantidad_existente = response.data.cantidad;
                    this.partidasalida.lote_temporal_id = response.data.lt_id;
                }).catch(error =>
                {
                    console.log(error);
                });

            }
        },

        maestro()
        {
            this.$emit('salidastallerdetalle:change');
            this.cancelar();
        },

        cancelar()
        {
            this.partidasalida.cantidad = '';
            this.tipoAccionpr = 0;
            this.partidasalida.articulo = '';
            this.partidasalida.cantidad_existente = '';
            this.partidasalida.lote_nombre_input = '';
            this.partidasalida.lotes_tgp = 0;
            this.desabilitarBuscarM = false;
        },

        cerrarModal()
        {
            this.modala = 0;
            this.tituloModal = '';
        },

        eliminar(id)
        {
            let me = this;
            axios.get('eliminar/partida/salida/' + id).then(response =>
            {
                me.cargardetallesalida(me.salidas);
                me.cancelar();
                toastr.success('Correcto', 'Partida eliminada correctamente');
                // me.isLoading = false;
            }).catch(error =>
            {
                console.error(error);
            });
        }
    },
    mounted()
    {

    }
}
</script>
