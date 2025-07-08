<template>
<main class="main">
    <div class="">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Usuarios
                <button type="button" @click="abrirModal()" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus mr-1"></i>Nuevo
                </button>
                <button v-show="false" type="button" @click="ver_id=true" class="btn btn-dark float-sm-right">
                    <i class="fas fa-list mr-1"></i>Yolo
                </button>
            </div>
            <div class="card-body">
                <div>
                    <vue-element-loading :active="isLoading" />
                    <v-client-table ref="myTable" :columns="columns" :data="tableData" :options="options">
                        <template slot="id" slot-scope="props">
                            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                <div class="btn-group dropup" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-grip-horizontal"></i> Acciones
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        <template>
                                            <button type="button" @click="abrirModal(0,props.row)" class="dropdown-item">
                                                <i class="fas fa-edit mr-1"></i>Actualizar
                                            </button>
                                            <button v-show="ver_id" type="button" @click="Ver(props.row.id)" class="dropdown-item">
                                                <i class="fas fa-edit mr-1"></i>Ver
                                            </button>
                                        </template>

                                        <template v-if="props.row.condicion">
                                            <button class="dropdown-item" @click="Desactivar(props.row.id,0)">
                                                <i class="fas fa-ban mr-1"></i>Desactivar
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template slot="condicion" slot-scope="props">
                            <template v-if="props.row.condicion">
                                <button type="button" class="btn btn-outline-success">Activo</button>
                            </template>
                            <template v-else>
                                <button type="button" class="btn btn-outline-danger">Dado de Baja</button>
                            </template>
                        </template>
                        <template slot="session_id" slot-scope="props">
                            <template v-if="props.row.session_id">
                                <button type="button" class="btn btn-success">Online</button>
                            </template>
                            <template v-else>
                                <button type="button" class="btn btn-danger">Offline</button>
                            </template>
                        </template>
                    </v-client-table>
                </div>
            </div>
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
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form form-horizontal">
                            <div class="form-group row">
                                <label class="col-md-2 " for="text-input">Usuario</label>
                                <div class="col-md-4">
                                    <input type="text" v-validate="'required'" name="name_user" v-model="name_user" class="form-control" placeholder="Nombre de Usuario">
                                    <span class="text-danger">{{ errors.first('name_user') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 " for="text-input">Correo</label>
                                <div class="col-md-4">
                                    <input type="text" data-vv-name="Correo" v-validate="'required'" autocomplete="false" pattern="^\w+\.\w+@conserflow.com$" v-model="email" class="form-control" placeholder="Correo">
                                    <span class="text-danger">{{ errors.first('Correo') }}</span>
                                </div>
                                <label class="col-md-2 " for="text-input">Password</label>
                                <div class="col-md-4">
                                    <input type="password" name="password" v-model="password" class="form-control" placeholder="Nueva Contraseña" data-vv-name="Contraseña">
                                    <span class="text-danger">{{ errors.first('Contraseña') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 " for="text-input">Ubicacion</label>
                                <div class="col-md-4">
                                    <select class="form-control" id="tipo_ubicacion_id" name="tipo_ubicacion_id" v-model="tipo_ubicacion_id" v-validate="'excluded:0'" data-vv-as="Ubicacion">
                                        <option v-for="item in listaUbicaciones" :value="item.id" :key="item.id">{{ item.nombre }}</option>
                                    </select>
                                    <span class="text-danger">{{ errors.first('tipo_ubicacion_id') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 form-control-label" form="text-empleado">Empleado</label>
                                <div class="col-md-6">
                                    <v-select label="nombre" v-model="empleado_id" :options="listaEmpleados" data-vv-name="empleado"></v-select>
                                    <span class="text-danger">{{ errors.first('empleado') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" @click="cerrarModal()"><i class="fas fa-window-close"></i>&nbsp;Cerrar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-secondary" @click="GuardarUsuario()"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-secondary" @click="GuardarUsuario(0)"><i class="fas fa-save"></i>&nbsp;Actualizar</button>
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
var config = require('../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            url: '/usuario',
            modal: 0,

            swal_titulo: '',
            swal_msg: '',
            swal_tle: '',
            empleado_id: 0,
            tipo_ubicacion_id: '',
            listaUbicaciones: [],
            listaEmpleados: [],
            id: 0,
            ver_id: false,
            name: '',
            name_user: '',
            password: '',
            ubicacion: '',
            email: '',
            tituloModal: '',
            tipoAccion: 0,
            error: 0,
            session_id: 0,
            isLoading: false,
            columns: [
                'id',
                'name',
                'name_user',
                'email',
                "condicion"
            ],
            tableData: [],
            options:
            {
                headings:
                {
                    'id': 'Acciones',
                    'name': 'Nombre',
                    'name_user': 'Usuario',
                    'email': 'Email',
                    'condicion': 'Estado',
                    'session_id': 'Conexión',
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                listColumns:
                {
                    'usuario.condicion': [
                    {
                        id: 1,
                        text: 'Activo'
                    },
                    {
                        id: 0,
                        text: 'Baja'
                    }],
                },
                texts: config.texts
            },
        }
    },
    methods:
    {
        /**
         * Obtener todos los empleados activos para asignación de usuario
         */
        ObtenerEmpleados()
        {
            axios.get('generales/empleadoactivos').then(res =>
            {
                if (res.data.status)
                {
                    this.listaEmpleados = res.data.empleados;
                }
                else
                {
                    toas.error(res.data.mensaje);
                }
            });
        },

        /**
         * Obtiene las ubicaciones
         */
        ObtenerUbicaciones()
        {
            axios.get('/rh/catalogos/tipoubicacion').then(response =>
            {
                this.listaUbicaciones = response.data.ubicaciones;
            })
        },

        /**
         * Obtener todos los usuarios registrados
         */
        GetUsers()
        {
            this.isLoading = true;
            axios.get(this.url).then(res =>
            {
                this.isLoading = false;
                this.tableData = res.data.usuarios;
            });
        },

        /**
         * Registrar o actualizar el usuario
         */
        async GuardarUsuario(nuevo = true)
        {
            let isValid = await this.$validator.validate();
            if (!isValid) return;
            this.isLoading = true;
            let data = new FormData();
            if (!nuevo)
                data.append('id', this.id);
            data.append('name_user', this.name_user);
            data.append('name', this.empleado_id.nombre);
            data.append('password', this.password);
            data.append('email', this.email);
            data.append('tipo_ubicacion_id', this.tipo_ubicacion_id);
            data.append("empleado_id", this.empleado_id.id);
            axios.post(this.url, data).then(res =>
            {
                this.isLoading = false;
                if (res.data.status)
                {
                    this.cerrarModal();
                    toastr.success('Usuario Registrado Correctamente')
                    this.GetUsers();
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Desactivar el usuario
         */
        Desactivar(id)
        {
            swal(
            {
                title: "Desactivar este usuario",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
                reverseButtons: true
            }).then((result) =>
            {
                if (result.value)
                {

                    axios.get(this.url + '/' + id + '/edit').then(res =>
                    {
                        toastr.success("Desactivado correctamente");
                        this.GetUsers();
                    });
                }
            })
        },

        cerrarModal()
        {
            this.modal = 0;
            this.name = '';
            this.name_user = '';
            this.password = '';
            this.tipo_ubicacion_id = '';
            this.email = '';
            this.tipoAccion = 1;
            this.empleado_id = 0;
        },

        abrirModal(nuevo = true, data = {})
        {
            this.ObtenerEmpleados();
            this.ObtenerUbicaciones();
            this.modal = 1;
            if (nuevo)
            {
                this.tituloModal = 'Registrar Usuario';
                this.tipoAccion = 1;
            }
            else
            {
                this.tituloModal = 'Actualizar Usuario ';
                this.id = data.id;
                this.name_user = data.name_user;
                this.email = data.email;
                this.tipo_ubicacion_id = data.tipo_ubicacion_id;
                this.empleado_id = {
                    id: data.e_id,
                    nombre: data.name
                };
                this.name = this.empleado_id.nombre;
                this.tipoAccion = 2;
            }
        },
        Ver(id)
        {
            axios.post("sistema/permisos/cambiar",
            {
                user_id: id
            }).then(res =>
            {
            });
        }
    },
    mounted()
    {
        this.GetUsers();
    }
}
</script>
