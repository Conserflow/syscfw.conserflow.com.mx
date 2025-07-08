<template>
<main class="main">
    <!-- Listado DiasFestivos -->
    <div class="card" v-show="tipoCardDiasFestivos==1">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Días Festivos
            <button type="button" @click="AbrirModalDiasFestivos(true)" class="btn btn-dark float-sm-right">
                <i class="fas fa-plus"></i>&nbsp;Nuevo
            </button>
        </div>
        <div class="card-body">
            <vue-element-loading :active="isObtenerdiasfestivos_loading" />
            <v-client-table :columns="columns_diasfestivos" :data="list_diasfestivos" :options="options_diasfestivos">
                <template slot="id">
                    <div class="btn-group" role="group">
                        <div class="btn-group dropup" role="group">
                            <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-grip-horizontal"></i> Acciones
                            </button>
                            <div class="dropdown-menu">
                                <!-- <button type="button" class="dropdown-item" @click="AbrirModalDiasFestivos(false,props.row)">
                                    <i class="fas fa-edit"></i> Actualizar
                                </button> -->
                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>
        </div>
    </div>

    <!--Inicio del modal DiasFestivos-->
    <div v-if="ver_modal_diasfestivos" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_diasfestivos}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_diasfestivos"></h4>
                        <button type="button" class="close" @click="CerrarModalDiasFestivos()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardardiasfestivos_loading" />
                        <div>
                            <!-- Formulario -->

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Día</label>
                                <div class="col-md-9">
                                    <input type="date" v-validate="'required'" v-model="diasfestivos.dia" class="form-control" data-vv-name="Día" />
                                    <span class="text-danger">{{ errors.first('Día') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Descipción</label>
                                <div class="col-md-9">
                                    <input type="text" v-validate="'required'" v-model="diasfestivos.descripcion" class="form-control" data-vv-name="Descripción" />
                                    <span class="text-danger">{{ errors.first('Descripción') }}</span>
                                </div>
                            </div>

                            <!-- Formulario -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardardiasfestivos_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalDiasFestivos()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                            <button type="button" v-if="tipoAccion_diasfestivos==1" class="btn btn-secondary" @click="RegistrarDiasFestivos(true)">
                                <i class="fas fa-save"></i>&nbsp;Guardar
                            </button>
                            <button type="button" v-if="tipoAccion_diasfestivos==2" class="btn btn-secondary" @click="RegistrarDiasFestivos(false)">
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
    <!--Fin del modal DiasFestivos-->

</main>
</template>

<script>
var config = require('../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            //// DiasFestivos
            url_diasfestivos: "rh/diasfestivos",
            tipoAccion_diasfestivos: 1,
            tipoCardDiasFestivos: 1,
            ver_modal_diasfestivos: false,
            tituloModal_diasfestivos: "",
            diasfestivos_id: 0,
            isGuardardiasfestivos_loading: false,
            isObtenerdiasfestivos_loading: false,
            columns_diasfestivos: [
                "id",
                "dia",
                "descripcion"
            ],
            list_diasfestivos: [],
            diasfestivos:
            {},
            options_diasfestivos:
            {
                headings:
                {
                    id: "Acciones",
                    dia: "Día",
                    descripcion: "Descripción"

                },
                perPage: 20,
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
        ObtenerDiasFestivos()
        {
            this.isObtenerdiasfestivos_loading = true;
            axios.get(this.url_diasfestivos + "/obtener").then(res =>
            {
                this.isObtenerdiasfestivos_loading = false;
                if (res.data.status)
                {
                    this.list_diasfestivos = res.data.diasfestivos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Abrir modal DiasFestivos
         */
        AbrirModalDiasFestivos(nuevo, data = {})
        {
            this.ver_modal_diasfestivos = true;
            if (nuevo)
            {
                this.tituloModal_diasfestivos = "Registrar Días festivos";
                this.tipoAccion_diasfestivos = 1;
            }
            else
            {
                this.tituloModal_diasfestivos = "Actualizar Días festivos";
                this.tipoAccion_diasfestivos = 2;
                this.asd = {
                    ...data
                };
            }
        },

        /**
         * Registrar DiasFestivos
         */
        RegistrarDiasFestivos(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.diasfestivos_id);
                data.append("dia", this.diasfestivos.dia);
                data.append("descripcion", this.diasfestivos.descripcion);

                this.isGuardardiasfestivos_loading = true;
                axios.post(this.url_diasfestivos + "/guardar", data).then(res =>
                {
                    this.isGuardardiasfestivos_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.ObtenerDiasFestivos();
                        this.CerrarModalDiasFestivos();
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
        CerrarModalDiasFestivos()
        {
            this.ver_modal_diasfestivos = false;
        },

    },
    mounted()
    {
        this.ObtenerDiasFestivos();
    }
}
</script>
