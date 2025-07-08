<template>
<main class="main">
    <div class="container-fluid">

        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Conductores
                <button type="button" @click="abrirModal()" class="btn btn-dark float-sm-right">
                    <i class="fas fa-plus mr-1"></i>Nuevo
                </button>
            </div>
            <div class="card-body">
                <vue-element-loading :active="isLoading_conductores" />
                <h5>Activos</h5>
                <v-client-table :data="list_conductores_activos" :columns="columns" :options="options_conductores">
                    <template slot='id' slot-scope='props'>
                        <div class='btn-group' role='group'>
                            <button id='btn_id' type='button' class='btn btn-outline-dark dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                <i class='fas fa-grip-horizontal'></i> Acciones
                            </button>
                            <div class='dropdown-menu'>
                                <button type='button' @click='abrirModal(false, props.row)' class='dropdown-item'>
                                    <i class='fas fa-edit'></i>Actualizar
                                </button>
                            </div>
                        </div>
                    </template>
                    <template slot="licencia_doc" slot-scope="props">
                        <button class="btn btn-outline-dark" @click="descargarComprobante(props.row.licencia_doc)">
                            <i class="fas fa-file-pdf"></i>
                            <i class="fas fa-download"></i>
                        </button>
                    </template>
                </v-client-table>

                <br>
                <hr>
                <h5>Inactivos</h5>
                <v-client-table :data="list_conductores_inactivos" :columns="columns_conductores_inactivos" :options="options_conductores">
                </v-client-table>

            </div>
        </div>

        <!-- Modal para registro/actualización-->
        <div class='modal fade' tabindex='-1' :class="{'mostrar' : modal}" role='dialog' aria-labelledby='myModalLabel' style='display: none;' aria-hidden='true'>
            <div class='modal-dialog modal-dark modal-lg' role='document'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h4 class='modal-title' v-text='tituloModal'></h4>
                        <button type='button' class='close' @click='CerrarModal()' aria-label='Close'>
                            <span aria-hidden='true'>×</span>
                        </button>
                    </div>
                    <div class='modal-body'>
                        <vue-element-loading :active="isLoading" />
                        <div>
                            <div class="form-row">
                                <div class="col-md-7 mb-3">
                                    <label>Empleado</label>
                                    <v-select :options="listaEmpleados" label="nombre" v-model="empleado" data-vv-name="Empleado" v-validate="'required'"></v-select>
                                    <span class="text-danger">{{errors.first('Empleado')}}</span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4 mb-3">
                                    <label>No. Licencia</label>
                                    <input placeholder="No. de licencia" maxlength="20" type="text" class="form-control" v-model="licencia" data-vv-name="Licencia" v-validate="'required'">
                                    <span class="text-danger">{{errors.first('Licencia')}}</span>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Tipo</label>
                                    <input type="text" placeholder="Tipo de licencia" maxlength="35" class="form-control" v-model="tipo" data-vv-name="Tipo" v-validate="'required'">
                                    <span class="text-danger">{{errors.first('Tipo')}}</span>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Vigencia</label>
                                    <input type="date" class="form-control" v-validate="'required'" v-model="vigencia" data-vv-name="Vigencia">
                                    <span class="text-danger">{{errors.first('Vigencia')}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label>Entidad</label>
                                    <input type="text" placeholder="Estado" maxlength="20" v-validate="'required'" class="form-control" v-model="entidad" data-vv-name="Entidad">
                                    <span class="text-danger">{{errors.first('Entidad')}}</span>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Licencia</label>
                                    <input id="file_comprobante" accept="application/pdf" type="file" class="form-control" name="pdf">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <vue-element-loading :active="isLoading" />
                        <div>
                            <button type='button' class='btn btn-outline-dark' @click='CerrarModal()'><i class='mr-1 fas fa-times'></i>Cerrar</button>
                            <button type='button' v-if='tipoAccion == 1' class='btn btn-secondary' @click='Guardar(true)'><i class='mr-1 fas fa-save'></i>Guardar</button>
                            <button type='button' v-if='tipoAccion == 2' class='btn btn-secondary' @click='Guardar(false)'><i class='mr-1 fas fa-save'></i>Actualizar</button>
                        </div>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- Modal -->

    </div>
</main>
</template>

<script>
var config = require('../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            modal: 0,
            tituloModal: '',
            tipoAccion: 0,
            isLoading_conductores: false,
            isLoading: false,
            listaEmpleados: [],
            img: [],
            id: 0,
            empleado: '',
            licencia: '',
            entidad: "",
            tipo: '',
            vigencia: '',
            id: 0,
            poliza: '',
            list_conductores_activos: [],
            list_conductores_inactivos: [],
            columns_conductores_inactivos: [
                'nombre',
                'licencia',
                'tipo',
                'vigencia',
                'estado',
            ],
            columns: [
                'id',
                'nombre',
                'licencia',
                'tipo',
                'vigencia',
                'estado',
                'licencia_doc'
            ],
            options_conductores:
            {
                headings:
                {
                    'id': 'Acciones',
                    'nombre': 'Empleado',
                    'licencia': 'Licencia',
                    'tipo': 'Tipo',
                    'vigencia': 'Vigencia',
                    'estado': "Entidad",
                    "licencia_doc": "Licencia"
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
         * Obtener los conductores activos
         */
        ObtenerConductores()
        {
            this.isLoading_conductores = true;
            axios.get('vehiculos/conductores/obtener').then(res =>
            {
                if (res.data.status)
                {
                    this.list_conductores_activos = res.data.conductores.filter(c => c.condicion == 1);
                    this.list_conductores_inactivos = res.data.conductores.filter(c => c.condicion == 0);
                    this.isLoading_conductores = false;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Obtener todos los empleados activos para registrar conductor
         */
        ObtenerEmpleados()
        {
            axios.get('generales/empleadoactivos').then(res =>
            {
                if (res.data.status)
                    this.listaEmpleados = res.data.empleados;
                else
                    toastr.error(res.data.mensaje);
            });
        },

        /**
         * Abrir modal para registro/actualización de conductor
         */
        abrirModal(nuevo = true, data = [])
        {
            this.ObtenerEmpleados();
            this.modal = 1;
            if (nuevo)
            {
                this.tipoAccion = 1;
                this.tituloModal = "Registrar Conductor";
            }
            else
            {
                this.tipoAccion = 2;
                this.tituloModal = 'Actualizar Conductor';
                this.id = data.id;
                this.empleado = {
                    id: data.empleado_id,
                    nombre: data.nombre
                };
                this.licencia = data.licencia;
                this.entidad = data.estado;
                this.tipo = data.tipo;
                this.vigencia = data.vigencia;
            }
        },

        /**
         * Cerrar modal
         */
        CerrarModal()
        {
            this.modal = 0;
            this.empleado = {};
            this.licencia = "";
            this.tipo = "";
            this.vigencia = "";
            this.entidad = "";
            this.img = [];
            $("#file_comprobante").val("");
        },

        /**
         * Registrar/actualizar conductor
         */
        Guardar(nuevo)
        {
            this.$validator.validate().then(result =>
            {
                if (result)
                {
                    let files = $("#file_comprobante").prop("files");
                    let formData = new FormData();

                    // Si es actualización, pdf no es obligatorio
                    if (nuevo)
                    {
                        if (files.length < 1)
                        {
                            toastr.warning("Ingrese la licencia del conductor");
                            return
                        }
                        formData.append('comprobante', files[0]);
                    }

                    this.isLoading = true;
                    if (!nuevo)
                        formData.append('id', this.id);
                    formData.append('empleado_id', this.empleado.id);
                    formData.append('licencia', this.licencia);
                    formData.append('tipo', this.tipo);
                    formData.append('vigencia', this.vigencia);
                    formData.append('estado', this.entidad);
                    if (files.length == 1)
                        formData.append('comprobante', files[0]);

                    axios.post("vehiculos/conductores/guardar", formData).then(res =>
                    {
                        this.isLoading = false;
                        if (res.data.status)
                        {
                            toastr.success('Guardado correctamente');
                            this.CerrarModal();
                            this.ObtenerConductores();
                        }
                        else
                            toastr.error(res.data.mensaje);
                    });
                }
            });
        },

        /**
         * Descarga el comprobante de licencia
         */
        descargarComprobante(archivo)
        {
            axios(
            {
                url: '/vehiculos/conductores/licencia/' + archivo,
                method: 'GET',
                responseType: 'blob', // importante
            }).then(response =>
            {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', archivo); //archivo = nombre del archivo alojado en el ftp
                document.body.appendChild(link);
                link.click();

                axios.get('vehiculos/conductores/del_temp/' + archivo)
                    .then(_)
                    .catch(error =>
                    {
                        alert("Error al descargar el archivo. Notifique al administrador");
                        console.log(error);
                    });
            }).catch(r =>
            {
                toastr.error("Documento no encontrado");
            });
        },
    },
    mounted()
    {
        this.ObtenerConductores();
    }
}
</script>
