<template>
<main class="main">
    <!-- Listado SatCatUnidades -->
    <div class="card" v-show="tipoCardSatCatUnidades==1">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> UNIDADES
            <button type="button" @click="AbrirModalSatCatUnidades(true)" class="btn btn-dark float-sm-right">
                <i class="fas fa-plus"></i>&nbsp;Nuevo
            </button>
        </div>
        <div class="card-body">
            <vue-element-loading :active="isObtenersatcatunidades_loading" />
            <v-client-table :columns="columns_satcatunidades" :data="list_satcatunidades" :options="options_satcatunidades">
                <template slot="id" slot-scope="props">
                    <div class="btn-group" role="group">
                        <div class="btn-group dropup" role="group">
                            <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-grip-horizontal"></i> Acciones
                            </button>
                            <div class="dropdown-menu">
                                <button type="button" class="dropdown-item" @click="AbrirModalSatCatUnidades(false,props.row)">
                                    <i class="fas fa-edit"></i> Actualizar
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>
        </div>
    </div>

    <!--Inicio del modal SatCatUnidades-->
    <div v-if="ver_modal_satcatunidades" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_satcatunidades}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_satcatunidades"></h4>
                        <button type="button" class="close" @click="CerrarModalSatCatUnidades()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardarsatcatunidades_loading" />
                        <div>
                            <!-- Formulario -->

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Unidad</label>
                                <div class="col-md-9">
                                    <input type="text" maxlength="3" minlength="1" v-validate="'required'" v-model="satcatunidades.c_unidad" class="form-control" data-vv-name="Unidad" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Unidad') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Nombre</label>
                                <div class="col-md-9">
                                    <input type="text" maxlength="150" minlength="1" v-validate="'required'" v-model="satcatunidades.nombre" class="form-control" data-vv-name="Nombre" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Nombre') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Descripción</label>
                                <div class="col-md-9">
                                    <input type="text" v-validate="'required'" v-model="satcatunidades.descripcion" class="form-control" data-vv-name="Descripción" />
                                    <span class="text-danger">{{ errors.first('Descripción') }}</span>
                                </div>
                            </div>

                            <!-- Formulario -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardarsatcatunidades_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalSatCatUnidades()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                            <button type="button" v-if="tipoAccion_satcatunidades==1" class="btn btn-secondary" @click="RegistrarSatCatUnidades(true)">
                                <i class="fas fa-save"></i>&nbsp;Guardar
                            </button>
                            <button type="button" v-if="tipoAccion_satcatunidades==2" class="btn btn-secondary" @click="RegistrarSatCatUnidades(false)">
                                <i class="fas fa-save"></i>&nbsp;Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal SatCatUnidades-->

</main>
</template>

<script>
var config = require('../../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            //// SatCatUnidades
            url_satcatunidades: "tesoreria/catalogos/unidades",
            tipoAccion_satcatunidades: 1,
            tipoCardSatCatUnidades: 1,
            ver_modal_satcatunidades: false,
            tituloModal_satcatunidades: "",
            satcatunidades_id: 0,
            isGuardarsatcatunidades_loading: false,
            isObtenersatcatunidades_loading: false,
            columns_satcatunidades: ["id",
                "c_unidad",
                "nombre",
                "descripcion"
            ],
            list_satcatunidades: [],
            satcatunidades:
            {},
            options_satcatunidades:
            {
                headings:
                {
                    id: "Acciones",
                    c_unidad: "Unidad",
                    nombre: "Nombre",
                    descripcion: "Descripción",

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
         * Obtener todos los registros
         */
        ObtenerSatCatUnidades()
        {
            this.isObtenersatcatunidades_loading = true;
            axios.get(this.url_satcatunidades + "/obtener").then(res =>
            {
                this.isObtenersatcatunidades_loading = false;
                if (res.data.status)
                {
                    this.list_satcatunidades = res.data.unidades;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Abrir modal SatCatUnidades
         */
        AbrirModalSatCatUnidades(nuevo, data = {})
        {
            this.ver_modal_satcatunidades = true;
            if (nuevo)
            {
                this.tituloModal_satcatunidades = "Registrar Unidades";
                this.tipoAccion_satcatunidades = 1;
            }
            else
            {
                this.satcatunidades_id=data.id;
                this.tituloModal_satcatunidades = "Actualizar Unidades";
                this.tipoAccion_satcatunidades = 2;
                this.satcatunidades = {
                    ...data
                };
            }
        },

        /**
         * Registrar SatCatUnidades
         */
        RegistrarSatCatUnidades(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.satcatunidades_id);
                data.append("c_unidad", this.satcatunidades.c_unidad);
                data.append("nombre", this.satcatunidades.nombre);
                data.append("descripcion", this.satcatunidades.descripcion);

                this.isGuardarsatcatunidades_loading = true;
                axios.post(this.url_satcatunidades + "/guardar", data).then(res =>
                {
                    this.isGuardarsatcatunidades_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.ObtenerSatCatUnidades();
                        this.CerrarModalSatCatUnidades();
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
        CerrarModalSatCatUnidades()
        {
            this.satcatunidades={};
            this.ver_modal_satcatunidades = false;
        },

    },
    mounted()
    {
        this.ObtenerSatCatUnidades();
    }
}
</script>
