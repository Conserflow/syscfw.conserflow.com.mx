<template>
<div class="card border-dark">
    <div class="card-header bg-dark text-white">
        <i class="fa fa-align-justify"> </i> Requisiciones por recibir:
        <button v-if="ver_partidas" type="button" @click="regresar()" class="btn btn-secondary float-sm-right">
            <i class="icon-arrow-left"></i>Regresar
        </button>
    </div>

    <vue-element-loading :active="isLoading" />
    <div class="card-body">
        <v-client-table :data="list_requis" :columns="columns_requis" :options="options" v-show="!ver_partidas">
            <template slot="id" slot-scope="props">
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                    <div class="btn-group dropup" role="group">
                        <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-grip-horizontal"></i> Acciones
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <button type="button" @click="cargardetalle(props.row.id)" class="dropdown-item">
                                <i class="fas fa-eye"></i> Ver partidas
                            </button>
                            <button class="dropdown-item" @click="descargar(props.row)">
                                <i class="fas fa-file-pdf"></i>Descargar Requisición
                            </button>
                        </div>
                    </div>
                </div>
            </template>
            <template slot="condicion" slot-scope="props">
                <button class="btn btn-sm btn-success" @click="autorizar(4, props.row.id)"><i class="fas fa-check"></i> Sí</button>
                <button class="btn btn-sm btn-danger" @click="autorizar(0, props.row.id)"><i class="fas fa-close"></i> No</button>
            </template>
        </v-client-table>

        <div v-show="ver_partidas">
            <v-client-table :data="list_partidas" :options="options_partidas" :columns="columns_partidas">
                <template slot="req.descripciona" slot-scope="props">
                    <textarea rows="6" cols="40" :value="props.row.correccion != null ? props.row.correccion.comentario :
                    props.row.req.descripciona + ' ' + (props.row.req.comentario == null ? '' :
                    props.row.req.comentario)" @keyup.enter="guardarcorrecion($event, props.row.req)">
                    </textarea>
                </template>
            </v-client-table>

        </div>
    </div>
</div>
</template>

<script>
var config = require('../../Herramientas/config-vuetables-client').call(this);
export default
{
    data()
    {
        return {
            isLoading: false,
            nuevo: null,
            solicitud: [],
            ver_partidas: false,
            detalles_solicitudes: null,
            tipo_cambio: 0,
            moneda: 0,
            list_requis: [],
            columns_requis: [
                'id',
                'folio',
                'nombrep',
                'fecha_solicitud',
                'nombre_solicita',
                'nombre_autoriza',
                'condicion'
            ],
            options:
            {
                headings:
                {
                    'id': 'Acciones',
                    'nombrep': 'Proyecto',
                    'nombre_solicita': 'Empleado que solicita',
                    'nombre_autoriza': 'Empleado que autoriza',
                    'condicion': 'Recibir',
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts,
            },
            list_partidas: [],
            columns_partidas: [
                'req.descripciona',
                'req.cantidad_compra',
                'req.um',
                'req.fecha_requerido'
            ],
            options_partidas:
            {
                headings:
                {
                    'req.descripciona': 'Articulo/Servicio',
                    'req.um': 'Unidad',
                    'req.fecha_requerido': 'Fecha requerida',
                    'req.cantidad_compra': 'Cantidad a comprar',
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts,
            }
        }
    },
    methods:
    {

        /**
         * Obtiene las requisiciones por autorizar de compras
         */
        getData()
        {
            this.isLoading = true;
            axios.get('/requisicionesrecibir').then(res =>
            {
                this.isLoading = false;
                if (res.data.status)
                {
                    this.list_requis = res.data.requisiciones;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Mostrar las partidas de la requi seleccionada
         */
        cargardetalle(id)
        {
            this.isLoading = true;
            this.ver_partidas = true;

            axios.get('/requisicioncompserart/' + id).then(res =>
            {
                this.detalles_solicitudes = res.data;
                this.list_partidas = res.data;
                this.isLoading = false;
            });
        },

        /**
         * Autoriza o rechaza la requi seleccionada
         */
        autorizar(estado, id)
        {
            this.isLoading = true;
            if (estado == 4)
            {
                swal(
                {
                    title: '¿Autorizar requisición?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4dbd74',
                    cancelButtonColor: '#f86c6b',
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then(result =>
                {
                    this.isLoading = false;
                    if (result.value)
                    {
                        axios.post('/autorizarequisicionproyectos',
                        {
                            id: id,
                            estado: estado,
                        }).then(response =>
                        {
                            toastr.success('Correcto', 'Requisición aprobada correctamente');
                            this.getData();
                        });
                    }
                });
            }
            else
            {
                let cadena = ['Almacén', 'Supervisor'];
                let cadenaid = [10, 11];
                swal(
                {
                    title: 'Direccionar a...',
                    input: 'select',
                    inputOptions: cadena,
                    inputPlaceholder: 'Seleccionar Estado',
                    confirmButtonText: 'Continuar <i class="fa fa-arrow-right></i>',
                    showCancelButton: true,
                    customClass: 'form-check form-check-inline',
                    inputValidator: (result) =>
                    {
                        return !result && 'Se Requiere Elegir un Elemento'
                    }
                }).then(result =>
                {
                    this.isLoading = false;
                    if (result.value)
                    {
                        axios.post('/autorizarequisicionproyectos',
                        {
                            id: id,
                            estado: cadenaid[result.value],
                        }).then(response =>
                        {
                            if (response == 4)
                            {
                                toastr.success('Requisición recibida correctamente');
                            }
                            else
                            {
                                toastr.warning('Requisición rechazada');
                            }
                            this.getData();
                        });
                    }
                });
            }
        },

        /**
         * Descargar la requisición seleccionada
         */
        descargar(data)
        {
            window.open('descargar-requisicionew/' + data.id, '_blank');
        },

        /**
         * Actualiza la descripción del artículo/servicio actual
         */
        guardarcorrecion(e, data)
        {
            axios.post('agregar/correciones/partidas',
            {
                requisicion_id: data.rid,
                pda: data.pda,
                articulo_servicio: (data.articulo_id != null ? 1 : 0),
                articulo_servicio_id: (data.articulo_id != null ? data.articulo_id : data.servicio_id),
                comentario: e.target.value,
            }).then(res =>
            {
                toastr.success('Guardado');
            });
        },

        /**
         * Regresar a la card de requisiciones
         */
        regresar()
        {
            this.ver_partidas = false;
            this.detalles_solicitudes = null;
        }
    },
    mounted()
    {
        this.getData();
    }
}
</script>
