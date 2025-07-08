<template>
<main class="main">
    <!-- Ejemplo de tabla Listado -->
    <div class="card">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Departamentos
            <button v-if="PermisosCRUD.Create" type="button" @click="abrirModal('departamento','registrar')" class="btn btn-dark float-sm-right">
                <i class="fas fa-plus"></i>&nbsp;Nuevo
            </button>
        </div>
        <div class="card-body">

            <v-client-table :columns="columns" :data="list_departamentos" :options="options" ref="myTable">
                <template slot="id" slot-scope="props">
                    <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                        <div class="btn-group dropup" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-grip-horizontal"></i> Acciones
                            </button>
                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                <button v-if="PermisosCRUD.Update" type="button" @click="abrirModal(false,props.row)" class="dropdown-item">
                                    <i class="fa fa-edit"></i> Actualizar
                                </button>
                                <button v-if="PermisosCRUD.Delete" type="button" class="dropdown-item" @click="desactivarDepartamento(props.row.id)">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>

        </div>
    </div>
    <!-- Fin ejemplo de tabla Listado -->

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
                        <!-- <form action="" method="post" enctype="multipart/form-data" class="form-horizontal"> -->
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="nombre">Nombre</label>
                            <div class="col-md-9">
                                <input type="text" v-validate="'required|max:100'" name="nombre" v-model="departamento.nombre" class="form-control" placeholder="Nombre del departamento" autocomplete="off" id="nombre">
                                <span class="text-danger">{{ errors.first('nombre') }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="direccion_administrativa_id">Dirección administrativa</label>
                            <div class="col-md-9">
                                <select class="form-control" id="direccion_administrativa_id" name="direccion_administrativa_id" v-model="departamento.direccion_administrativa_id" v-validate="'excluded:0'" data-vv-as="Dirección administrativa">
                                    <option v-for="item in listaDirecciones" :value="item.id" :key="item.id">{{ item.nombre }}</option>
                                </select>
                                <span class="text-danger">{{ errors.first('direccion_administrativa_id') }}</span>
                            </div>
                        </div>

                        <!-- </form> -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" @click="cerrarModal()"><i class="fas fa-window-close"></i>&nbsp;Cerrar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-secondary" @click="guardarDepartamento(true)"><i class="fas fa-save"></i>&nbsp;Guardar</button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-secondary" @click="guardarDepartamento(false)"><i class="fas fa-save"></i>&nbsp;Actualizar</button>
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

export default
{
    data()
    {
        return {
            url: "rh/catalogos/departamento",
            departamento:
            {
                id: 0,
                direccion_administrativa_id: 0,
                nombre: ''
            },
            listaDirecciones: [],
            modal: 0,
            tituloModal: '',
            tipoAccion: 0,
            isLoading: false,
            columns: ['id', 'nombre', 'direccion'],
            list_departamentos: [],
            options:
            {
                headings:
                {
                    nombre: 'Nombre',
                    direccion: 'Dirección',
                    id: 'Acciones',
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            },
            PermisosCRUD:
            {},
        }
    },
    methods:
    {
        ObtenerDepartamentos()
        {
            axios.get(this.url + "/obtener").then(res =>
            {
                this.list_departamentos = res.data.departamentos;
            });
        },
        getListaDirecciones()
        {
            axios.get("rh/catalogos/direccionesadministrativa/obtener").then(res =>
            {
                this.listaDirecciones = res.data.direcciones;
            });
        },

        guardarDepartamento(nuevo)
        {
            this.$validator.validate().then(result =>
            {
                if (result)
                {
                    this.isLoading = true;
                    let data = new FormData();
                    data.append("nombre", this.departamento.nombre);
                    data.append("direccion_administrativa_id", this.departamento.direccion_administrativa_id);
                    if (!nuevo)
                        data.append("id", this.departamento.id);

                    axios(
                    {
                        method: 'POST',
                        url: this.url + "/guardar",
                        data
                    }).then(res =>
                    {
                        this.isLoading = false;
                        if (res.data.status)
                        {
                            this.cerrarModal();
                            this.ObtenerDepartamentos();
                            if (nuevo)
                            {
                                toastr.success('Departamento Registrado Correctamente');
                            }
                            else
                            {
                                toastr.success('Departamento Actualizado Correctamente');
                            }
                        }
                        else
                        {
                            toastr.error(res.data.error);
                        }
                    });
                }
            });
        },

        desactivarDepartamento(id)
        {
            swal(
            {
                title: 'Está seguro de desactivar este departamento?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4dbd74',
                cancelButtonColor: '#f86c6b',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) =>
            {
                if (result.value)
                {

                    axios.post(this.url + '/desactivar',
                    {
                        'id': id
                    }).then(res =>
                    {
                        toastr.error('Departamento Desactivado Correctamente');
                        this.ObtenerDepartamentos();
                    });
                }
            })
        },
        cerrarModal()
        {
            Utilerias.resetObject(this.departamento);
            this.modal = 0;
            Utilerias.resetObject(this.departamento);
        },

        abrirModal(nuevo = true, departamento = {})
        {
            this.modal = 1;
            this.ObtenerDepartamentos();
            if (nuevo)
            {
                this.modal = 1;
                this.tituloModal = 'Registrar departamento';
                this.tipoAccion = 1;

            }
            else
            {
                this.tituloModal = "Actualizar departamento";
                this.tipoAccion = 2;
                this.departamento = {
                    ...departamento
                };
            }
        }
    },
    mounted()
    {
        this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
        this.getListaDirecciones();
        this.ObtenerDepartamentos();
    }
}
</script>
