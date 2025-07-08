<template>
<main>
    <!-- Listado DatosBancariosEmpleado -->
    <div class="card">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> DATOS BANCARIOS
            <button v-show="PermisosCrud.Create && empleado_activo" type="button" @click="AbrirModalDatosBancariosEmpleado(true)" class="btn btn-dark float-sm-right">
                <i class="fas fa-plus"></i>&nbsp;Nuevo
            </button>
        </div>
        <div class="card-body">
            <vue-element-loading :active="isObtenerdatosbancariosempleado_loading" />
            <v-client-table :columns="columns_datosbancariosempleado" :data="list_datosbancariosempleado" :options="options_datosbancariosempleado">
                <template slot="id" slot-scope="props">
                    <div class="btn-group" role="group">
                        <div class="btn-group dropup" role="group">
                            <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-grip-horizontal"></i> Acciones
                            </button>
                            <div class="dropdown-menu">
                                <button v-show="PermisosCrud.Update" type="button" class="dropdown-item" @click="AbrirModalDatosBancariosEmpleado(false,props.row)">
                                    <i class="fas fa-edit"></i> Actualizar
                                </button>
                                <button v-show="PermisosCrud.Delete && empleado_activo" type="button" class="dropdown-item" @click="EliminarBanco(props.row)">
                                    <i class="fas fa-times"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>
        </div>
    </div>

    <!--Inicio del modal DatosBancariosEmpleado-->
    <div v-if="ver_modal_datosbancariosempleado" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_datosbancariosempleado}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_datosbancariosempleado"></h4>
                        <button type="button" class="close" @click="CerrarModalDatosBancariosEmpleado()" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardardatosbancariosempleado_loading" />
                        <div>
                            <!-- Formulario -->

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Banco</label>
                                <div class="col-md-9">
                                    <select class="form-control" v-model="datosbancariosempleado.banco_id">
                                        <option :key="i" v-for="(b,i) in list_bancos_catalogo" :value="b.id">{{b.nombre}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">No. tarjeta</label>
                                <div class="col-md-9">
                                    <input type="text" maxlength="16" minlength="12" v-validate="'required'" v-model="datosbancariosempleado.numero_tarjeta" class="form-control" data-vv-name="No. tarjeta" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('No. tarjeta') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">No. cuenta</label>
                                <div class="col-md-9">
                                    <input type="text" maxlength="18" minlength="10" v-validate="'required'" v-model="datosbancariosempleado.numero_cuenta" class="form-control" data-vv-name="No. cuenta" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('No. cuenta') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Clabe</label>
                                <div class="col-md-9">
                                    <input type="text" maxlength="18" minlength="12" v-validate="'required'" v-model="datosbancariosempleado.clabe" class="form-control" data-vv-name="Clabe" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Clabe') }}</span>
                                </div>
                            </div>

                            <!-- Formulario -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardardatosbancariosempleado_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalDatosBancariosEmpleado()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                            <template v-if="tipoAccion_datosbancariosempleado==1">
                                <button  type="button" v-show="empleado_activo" class="btn btn-secondary" @click="RegistrarDatosBancariosEmpleado(true)">
                                    <i class="fas fa-save"></i>&nbsp;Guardar
                                </button>
                            </template>
                            <template v-if="tipoAccion_datosbancariosempleado==2">
                                <button type="button" v-show="empleado_activo" class="btn btn-secondary" @click="RegistrarDatosBancariosEmpleado(false)">
                                    <i class="fas fa-save"></i>&nbsp;Actualizar
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal DatosBancariosEmpleado-->

</main>
</template>

<script>
var config = require('../../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            //// DatosBancariosEmpleado
            url_datosbancariosempleado: "rh/empleados/banco",
            PermisosCrud:
            {},
            empleado_activo: false,
            tipoAccion_datosbancariosempleado: 1,
            tipoCardDatosBancariosEmpleado: 1,
            ver_modal_datosbancariosempleado: false,
            tituloModal_datosbancariosempleado: "",
            datosbancariosempleado_id: 0,
            list_bancos_catalogo: [],
            isGuardardatosbancariosempleado_loading: false,
            isObtenerdatosbancariosempleado_loading: false,
            columns_datosbancariosempleado: [
                "id",
                "bnombre",
                "numero_tarjeta",
                "numero_cuenta",
                "clabe",
            ],
            list_datosbancariosempleado: [],
            datosbancariosempleado:
            {
                banco_id: 1
            },
            options_datosbancariosempleado:
            {
                headings:
                {
                    id: "Acciones",
                    numero_tarjeta: "No. tarjeta",
                    numero_cuenta: "No. cuenta",
                    clabe: "Clabe",
                    bnombre: "Banco"
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
        // Metodos
        /**
         * Obtener todos los bancos registrados
         */
        CargarDatosBancarios(empleado, PermisosCrud = null)
        {
            // Asignar permisos crud
            if (PermisosCrud != null) this.PermisosCrud = PermisosCrud;
            this.isObtenerdatosbancariosempleado_loading = true;
            this.datosbancariosempleado = {
                empleado_id: empleado.id,
            };
            this.empleado_activo = empleado.condicion;
            axios.get(this.url_datosbancariosempleado + "/obtener/" + empleado.id).then(res =>
            {
                this.isObtenerdatosbancariosempleado_loading = false;

                if (res.data.status)
                {
                    this.list_datosbancariosempleado = res.data.bancos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Abrir modal DatosBancariosEmpleado
         */
        AbrirModalDatosBancariosEmpleado(nuevo, data = {})
        {
            this.CargarBancos();
            this.ver_modal_datosbancariosempleado = true;
            if (nuevo)
            {
                this.tituloModal_datosbancariosempleado = "Registrar Datos bancarios";
                this.tipoAccion_datosbancariosempleado = 1;
            }
            else
            {
                this.tituloModal_datosbancariosempleado = "Actualizar Datos bancarios";
                this.tipoAccion_datosbancariosempleado = 2;
                this.datosbancariosempleado = {
                    ...data
                };
            }
        },

        /**
         * Obtiene los catalogos de bancos
         */
        CargarBancos()
        {
            axios.get("rh/catalogos/bancos").then(res =>
            {
                if (res.data.status)
                {
                    this.list_bancos_catalogo = res.data.bancos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Registrar DatosBancariosEmpleado
         */
        RegistrarDatosBancariosEmpleado(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                let empleado_id = this.datosbancariosempleado.empleado_id;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.datosbancariosempleado.id);
                data.append("numero_tarjeta", this.datosbancariosempleado.numero_tarjeta);
                data.append("empleado_id", empleado_id);
                data.append("numero_cuenta", this.datosbancariosempleado.numero_cuenta);
                data.append("clabe", this.datosbancariosempleado.clabe);
                data.append("banco_id", this.datosbancariosempleado.banco_id);

                this.isGuardardatosbancariosempleado_loading = true;
                axios.post(this.url_datosbancariosempleado + "/guardar", data).then(res =>
                {
                    this.isGuardardatosbancariosempleado_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.CerrarModalDatosBancariosEmpleado();
                        this.CargarDatosBancarios(
                        {
                            id: empleado_id,
                            condicion:this.empleado_activo
                        });
                    }
                    else
                    {
                        toastr.error(res.data.mensaje);
                    }
                })

            })
        },

        /**
         * Cerrar modal
         */
        CerrarModalDatosBancariosEmpleado()
        {
            this.ver_modal_datosbancariosempleado = false;
            this.datosbancariosempleado = {};
        },

        /**
         * Eliminar banco
         */
        EliminarBanco(banco)
        {
            this.isObtenerdatosbancariosempleado_loading = true;
            axios.post(this.url_datosbancariosempleado + "/eliminar",
            {
                banco_id: banco.id,
                empleado_id: banco.empleado_id
            }).then(res =>
            {
                this.isObtenerdatosbancariosempleado_loading = true;
                if (res.data.status)
                {
                    toastr.success("Eliminado correctamente");
                    this.CargarDatosBancarios(
                    {
                        id: banco.empleado_id,
                        condicion:this.empleado_activo
                    });
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        }
    }
}
</script>
