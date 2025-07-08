<template>
<main class="main">
    <div class="">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Artículos
                <button v-show="PermisosCRUD.Create" type="button" @click="abrirModalArticulo()" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus mr-1"></i>Nuevo
                </button>
            </div>
            <div class="card-body">
                <v-server-table ref="myTable" :columns="columns" url="almacen/articulos/obtener?query={}&limit=10&ascending=1&page=1&byColumn=1" :options="options">
                    <template slot="id" slot-scope="props">
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group dropup" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i>&nbsp;Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                    <button v-show="PermisosCRUD.Update" type="button" @click="abrirModalArticulo(false,props.row)" class="dropdown-item">
                                        <i class="fas fa-edit mr-1"></i>Actualizar
                                    </button>

                                    <template v-if="props.row.condicion">
                                        <button v-show="PermisosCRUD.Delete" type="button" class="dropdown-item" @click="DesactivarArticulo(props.row.id)">
                                            <i class="fas fa-times mr-1"></i>Desactivar Artículo
                                        </button>
                                    </template>
                                    <template v-else>
                                        <button show="PermisosCRUD.Delete" type="button" class="dropdown-item" @click="activarArticulo(props.row.id)">
                                            <i class="fas fa-check"></i>Activar Artículo
                                        </button>
                                    </template>

                                    <button v-show="PermisosCRUD.Read" type="button" class="dropdown-item" @click.prevent="mostrarExistencias(props.row)">
                                        <i class="fas fa-boxes mr-1"></i>Existencias</button>

                                    <button v-show="PermisosCRUD.Read" type="button" class="dropdown-item" @click.prevent="mostrarKardex(props.row)">
                                        <i class="fas fa-people-carry mr-1"></i>Movimientos</button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template slot="condicion" slot-scope="props">
                        <template v-if="props.row.condicion">
                            <button type="button" class="btn btn-outline-success">Activo</button>
                        </template>
                        <template v-else>
                            <button type="button" class="btn btn-outline-danger">Desactivado</button>
                        </template>
                    </template>

                    <template slot="calidad" slot-scope="props">
                        <template v-if="props.row.calidad_id != null">
                            <button type="button" class="btn btn-warning" data-toggle="tooltip" data-placement="top" :title="props.row.descal">
                                {{props.row.calidad}}
                            </button>
                        </template>
                    </template>
                </v-server-table>

            </div>
        </div>
        <!-- Fin ejemplo de tabla Listado -->
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
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label" for="nombre">Nombre</label>
                                        <div class="col-md-9">
                                            <input type="text" name="nombre" v-model="nombre" class="form-control" placeholder="Nombre del articulo" autocomplete="off" id="nombre">
                                            <span class="text-danger">{{ errors.first('nombre') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label" for="codigo">Código</label>
                                        <div class="col-md-9">
                                            <input type="text" name="codigo" v-validate="'max:50'" v-model="codigo" class="form-control" placeholder="Codigo" autocomplete="off" id="codigo">
                                            <span class="text-danger">{{ errors.first('codigo') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label" for="descripcion">Descripción</label>
                                        <div class="col-md-9">
                                            <textarea v-validate="'required'" name="descripcion" v-model="descripcion" class="form-control" placeholder="Descripcion"></textarea>
                                            <span class="text-danger">{{ errors.first('descripcion') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label" for="marca">Marca</label>
                                        <div class="col-md-9">
                                            <input type="text" name="marca" v-validate="'max:100'" v-model="marca" class="form-control" placeholder="Marca" autocomplete="off" id="marca">
                                            <span class="text-danger">{{ errors.first('marca') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label" for="unidad">U.M.</label>
                                        <div class="col-md-9">
                                            <v-select v-validate="'required'" name="unidad" label="nombre" v-model="unidad" :options="listUnidadesM"></v-select>
                                            <span class="text-danger">{{ errors.first('unidad') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label" for="categoria_id">Categoria</label>
                                        <div class="col-md-9">
                                            <select class="form-control" id="categoria_id" name="categoria_id" v-model="categoria_id" v-validate="'required|excluded:0'" data-vv-as="Categoria" v-on:change="onChangeCategoria">
                                                <option v-for="item in listaCategorias" :value="item.id" :key="item.id">{{ item.nombre }}</option>
                                            </select>
                                            <span class="text-danger">{{ errors.first('categoria_id') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label" for="grupo_id">Grupo</label>
                                        <div class="col-md-9">
                                            <vue-element-loading :active="isLoadingSelect" />
                                            <select class="form-control" id="grupo_id" name="grupo_id" v-model="grupo_id" v-validate="'required|excluded:0'" data-vv-as="Grupo">
                                                <option v-for="item in listaGrupos" :value="item.id" :key="item.id">{{ item.nombre }}</option>
                                            </select>
                                            <span class="text-danger">{{ errors.first('grupo_id') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label" for="calidad_id">Calidad</label>
                                        <div class="col-md-9">
                                            <select class="form-control" id="calidad_id" name="calidad_id" v-model="calidad_id" v-validate="'required|excluded:0'" data-vv-as="Calidad">
                                                <option v-for="item in listaTipoCalidad" :value="item.id" :key="item.id">{{ item.nombre }}</option>
                                            </select>
                                            <span class="text-danger">{{ errors.first('calidad_id') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" @click="cerrarModal()"><i class="fas fa-window-close"></i>&nbsp;Cerrar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-secondary" @click="RegistrarActualizar(true)"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-secondary" @click="RegistrarActualizar(flase)"><i class="fas fa-save"></i>&nbsp;Actualizar</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->

    <!--Inicio del modal existencias-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modalExistencias}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title">Existencias</h4>
                        <button type="button" class="close" @click="cerrarModalExistencias()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <existencias ref="existencias"></existencias>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" @click="cerrarModalExistencias()"><i class="fas fa-window-close"></i>&nbsp;Cerrar</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->

    <!--Inicio del modal existencias-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modalKardex}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title">Movimientos</h4>
                        <button type="button" class="close" @click="cerrarModalKardex()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <kardex ref="kardex"></kardex>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" @click="cerrarModalKardex()"><i class="fas fa-window-close"></i>&nbsp;Cerrar</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->

</main>
</template>

<script>
import Utilerias from '../../Herramientas/utilerias.js';
var config = require('../../Herramientas/config-vuetables-client').call(this);
const Existencias = r => require.ensure([], () => r(require('../Existencias.vue')), 'rh');
const Kardex = r => require.ensure([], () => r(require('../Kardex.vue')), 'rh');

export default
{
    components:
    {
        'existencias': Existencias,
        'kardex': Kardex
    },
    data()
    {
        return {
            listUnidadesM: [],
            txtProductoSat: '',
            modalSat: 0,
            ver_id: false,
            listaTiposSsat: [],
            listaDivisionSat: [],
            listaGrupoSat: [],
            listaClaseSat: [],
            listaCodigosSat: [],
            tiposat_id: 0,
            gruposat_id: 0,
            divisionsat_id: 0,
            clasesat_id: 0,
            codigosSat_id: 0,
            productoSeleccionado: 0,
            PermisosCRUD:
            {},
            url: '/articulos',
            articulo_id: 0,
            nombre: '',
            codigo: '',
            nombreproveedor: '',
            descripcion: '',
            marca: '',
            unidad:
            {
                id: 0,
                clave: ""
            },
            comentarios: '',
            minimo: 0,
            maximo: 0,
            listaGrupos: [],
            listaCategorias: [],
            listaTipoCalidad: [],
            listaTipoResguardo: [],
            categoria_id: 0,
            grupo_id: 0,
            ficha_tecnica: '',
            fotografia: '',
            calidad_id: 0,
            descal: '',
            trid: 0,
            centro_costo: [],
            centro_costo_id: '',
            ClassL_a: 'btn btn-info',
            BtnL_a: 'Actualizar',
            BtnL_a2: 'Subir Archivo',
            ClassL_b: 'btn btn-info',
            BtnL_b: 'Actualizar',
            BtnL_b2: 'Subir Archivo',
            Metodo: '',
            modal: 0,
            tituloModal: '',
            tipoAccion: 0,
            isLoading: false,
            isLoadingSelect: false,
            modalExistencias: 0,
            modalKardex: 0,
            modalPrecio: 0,
            columns: [
                'id',
                'a_id',
                'nombre',
                'descripcion',
                'marca',
                'unidad',
                'condicion'
            ],
            tableData: [],
            options:
            {
                headings:
                {
                    id: 'Acciones',
                    a_id:"Codigo",
                    nombre: 'Nombre',
                    codigo: 'Codigo',
                    descripcion: 'Descripción',
                    marca: 'Marca',
                    unidad: 'Unidad',
                    grupo: 'Grupo',
                    categoria: 'Categoria',
                    calidad: 'Calidad',
                    condicion: 'Estado',
                    fotografia: 'Fotografia',
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
                        text: 'Desactivado'
                    }]
                },
                texts: config.texts
            },
        }
    },
    computed:
    {},
    methods:
    {
        ObtenerArticulos()
        {
            this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
            let me = this;
            axios.get('/articulo').then(response =>
                {
                    me.tableData = response.data;
                })
                .catch(function (error)
                {
                    console.log(error);
                });
        },

        /**
         * Obtiene los catalogos del articulo
         */
        getLista()
        {
            axios.get('/categoria/getlist').then(response =>
            {
                this.listaCategorias = response.data;
            });
            axios.get('/tipocalidad').then(response =>
            {
                this.listaTipoCalidad = response.data;
            });
            axios.get('/tipoResguardo').then(response =>
            {
                this.listaTipoResguardo = response.data;
            });
            axios.get('/grupo').then(response =>
            {
                this.listaGrupos = response.data;
            });
        },

        /**
         * Registrar o actualizar el articulo
         */
        async RegistrarActualizar(nuevo)
        {
            let isValid = await this.$validator.validate();
            if (!isValid) return;
            this.isLoading = true;
            let formData = new FormData();

            formData.append('metodo', this.Metodo);
            formData.append('nombre', this.nombre == null ? '' : this.nombre);
            formData.append('codigo', this.codigo == null ? '' : this.codigo);
            formData.append('marca', this.marca == null ? '' : this.marca);
            formData.append('unidad', this.unidad.nombre);
            formData.append('um_id', this.unidad.id);
            formData.append('descripcion', this.descripcion);
            formData.append('comentarios', this.comentarios == null ? '' : this.comentarios);
            formData.append('minimo', this.minimo == null ? '0' : this.minimo);
            formData.append('maximo', this.maximo == null ? '0' : this.maximo);
            formData.append('grupo_id', this.grupo_id == null ? '' : this.grupo_id);
            formData.append('categoria_id', this.categoria_id);
            formData.append('calidad_id', this.calidad_id) == null ? '' : this.calidad_id;
            formData.append('ficha_tecnica', this.ficha_tecnica);
            formData.append('fotografia', this.fotografia);
            formData.append('id', this.articulo_id);
            formData.append('trid', this.trid);
            formData.append('centro_costo_id', this.centro_costo_id);

            axios.post(this.url, formData).then(res =>
            {
                this.isLoading = false;
                if (res.data.status)
                {
                    this.cerrarModal();
                    this.ObtenerArticulos();

                    if (nuevo)
                    {
                        toastr.success('Articulo Registrado Correctamente');
                    }
                    else
                    {
                        toastr.success('Articulo Actualizado Correctamente');
                    }
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });

        },

        /**
         * Desactivar el articulo seleccionado
         */
        DesactivarArticulo(id)
        {
            swal(
            {
                title: 'Esta seguro de desactivar este Articulo?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4dbd74',
                cancelButtonColor: '#f86c6b',
                confirmButtonText: 'Aceptar!',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then(result =>
            {
                if (result.value)
                {
                    axios.put('/articulo/desactivar',
                    {
                        'id': id
                    }).then(() =>
                    {
                        toastr.error('El registro ha sido desactivado con éxito.');
                        this.ObtenerArticulos();
                    });

                }
            })
        },

        /**
         * Activar el articulo seleccionado
         */
        activarArticulo(id)
        {
            let x = this;
            swal(
            {
                title: 'Esta seguro de activar este Articulo?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4dbd74',
                cancelButtonColor: '#f86c6b',
                confirmButtonText: 'Aceptar!',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) =>
            {
                if (result.value)
                {
                    axios.put('/articulo/activar',
                    {
                        'id': id
                    }).then(() =>
                    {
                        toastr.success('El registro ha sido activado con éxito.');
                        x.ObtenerArticulos();
                    });
                }
            })
        },

        /**
         * Cerrar el modal de registro de articulo
         */
        cerrarModal()
        {
            this.modal = false;
            this.ClassL_a = 'btn btn-info';
            this.BtnL_a = 'Actualizar';
            this.BtnL_a2 = 'Subir Archivo';
            this.ClassL_b = 'btn btn-info';
            this.BtnL_b = 'Actualizar';
            this.BtnL_b2 = 'Subir Archivo';
            this.nombre = '';
            this.codigo = '';
            // this.nombreproveedor = '';
            this.descripcion = '';
            this.marca = '';
            this.unidad = '';
            this.comentarios = '';
            this.minimo = 0;
            this.maximo = 0;
            this.categoria_id = 0;
            this.grupo_id = 0;
            this.calidad_id = 0;
            this.trid = 0;
            this.centro_costo_id = '';
        },

        /**
         * Abrir modal de articulo
         */
        abrirModalArticulo(nuevo = true, data = [])
        {
            this.getLista();
            this.ObtenerUnidadesM();
            if (nuevo)
            {
                this.modal = true;
                this.tituloModal = 'Registrar articulo';
                this.tipoAccion = 1;
                this.Metodo = 'Nuevo';
            }
            else
            {
                this.modal = 1;
                this.tituloModal = 'Actualizar articulo';
                this.tipoAccion = 2;
                this.articulo_id = data['id'];
                this.nombre = data['nombre'];
                this.codigo = data['codigo'];
                this.descripcion = data['descripcion'];
                this.marca = data['marca'];
                this.unidad = {
                    id: data['um_id'],
                    nombre: data['unidad']
                };
                this.comentarios = data['comentarios'];
                this.minimo = data['minimo'];
                this.maximo = data['maximo'];
                this.categoria_id = data['categoria_id'];
                this.calidad_id = data['calidad_id'];
                this.trid = data['trid'];
                this.centro_costo_id = data['centro_costo_id'] == null ? '' : data['centro_costo_id'];
                this.Metodo = 'Actualizar';
                let x = this;
                axios.get('/categoria/getlist').then(response =>
                {
                    x.listaCategorias = response.data;
                    axios.get('/grupo/getlist/' + x.categoria_id).then(response =>
                    {
                        x.listaGrupos = response.data;
                        this.grupo_id = data['grupo_id'];
                    })
                });
            }
        },

        /**
         * Abrir modal de existencias
         */
        mostrarExistencias(data)
        {
            this.modalExistencias = 1;
            var childExistencias = this.$refs.existencias;
            childExistencias.cargarExistencias(data);
        },

        cerrarModalExistencias()
        {
            this.modalExistencias = 0;
        },
        mostrarKardex(data)
        {
            this.modalKardex = 1;
            var childKardex = this.$refs.kardex;
            childKardex.cargarMovimientos(data);
        },
        cerrarModalKardex()
        {
            this.modalKardex = 0;
        },
        onChangeCategoria()
        {
            let me = this;
            this.isLoadingSelect = true;
            axios.get('/grupo/getlist/' + me.categoria_id).then(response =>
                {
                    me.listaGrupos = response.data;
                    me.isLoadingSelect = false;
                })
                .catch(function (error)
                {
                    console.log(error);
                });
        },
        ObtenerUnidadesM()
        {
            axios.get("/almacen/unidadesm/obtenerdesc").then(res =>
            {
                if (res.status)
                {
                    this.listUnidadesM = res.data.unidades;
                }
                else
                {
                    toastr.error("Error al obtener las unidades", "Error");
                }
            });
        },
    },
    mounted()
    {
        this.ObtenerArticulos();
    },
}
</script>
