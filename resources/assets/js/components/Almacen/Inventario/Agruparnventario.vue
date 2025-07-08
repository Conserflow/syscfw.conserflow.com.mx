<template>
<div class=''>
    <!-- Card Inicio-->
    <div class=''>

        <!-- GRupos -->
        <div class="container" v-show="tipo_card==1">

            <!-- Inicio card-->
            <div class='container'>
                <button type='button' class='btn btn-dark float-sm-right' @click='AbrirModalArticulo(true)'>
                    <i class='fas fa-plus'>&nbsp;</i>Nuevo
                </button>
                <br>
            </div>

            <div class=''>
                <h5>Grupos</h5>
                <!-- Tabla de Articulo-->
                <v-client-table :columns='columns_articulo' :data='list_articulo' :options='options_articulo' ref='tbl_articulo'>
                    <template slot='id' slot-scope='props'>
                        <div class='btn-group' role='group'>
                            <button id='btn_id' type='button' class='btn btn-outline-dark dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                <i class='fas fa-grip-horizontal'></i>&nbsp; Acciones
                            </button>
                            <div class='dropdown-menu'>
                                <template>
                                    <button type='button' @click='AbrirModalArticulo(false, props.row)' class='dropdown-item'>
                                        <i class='fas fa-edit'></i>&nbsp;Actualizar
                                    </button>

                                    <button type='button' @click='VerArticulos(props.row)' class='dropdown-item'>
                                        <i class='fas fa-eye'></i>&nbsp;Ver Artículos
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </v-client-table>
                <!--Card body -->
            </div>
        </div>

        <!-- Articulos del grupo -->
        <div class="container" v-show="tipo_card==2">
            <!-- Inicio card-->
            <div class='container'>
                <h5>Artículos del grupo: <span class="font-weight-bold">{{nombre_grupo}}</span></h5>
                <button type='button' class='btn btn-dark float-sm-right' @click='AbrirModalBuscarArticulos()'>
                    <i class='fas fa-plus'>&nbsp;</i>Agregar
                </button>
                <button type='button' class='btn btn-dark float-sm-right' @click='Regresar'>
                    <i class='fas fa-arrow-left'>&nbsp;</i>Regresar
                </button>
                <br>
                <br>
            </div>

            <div class=''>
                <!-- Tabla de Articulo-->
                <v-client-table :columns='columns_articulos_grupo' :data='list_articulos_grupo' :options='options_articulos_grupo' ref='tbl_articulo'>
                </v-client-table>
                <!--Card body -->
            </div>
        </div>
    </div>

    <!-- Modal Articulo -->
    <div class='modal fade' tabindex='-1' :class="{'mostrar' : ver_modal_articulo}" role='dialog' aria-labelledby='myModalLabel' style='display: none;' aria-hidden='true'>
        <div class='modal-dialog modal-dark modal-lg' role='document'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title' v-text='titulo_modal_articulo'></h4>
                    <button type='button' class='close' @click='CerrarModalArticulo()' aria-label='Close'>
                        <span aria-hidden='true'>×</span>
                    </button>
                </div>
                <div class='modal-body'>
                    <div class='form-group row'>
                        <label class='col-md-2 form-control-label'>Nombre</label>
                        <div class='col-md-9'>
                            <input type='text' v-validate='"required"' class='form-control' v-model='articulo_modal.nombre' data-vv-name='Nombre'>
                            <span class="text-danger">{{errors.first('Nombre')}}</span>
                        </div>
                    </div>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-outline-dark' @click='CerrarModalArticulo()'><i class='fas fa-times'></i>&nbsp;Cerrar</button>
                    <button type='button' v-if='tipoAccion_modal_articulo== 1' class='btn btn-secondary' @click='Guardar(1)'><i class='fas fa-save'></i>&nbsp;Guardar</button>
                    <button type='button' v-if='tipoAccion_modal_articulo==2' class='btn btn-secondary' @click='Guardar(0)'><i class='fas fa-save'></i>&nbsp;Actualizar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <!-- Modal Articulo Pendientes -->
    <div class='modal fade' tabindex='-1' :class="{'mostrar' : ver_modal_articulos_pendientes}" role='dialog' aria-labelledby='myModalLabel' style='display: none;' aria-hidden='true'>
        <div class='modal-dialog modal-dark modal-lg' role='document'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title'>Artículos Pendientes por Agrupar</h4>
                    <button type='button' class='close' @click='CerrarModalArticulosPendientes()' aria-label='Close'>
                        <span aria-hidden='true'>×</span>
                    </button>
                </div>
                <div class='modal-body'>
                    <v-client-table :columns='columns_articulos_pendientes' :data='list_articulos_pendientes' :options='options_articulos_pendientes' ref='tbl_articulo'>
                        <template slot='check' slot-scope='props'>
                            <input v-model="list_pendintes_temp[props.row.n]" @change="Checked(props.row,list_pendintes_temp[props.row.n])" type="checkbox" :id="'chec_'+props.row.ai_id">
                        </template>
                    </v-client-table>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-outline-dark' @click='CerrarModalArticulosPendientes'><i class='fas fa-times'></i>&nbsp;Cerrar</button>
                    <button type='button' class='btn btn-secondary' @click='GuardarArticulosPendientes'><i class='fas fa-save'></i>&nbsp;Guardar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</div> <!-- Main -->
</template>

<script>
/* CAMBIAR UBICACIÓN  */
var config = require('../../Herramientas/config-vuetables-client').call(this);
export default
{
    data()
    {
        return {
            url: "almacen/inventario/grupo",
            // Tabla 
            ver_modal_articulo: 0,
            columns_articulo: ['id', 'nombre'],
            list_articulo: [],
            options_articulo:
            {
                headings:
                {
                    id: 'Acciones',
                    nombre: 'Nombre'
                }, // Headings,
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            }, //options 
            // Modal
            titulo_modal_articulo: '',
            tipoAccion_modal_articulo: 0,
            articulo_modal:
            {},
            // Card grupos 
            nombre_grupo: "",
            tipo_card: 1,

            // Grupos
            ver_modal_articulos_grupo: false,
            columns_articulos_grupo: ['nombre'],
            list_articulos_grupo: [],
            options_articulos_grupo:
            {
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts,
            }, //options 

            // Pendientes
            // Grupos
            list_pendientes_models_temp: [],
            ver_modal_articulos_pendientes: false,
            columns_articulos_pendientes: ['descripcion', 'check'],
            list_articulos_pendientes: [],
            options_articulos_pendientes:
            {
                headings:
                {
                    "ai_id": "No",
                    "descripcion": "Artículo",
                    "check": "Seleccionado"
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            }, //options 
            grupo_id: 0,
            list_pendintes_temp: [], // Guardar los pendientes a agrupar
        } // return
    }, //data
    computed:
    {},
    methods:
    {
        AbrirModalArticulo(nuevo, model = [])
        {
            this.ver_modal_articulo = true;
            if (nuevo)
            {
                // Crear nuevo
                this.titulo_modal_articulo = 'Registrar Articulo';
                this.tipoAccion_modal_articulo = 1;
            }
            else
            {
                // Actualizar
                this.titulo_modal_articulo = 'Actualizar Articulo';
                this.tipoAccion_modal_articulo = 2;
                this.articulo_modal.id = model.id;
                this.articulo_modal.nombre = model.nombre;
            } // Fin if
        },

        CerrarModalArticulo()
        {
            this.ver_modal_articulo = false;
            this.articulo_modal = {};
        },

        /**
         * Registra un nuevo grupo de los artículos
         */
        Guardar(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.articulo_modal.id);
                data.append("nombre", this.articulo_modal.nombre);

                axios.post(this.url + "/guardar", data).then(res =>
                {
                    if (res.data.status)
                    {
                        toastr.success("Registrado correctamente");
                        this.CerrarModalArticulo();
                        this.ObtenerGrupos();
                    }
                    else
                    {
                        toastr.error(res.data.mensaje);
                    }
                });
            });
        },

        /**
         * Obtener los grupos de los artículos
         */
        ObtenerGrupos()
        {
            axios.get(this.url + "/obtener").then(res =>
            {
                if (res.data.status)
                {
                    this.list_articulo = res.data.grupos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Ver card de artículos del grupo
         */
        VerArticulos(grupo)
        {
            this.grupo_id = grupo.id;
            this.nombre_grupo = grupo.nombre;
            axios.get(this.url + "/obtenerporgrupo/" + grupo.id).then(res =>
            {
                if (res.data.status)
                {
                    this.list_articulos_grupo = res.data.articulos;
                    this.tipo_card = 2; // Card de articulos por grupo
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Regresa al card de grupos de artículos
         */
        Regresar()
        {
            this.tipo_card = 1;
            this.list_articulos_grupo = [];
        },

        /**
         * Muestra los artículos pendientes por agrupar
         */
        AbrirModalBuscarArticulos()
        {
            axios.get(this.url + "/obtenerendientes").then(res =>
            {
                this.ver_modal_articulos_pendientes = true;
                if (res.data.status)
                {
                    let i = 0;
                    // Lista para guardar los articulos checados
                    this.list_pendintes_temp = [];
                    this.list_articulos_pendientes = [];
                    // Recorrer todos los artículos, agregarle el index para los models del check
                    res.data.articulos.forEach(a =>
                    {
                        this.list_articulos_pendientes.push(
                        {
                            ai_id: a.ai_id,
                            descripcion: a.descripcion,
                            n: i,
                        });
                        this.list_pendintes_temp.push(false);
                        i = i + 1;
                    });
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            }).catch(x =>
            {
                console.error(x);
                toastr.error("Error. Informe al administrador");
            })
        },

        /**
         * Cerrar moda de articulos pendientes
         */
        CerrarModalArticulosPendientes()
        {
            this.ver_modal_articulos_pendientes = false;
            this.list_pendintes_temp = [];
            this.list_pendientes_models_temp = [];
        },

        /**
         * Agrupar articulos
         */
        GuardarArticulosPendientes()
        {
            if (this.list_pendientes_models_temp.length == 0)
            {
                toastr.warning("Seleccione al menos un artículo");
                return;
            }

            // Guadar
            let data = new FormData();
            data.append("grupo_id", this.grupo_id);
            data.append("list_articulos", this.list_pendientes_models_temp);
            axios.post(this.url + "/agrupararticulos", data).then(res =>
            {
                if (res.data.status)
                {
                    // Obtener los artículos del grupo
                    this.VerArticulos(
                    {
                        id: this.grupo_id,
                        nombre: this.nombre_grupo
                    });
                    toastr.success("Guardaro correctmente");
                    this.CerrarModalArticulosPendientes();
                }
                else
                {
                    toastr.error(res.data.mensaje)
                }
            })
        },

        /**
         * Marcar para agrupar
         */
        Checked(articulo, checked)
        {
            if (checked)
            {
                // Agregar
                this.list_pendientes_models_temp.push(articulo.ai_id);
            }
            else
            {
                // Quitar
                this.list_pendientes_models_temp = this.list_pendientes_models_temp.filter(id => id != articulo.ai_id);
            }
        },

        /**
         * Guardar
         */
    }, // Fin metodos
    mounted()
    {
        this.ObtenerGrupos();
    }
}
</script>
