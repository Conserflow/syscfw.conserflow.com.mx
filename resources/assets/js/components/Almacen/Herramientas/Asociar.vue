<template>
<div>
    <div v-show="tipoAccion==1">
        <vue-element-loading :active="isObtenerConceptos_loading" />
        <v-server-table ref="myTable" :columns="columns_herramientas" url="almacen/herramientas/conceptosserver?query={}&limit=10&ascending=1&page=1&byColumn=1" :options="options_herramientas">
            <template slot="a__id" slot-scope="props">
                <button class="btn btn-outline-dark" @click="BuscarOC(props.row)">
                    <i class="fas fa-exchange-alt"></i>
                </button>
            </template>
        </v-server-table>
    </div>
    <template v-if="tipoAccion==2">
        <div class="">
            <span class="h5">Asociar Herramienta</span>
            <button class="btn btn-sm btn-secondary float-sm-right" @click="Regresar">
                <i class="fas fa-arrow-left mr-1"></i>Regresar
            </button>
        </div>
        <br>

        <br>
        <div class="col">
            <div class="form-group row">
                <label class="col-md-1 form-control-label">Artículo</label>
                <div class="col-md-8">
                    {{concepto.a__nombre}}
                </div>
            </div>
            <div class="form-group row" v-if="entrada.c_pendiente">
                <label class="col-md-1 form-control-label">Cantidad</label>
                <div class="col-md-8">
                    {{entrada.c_pendiente}}
                </div>
            </div>

            <div class="form-group row" v-show="!isSelected_entrada">
                <span class="font-italic">Seleccionar Entrada</span>
                <vue-element-loading :active="isObtenerPartidas_loading" />
                <table class="table table-sm">
                    <thead class="thead-dark">
                        <tr style="background-color:black;color:white;">
                            <th scope="col"></th>
                            <th class="text-center" scope="col">Entrada</th>
                            <th class="text-center" scope="col">Pendiente</th>
                            <th class="text-center" scope="col">Fecha</th>
                            <th class="text-center" scope="col">OC</th>
                            <th class="text-center" scope="col">Comentarios</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="entrada" :key="i" v-for="(e,i) in list_oc">
                            <td>
                                <button class="btn btn-outline-dark" @click="DescargarEntrada(e.id)">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                                <button class="btn btn-outline-dark" @click="SeleccionarEntrada(e)">
                                    <i class="fas fa-check"></i>
                                </button>
                            </td>
                            <td> {{e.formato_entrada}} </td>
                            <td> {{e.c_pendiente}} </td>
                            <td> {{e.e_fecha}} </td>
                            <td> {{e.folio}} </td>
                            <td> {{e.e_com}} </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <hr>
            <br>
            <vue-element-loading :active="isGuardar_loading" />
            <template v-if="isSelected_entrada">
                <div class="form-group row">
                    <label class="col-md-1 form-control-label">Entrada</label>
                    <div class="col-md-3">
                        <input type="text" class="form-control" v-model="entrada.formato_entrada" readonly disabled>
                    </div>
                    <label class="col-md-1 form-control-label">OC</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="entrada.folio" readonly disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-1 form-control-label">No. Serie</label>
                    <div class="col-md-3">
                        <input type="text" class="form-control" v-model="no_serie" data-vv-name="No. Serie">
                    </div>
                    <label class="col-md-1 form-control-label">Codigo Int.</label>
                    <div class="col-md-3">
                        <input type="text" class="form-control" v-model="codigo_interno" data-vv-name="Codigo Int.">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-1 form-control-label">Marca</label>
                    <div class="col-md-3">
                        <input type="text" class="form-control" v-model="marca" v-validate="'required'" data-vv-name="Marca">
                        <span class="text-danger">{{errors.first("Marca")}}</span>
                    </div>
                    <label class="col-md-1 form-control-label">Modelo</label>
                    <div class="col-md-3">
                        <input type="text" class="form-control" v-model="modelo" v-validate="'required'" data-vv-name="Modelo">
                        <span class="text-danger">{{errors.first("Modelo")}}</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-dark" @click="Guardar">
                        <i class="fas fa-save mr-1"></i>Guardar
                    </button>
                </div>
            </template>
        </div>
    </template>
</div>
</template>

<script>
var config = require('../../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            // Catalogo
            tipoAccion: 1,
            columns_herramientas: ["a__id", "codigo", "a__nombre", "a__descripcion", "a__marca", "um__nombre"],
            isObtenerConceptos_loading: false,
            list_herramientas: [],
            concepto:
            {},
            options_herramientas:
            {
                headings:
                {
                    a__id: "Acciones",
                    um__nombre: "UM",
                    a__descripcion: "Descripción",
                    a__nombre: "Nombre",
                    a__marca: "Marca",
                },
                perPage: 15,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            },
            // Buscar OC
            buscar_oc: "",
            isObtenerPartidas_loading: false,
            list_oc: [],
            no_serie: "",
            modelo: "",
            marca: "",
            isGuardar_loading: false,
            codigo_interno: "",
            entrada:
            {},
            isSelected_entrada: false,
            completo: true,
        }
    },
    methods:
    {

        /**
         * Cambiar a Busqueda de OC
         */
        BuscarOC(articulo)
        {
            this.tipoAccion = 2;
            this.concepto = articulo;
            this.BuscarPartidas();
        },

        Regresar()
        {
            this.tipoAccion = 1;
            this.marca = "";
            this.no_serie = "";
            this.codigo_interno = "";
        },

        /**
         * Buscar los conceptos de la OC ingresada
         */
        BuscarPartidas()
        {
            this.isSelected_entrada = false;
            this.isObtenerPartidas_loading = true;
            axios.get("almacen/herramientas/oc/obtenerpartidas/" + this.concepto.a__id).then(res =>
            {
                this.isObtenerPartidas_loading = false;
                if (res.data.status)
                {
                    this.list_oc = res.data.ocs;
                    if (this.list_oc.length == 0)
                        toastr.warning("Sin resultados");
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Seleccionar la entrada para guardar
         */
        SeleccionarEntrada(entrada)
        {
            if (entrada.c_pendiente <= 0)
            {
                toastr.warning("Partidas completas");
                return;
            }
            this.entrada = entrada;
            this.isSelected_entrada = true;
            this.completo = false; // No se ha completado
        },

        /**
         * Guardar datos de la herramienta
         */
        async Guardar()
        {
            if (this.completo)
            {
                toastr.warning("Partidas completas");
                return;
            }
            this.isGuardar_loading = true;
            let isValid = await this.$validator.validate();
            if (!isValid) return;
            let data = new FormData();
            data.append("entrada_id", this.entrada.id);
            data.append("articulo_id", this.concepto.a__id);
            data.append("a_id", this.concepto.a__id);
            data.append("no_serie", this.no_serie);
            data.append("marca", this.marca);
            data.append("modelo", this.modelo);
            data.append("codigo_interno", this.codigo_interno);
            axios.post("almacen/herramientas/guardar", data).then(res =>
            {
                this.isGuardar_loading = false;
                if (res.data.status)
                {
                    toastr.success("Guardado correctamente");
                    this.no_serie = "";
                    // this.marca = "";
                    // this.modelo = "";
                    this.codigo_interno = "";
                    // Actualizar cantidad
                    let cantidad = this.entrada.c_pendiente;
                    this.entrada.c_pendiente = cantidad - 1;
                    if (this.entrada.c_pendiente <= 0)
                    {
                        // Ya no hay mas
                        toastr.warning("Partidas completas");
                        this.completo = true; // Entrada terminada
                    }
                }
                else toastr.error(res.data.mensaje)
            })
        },

        /**
         * Descargar la entrada seleccionada
         */
        DescargarEntrada(id)
        {
            window.open(`descargar-entrada-nuevo-formato/${id}`);
        }
    },
    mounted()
    {}

}
</script>

<style scoped>

</style>
