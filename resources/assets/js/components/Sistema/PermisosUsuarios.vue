<template>
<main class="main">
    <div class="">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Asignar permisos
            </div>
            <div class="card-body">
                <v-client-table :columns="columns" :data="list_usuarios" :options="options" ref="myTable">
                    <template slot="id" slot-scope="props">
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group dropup" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal mr-1"></i>Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <template>
                                        <button type="button" @click="abrirModal(props.row)" class="dropdown-item">
                                            <i class="fas fa-edit"></i>Actualizar Permisos
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </v-client-table>
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
                        <div class="form-group row">
                            <label class="ml-3 form-control-label font-weight-bold" for="nombre">Usuario</label>
                            <div class="col-md-6">
								<template v-if="tipoPermisos !=null">
                                <label>{{tipoPermisos.nombre}}</label>
								</template>
                            </div>
                        </div>
                        <div class="table-scroll">
                            <table class="table-main table">
                                <thead class="thead-dark">
                                    <!-- Modulos -->
                                    <tr class="header-modulo">
                                        <template v-for="(item,i) in tableModulos">
                                            <td class="a" :key="i">
                                                <div class="text-center1 item">
                                                    {{item.nombre}}
                                                </div>
                                            </td>
                                        </template>
                                    </tr>
                                    <tr>
                                        <td v-for="item in tableModulos" :key="item.id">
                                            <div v-for="itemM in tableMenus" :key="itemM.id">
                                                <div class="form-check checkbox" v-if="(item.id == itemM.m.modulo_id)">
                                                    <!-- Submenu -->
                                                    <template v-if="itemM.m.page != null">
                                                        <input class="" :value="itemM.m.id" :id="'smn_'+itemM.m.id" type="checkbox" v-on:change="menusupdate(tipoPermisos.id,itemM.m,0)" v-model="menus">
                                                        <label class="form-check-label" :for="'smn_'+itemM.m.id" style="margin-left:-1rem">
                                                            {{itemM.m.name}}
                                                        </label>
                                                    </template>
                                                    <!-- Menu -->
                                                    <template v-if="itemM.m.page == null">
                                                        <a class="nav-link" href="#" role="tab">{{itemM.m.name}}</a>
                                                        <div v-for="itemMS in itemM.s" :key="itemMS.id">
                                                            <input class="" :value="itemMS.id" :id="'mn_'+itemMS.id" type="checkbox" v-on:change="submenusupdate(tipoPermisos.id,itemMS,0)" v-model="submenus">
                                                            <label class="form-check-label" :for="'mn_'+itemMS.id">
                                                                {{itemMS.submenu}}
                                                            </label>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" @click="cerrarModal()"><i class="fas fa-times"></i>&nbsp;Cerrar</button>
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

<style>
.table-scroll {
    overflow-x: scroll;
    overflow-y: visible;
    padding-bottom: 5px;
    scroll-behavior: smooth;
    scroll-snap-type: x mandatory;
}

.item {
    scroll-snap-align: start;
    width: 12rem;
}

.header-modulo {
    font-weight: bold;
    text-align: center;
}
</style>

<script>
import Utilerias from '../Herramientas/utilerias.js';
var config = require('../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            url: '/usuario',
            tipoPermisos:
            {
                id: 0,
                nombre: ''
            },
            modal: 0,
            tituloModal: '',
            tipoAccion: 0,
            isLoading: false,
            columns: ['id', 'name', "name_user"],
            list_usuarios: [],
            tableModulos: [],
            tableMenus: [],
            events: [],
            tableSubMenus: [],
            menus: [],
            submenus: [],
            options:
            {
                headings:
                {
                    'name': 'Usuario',
                    'id': 'Acciones',
                    'name_user': 'Usuario',
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

    methods:
    {
        /**
         * Obtener todos los usuarios activos
         */
        GetUsers()
        {
            axios.get("sistema/usuariosactivos").then(res =>
            {
                this.list_usuarios = res.data.usuarios;
            });
        },

        crear()
        {
            let me = this;
            axios.get('/PermisoUser/create').then(response =>
                {
                    me.tableModulos = response.data;
                })
                .catch(function (error)
                {
                    console.log(error);

                });
        },

        editado()
        {
            let me = this;
            axios.get('/PermisoUser/edit').then(response =>
                {
                    me.tableSubMenus = response.data;
                })
                .catch(function (error)
                {
                    console.log(error);
                });
        },
        tablamenu()
        {
            let me = this;
            axios.get('/PermisoUser/0').then(response =>
                {
                    me.tableMenus = response.data;
                })
                .catch(function (error)
                {
                    console.log(error);
                });
        },

        menusupdate(id, data = [], nuevo)
        {
            let me = this;
            axios(
            {
                method: nuevo ? 'POST' : 'PUT',
                url: nuevo ? me.url : '/PermisoUser/' + id + '/actualizar',
                data:
                {
                    'modulo': data.modulo_id,
                    'id': id,
                    'id_menu': data.id

                }
            }).then(function (response)
            {

                if (response.data.status)
                {

                    toastr.success(' Actualizada Correctamente');

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

        },
        submenusupdate(id, data = [], nuevo)
        {
            let me = this;
            axios(
                {
                    method: nuevo ? 'POST' : 'PUT',
                    url: nuevo ? me.url : '/PermisoUser/' + id + '/actualizarsub',
                    data:
                    {
                        'menu_id': data.elementos_menu_id,
                        'id': id,
                        'id_submenu': data.id

                    }
                })
                .then(function (response)
                {

                    if (response.data.status)
                    {

                        toastr.success(' Actualizada Correctamente');

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

        },

        cerrarModal()
        {
            this.modal = 0;
            this.tituloModal = '';
            Utilerias.resetObject(this.tipoPermisos);

        },
        abrirModal(data = [])
        {
            this.modal = 1;
            this.tituloModal = 'Actualizar Permisos';
            this.tipoAccion = 2;
            this.tipoPermisos.id = data['id'];
            var id = data['id'];
            this.tipoPermisos.nombre = data['name'];
            var cadena = [];
            var cadenauno = [];
            axios.get('/menusidemp/' + id).then(function (response)
            {
                for (var i = 0; i < response.data.length; i++)
                {
                    cadena.push(response.data[i].elementos_menu_id);
                    cadenauno.push(response.data[i].elementos_submenu_id);
                }
            });
            this.menus = cadena;
            this.submenus = cadenauno;
        }
    },
    mounted()
    {
        this.GetUsers();
        this.crear();
        this.editado();
        this.tablamenu();
    }
}
</script>
