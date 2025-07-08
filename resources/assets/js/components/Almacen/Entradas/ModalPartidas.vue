<template>
<div>
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Agregar partidas de OC {{orden_compra.folio}}</h4>
                    <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <vue-element-loading :active="isLoading" />
                        <v-server-table ref="myTable" :url="'entradas/partidas/oc/'+orden_compra.id" :columns="columns" :options="options">
                            <template slot="cantidad_entrada" slot-scope="props">
                                <input :id="'cantidad_'+props.row.id" type="text" v-validate="'decimal:2'" width="140px" class="form-control" data-vv-name="cantidad">
                                <span class="text-danger">{{errors.first('cantidad')}}</span>
                            </template>
                            <template slot="material_rechazado" slot-scope="props">
                                <input :id="'cantidad_rechazo_'+props.row.id" type="text" v-validate="'decimal:2'" width="140px" class="form-control" data-vv-name="cantidad_rechazo">
                                <span class="text-danger">{{errors.first('cantidad')}}</span>
                            </template>
                            <template slot="almacen" slot-scope="props">
                                <button type="button" class="btn btn-primary btn-sm" @click="guardarCantidad(props.row)">
                                    <i class="fas fa-check mr-1"></i>Guardar</button>
                            </template>
                        </v-server-table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" @click="cerrarModal()"><i class="fas fa-window-close"></i>&nbsp;Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
var config = require('../../Herramientas/config-vuetables-client').call(this);
const ModalAlmacen = r => require.ensure([], () => r(require('./ModalAlmacen.vue')), 'alm');

export default
{
    data()
    {
        return {
            nuevo: 1,
            modal: 0,
            orden_compra: '',
            columns: ['descripcion', 'unidad', 'cantidad','cantidad_rechazo', 'cantidad_entrada','material_rechazado', 'almacen'],
            options:
            {
                headings:
                {
                    'descripcion': 'Descripción artículo',
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                sortable: ['descripcion', 'unidad', 'cantidad'],
                filterable: ['descripcion', 'unidad', 'cantidad'],
                filterByColumn: true,
                texts: config.texts
            },
            almacenes: [],
            entrada_id: '',
            isLoading: false,
            almacen:
            {
                id: 8,
                stand_id: '',
                nivel_id: '',
            },
            parentrada:
            {
                id: '',
            }
        }
    },
    components:
    {
        'modalalmacenes': ModalAlmacen,
    },
    methods:
    {
        /**
         *
         */
        cargar(data, oc, almacenes)
        {
            this.modal = 1;
            this.entrada_id = data;
            this.orden_compra = oc;
            this.almacenes = almacenes;
        },

        guardarCantidad(row)
        {
            let id = row.id;
            let cantidad = $("#cantidad_" + row.id)[0].value;
            let cantidad_rechazo =$("#cantidad_rechazo_" + row.id)[0].value;
            if (cantidad == null)
            {
                alert("Cantidad no ingresada");
                return;
            }

            if (cantidad_rechazo == null) {
                cantidad_rechazo = 0 ;
            }


            let articulo_id = row.articulo_id;
            let n_ingresada = parseFloat(cantidad); // cantidad ingresada
            let n_registrada = parseFloat(row.cantidad); // cantidad registrada
            if (n_ingresada > n_registrada)
            {
                toastr.warning('Se esta registrando una cantidad mayor a la compra');
                return;
            }
            if (n_ingresada <= 0)
            {
                toastr.warning("Ingrese una cantidad válida");
                return;
            }
            let me = this;

            this.isLoading = true;
            axios(
            {
                method: this.nuevo ? 'POST' : 'PUT',
                url: this.nuevo ? '/partidaentrada' : `partidaentrada/update/factura/${this.parentrada.id}`,
                data:
                {
                    'entrada_id': this.entrada_id,
                    'req_com_id': id,
                    'cantidad': cantidad,
                    'id': this.parentrada.id,
                    'articulo_id': articulo_id,
                    'validacion_calidad': 0,
                    'almacene_id': 8, // Taller,
                    'cantidad_rechazo': cantidad_rechazo,
                }
            }).then(response =>
            {
                $("#cantidad_" + row.id).val(0);
                $("#cantidad_rechazo_" + row.id).val(0);
                this.isLoading = false;

                if (response.data.status)
                {
                    toastr.success("Guardado Correctamente");
                    me.$refs.myTable.refresh();
                }
                else
                {
                    swal(
                        'Error!',
                        '!',
                        'warning'
                    )
                }
            }).catch(function (error)
            {
                console.log(error);
            });
        },

        cerrarModal()
        {
            this.modales = 1;
            this.$emit('atras:modalpartidas');
        },
    }
}
</script>
