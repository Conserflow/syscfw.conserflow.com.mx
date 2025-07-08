<template>
<main class="main">
    <!-- Listado Proveedor -->
    <div class="card" v-show="tipoCardProveedor==1">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> PROVEEDORES
            <button type="button" @click="AbrirModalProveedor(true)" class="btn btn-dark float-sm-right">
                <i class="fas fa-plus"></i>&nbsp;Nuevo
            </button>
        </div>
        <div class="card-body">
            <vue-element-loading :active="isObtenerproveedor_loading" />
            <v-client-table :columns="columns_proveedor" :data="list_proveedor" :options="options_proveedor">
                <template slot="id" slot-scope="props">
                    <div class="btn-group" role="group">
                        <div class="btn-group dropup" role="group">
                            <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-grip-horizontal"></i> Acciones
                            </button>
                            <div class="dropdown-menu">
                                <button type="button" class="dropdown-item" @click="AbrirModalProveedor(false,props.row)">
                                    <i class="fas fa-edit"></i> Actualizar
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </v-client-table>
        </div>
    </div>

    <!--Inicio del modal Proveedor-->
    <div v-if="ver_modal_proveedor" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_proveedor}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal_proveedor"></h4>
                        <button type="button" class="close" @click="CerrarModalProveedor()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <vue-element-loading :active="isGuardarproveedor_loading" />
                        <div>
                            <!-- Formulario -->
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Nombre comercial</label>
                                <div class="col-md-6">
                                    <input type="text" maxlength="150" minlength="5" v-validate="'required'" v-model="proveedor.nombre" class="form-control" data-vv-name="Nombre comercial" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Nombre comercial') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Razón social</label>
                                <div class="col-md-9">
                                    <input type="text" maxlength="150" minlength="5" v-validate="'required'" v-model="proveedor.razon_social" class="form-control" data-vv-name="Razón social" autocomplete="off" />
                                    <span class="text-danger">{{ errors.first('Razón social') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">RFC</label>
                                <div class="col-md-3">
                                    <input @input="ValidarRFC()" type="text" maxlength="13" minlength="12" v-validate="'required'" v-model="proveedor.rfc" class="form-control" data-vv-name="RFC" autocomplete="off" />
                                    <span v-show="!rfc_valido" class="text-danger">El RFC no es válido</span>
                                </div>
                            </div>
                            <!-- Formulario -->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <vue-element-loading :active="isGuardarproveedor_loading" />
                        <div>
                            <button type="button" class="btn btn-outline-dark" @click="CerrarModalProveedor()">
                                <i class="fas fa-window-close"></i>&nbsp;Cerrar
                            </button>
                            <button type="button" v-if="tipoAccion_proveedor==1" class="btn btn-secondary" @click="RegistrarProveedor(true)">
                                <i class="fas fa-save"></i>&nbsp;Guardar
                            </button>
                            <button type="button" v-if="tipoAccion_proveedor==2" class="btn btn-secondary" @click="RegistrarProveedor(false)">
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
    <!--Fin del modal Proveedor-->

</main>
</template>

<script>
var config = require('../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            //// Proveedor
            url_proveedor: "vehiculos/proveedor",
            tipoAccion_proveedor: 1,
            tipoCardProveedor: 1,
            ver_modal_proveedor: false,
            tituloModal_proveedor: "",
            proveedor_id: 0,
            isGuardarproveedor_loading: false,
            isObtenerproveedor_loading: false,
            rfc_valido: false,
            columns_proveedor: [
                "id",
                "nombre",
                "razon_social",
                "rfc"
            ],
            list_proveedor: [],
            proveedor:
            {},
            options_proveedor:
            {
                headings:
                {
                    id: "Acciones",
                    nombre: "Nombre comercial",
                    razon_social: "Razón social",
                    rfc: "RFC",

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
        ObtenerProveedor()
        {
            this.isObtenerproveedor_loading = true;
            axios.get(this.url_proveedor + "/obtener").then(res =>
            {
                this.isObtenerproveedor_loading = false;
                if (res.data.status)
                {
                    this.list_proveedor = res.data.proveedores;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Abrir modal Proveedor
         */
        AbrirModalProveedor(nuevo, data = {})
        {
            this.ver_modal_proveedor = true;
            if (nuevo)
            {
                this.tituloModal_proveedor = "Registrar Proveedores";
                this.tipoAccion_proveedor = 1;
            }
            else
            {
                this.tituloModal_proveedor = "Actualizar Proveedores";
                this.tipoAccion_proveedor = 2;
                this.proveedor_id = data.id;
                this.proveedor = {
                    ...data
                };
            }
        },

        /**
         * Registrar Proveedor
         */
        RegistrarProveedor(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;
                if (!this.rfc_valido) return;
                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.proveedor_id);
                data.append("nombre", this.proveedor.nombre);
                data.append("razon_social", this.proveedor.razon_social);
                data.append("rfc", this.proveedor.rfc);

                this.isGuardarproveedor_loading = true;
                axios.post(this.url_proveedor + "/guardar", data).then(res =>
                {
                    this.isGuardarproveedor_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Guardado correctamente");
                        this.ObtenerProveedor();
                        this.CerrarModalProveedor();
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
        CerrarModalProveedor()
        {
            this.ver_modal_proveedor = false;
            this.proveedor = {};
        },

        ValidarRFC()
        {
            this.rfc_valido = this.ValidarRFC_aux();
        },
        /**
         * Validar RFC
         */
        ValidarRFC_aux()
        {
            let rfc = this.proveedor.rfc;
            const aceptarGenerico = true;
            const re = /^([A-ZÑ&]{3,4}) ?(?:- ?)?(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])) ?(?:- ?)?([A-Z\d]{2})([A\d])$/;
            var validado = rfc.match(re);

            if (!validado) //Coincide con el formato general del regex?
                return false;

            //Separar el dígito verificador del resto del RFC
            const digitoVerificador = validado.pop(),
                rfcSinDigito = validado.slice(1).join(''),
                len = rfcSinDigito.length,

                //Obtener el digito esperado
                diccionario = "0123456789ABCDEFGHIJKLMN&OPQRSTUVWXYZ Ñ",
                indice = len + 1;
            var suma,
                digitoEsperado;

            if (len == 12) suma = 0
            else suma = 481; //Ajuste para persona moral

            for (var i = 0; i < len; i++)
                suma += diccionario.indexOf(rfcSinDigito.charAt(i)) * (indice - i);
            digitoEsperado = 11 - suma % 11;
            if (digitoEsperado == 11) digitoEsperado = 0;
            else if (digitoEsperado == 10) digitoEsperado = "A";

            //El dígito verificador coincide con el esperado?
            // o es un RFC Genérico (ventas a público general)?
            if ((digitoVerificador != digitoEsperado) &&
                (!aceptarGenerico || rfcSinDigito + digitoVerificador != "XAXX010101000"))
                return false;
            else if (!aceptarGenerico && rfcSinDigito + digitoVerificador == "XEXX010101000")
                return false;
            return true;
        }

    },
    mounted()
    {
        this.ObtenerProveedor();
    }
}
</script>
