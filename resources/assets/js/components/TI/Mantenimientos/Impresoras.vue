<template>
<div class="main">
    <!-- Card Inicio-->
    <div class="card">
        <!-- Inicio card-->
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Mantenimiento - Impresoras
        </div>
        <div class="card-body">
            <vue-element-loading :active="isObtener_Loading" />
            <div class="">
                <!-- Tabla de Impresora-->
                <div class="">
                    <v-client-table :columns="columns_impresora" :data="list_impresora" :options="options_impresora" ref="tbl_impresora">
                        <template slot="id" slot-scope="props">
                            <div class="btn-group" role="group">
                                <button id="btn_id" type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-grip-horizontal"></i>&nbsp; Acciones
                                </button>
                                <div class="dropdown-menu">
                                    <template>
                                        <button v-if="props.row.condicion==1" type="button" @click="AbrirModalImpresora(false,props.row)" class="dropdown-item">
                                            <i class="fas fa-plus mr-1"></i>Registrar
                                        </button>
                                        <button type="button" @click="Historial(props.row.id)" class="dropdown-item">
                                            <i class="fas fa-list mr-1"></i>Historial
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template slot="condicion" slot-scope="props">
                            <button v-if="props.row.condicion==1" class="btn btn-outline-success">Activo</button>
                            <button v-if="props.row.condicion==0" class="btn btn-outline-danger">Inactivo</button>
                            <button v-if="props.row.condicion==2" class="btn btn-outline-primary">En resguardo</button>
                            <button v-if="props.row.condicion == 3" class="btn btn-outline-primary">Sito</button>
                        </template>
                    </v-client-table>
                </div>
                <!--Card body -->
            </div> <!-- card-->
        </div>
    </div>

    <!-- Modal Impresora -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_impresora}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="titulo_modal_impresora"></h4>
                    <button type="button" class="close" @click="CerrarModalImpresora()" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method='post' class='form-horizontal'>
                        <div class='form-group row'>
                            <label class='col-md-3 form-control-label'>Ubicación</label>
                            <div class='col-md-4'>
                                <input type='text' class='form-control' v-validate="'required'" v-model="impresora_modal.ubicacion" data-vv-name="Ubicación">
                                <span class="text-danger">{{errors.first('Ubicación')}}</span>
                            </div>
                        </div>

                        <div class='form-group row'>
                            <label class='col-md-3 form-control-label'>Total Hojas</label>
                            <div class='col-md-3'>
                                <input type="number" class='form-control' v-validate="'required'" v-model='impresora_modal.total_hojas' data-vv-name="Total Hojas">
                                <span class="text-danger">{{errors.first('Total Hojas')}}</span>
                            </div>
                        </div>

                        <div class='form-group row'>
                            <label class='col-md-3 form-control-label'>Fecha</label>
                            <div class='col-md-3'>
                                <input type="date" class='form-control' v-validate="'required'" v-model='impresora_modal.fecha' data-vv-name="Fecha">
                                <span class="text-danger">{{errors.first('Fecha')}}</span>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class='col-md-3 form-control-label mt'>Uso de tinta</label>
                            <div class="col-md-2 mb-3">
                                <label>Cyan</label>
                                <select class="form-control" v-model='impresora_modal.c'>
                                    <option value="1/2">1/2</option>
                                    <option value="1">1</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label>Magenta</label>
                                <select class="form-control" v-model='impresora_modal.m'>
                                    <option value="1/2">1/2</option>
                                    <option value="1">1</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label>Yellow</label>
                                <select class="form-control" v-model='impresora_modal.y'>
                                    <option value="1/2">1/2</option>
                                    <option value="1">1</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label>Black</label>
                                <select class="form-control" v-model='impresora_modal.k'>
                                    <option value="1/2">1/2</option>
                                    <option value="1">1</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                        </div>

                        <div class='form-group row'>
                            <label class='col-md-3 form-control-label'>Observaciones</label>
                            <div class='col-md-8'>
                                <textarea rows="4" class='form-control' v-validate="'required'" v-model='impresora_modal.observaciones' data-vv-name="observaciones"></textarea>
                                <span class="text-danger">{{errors.first('observaciones')}}</span>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <vue-element-loading :active="isGuardar_Loading" />
                    <div>
                        <button type="button" class="btn btn-outline-dark" @click="CerrarModalImpresora"><i class="fas fa-times mr-1"></i>Cerrar</button>
                        <button type="button" v-if="tipoAccion_modal_impresora== 1" class="btn btn-secondary" @click="GuardarMtto"><i class="fas fa-save mr-1"></i>Guardar</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <!-- Modal Impresora -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_historial}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dark modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Historial de Mantenimiento</h4>
                    <button type="button" class="close" @click="CerrarModalImpresora()" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm table-striped">
                        <tr>
                            <th>Fecha</th>
                            <th>ubicacion</th>
                            <th>Total Hojas</th>
                            <th>C</th>
                            <th>M</th>
                            <th>Y</th>
                            <th>K</th>
                            <th>Observaciones</th>
                        </tr>
                        <tr :key="h.id" v-for="h in historial_mttos">
                            <td>{{h.fecha}}</td>
                            <td>{{h.ubicacion}}</td>
                            <td>{{h.total_hojas}}</td>
                            <td>{{h.c}}</td>
                            <td>{{h.m}}</td>
                            <td>{{h.y}}</td>
                            <td>{{h.k}}</td>
                            <td>{{h.observaciones}}</td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <div>
                        <button type="button" class="btn btn-outline-dark" @click="CerrarModalHistorial">
                            <i class="fas fa-times mr-1"></i>Cerrar
                        </button>
                    </div>
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
var config = require("../../Herramientas/config-vuetables-client").call(this);
export default
{
    data()
    {
        return {
            url: "ti/mtto/impresoras",
            // Tabla
            ver_modal_impresora: 0,
            columns_impresora: ["id", "descripcion", "marca_modelo", "no_serie", "ultimo_mtto", "condicion"],
            list_impresora: [],
            isObtener_Loading: false,
            isGuardar_Loading: false,
            options_impresora:
            {
                headings:
                {
                    id: "Acciones",
                    descripcion: "Descripción",
                    no_serie: "No. Serie",
                    marca_modelo: "Marca/Modelo",
                    ultimo_mtto: "Ult. Mtto.",
                    condicion: "Estado"
                }, // Headings,
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                listColumns:
                {
                    "condicion": [
                    {
                        id: 0,
                        text: "Inactivo"
                    },
                    {
                        id: 1,
                        text: "Activo"
                    },
                    {
                        id: 2,
                        text: "En resguardo"
                    },
                    {
                        id: 3,
                        text: "En sitio"
                    }, ],
                },
                texts: config.texts
            }, //options
            // Modal
            titulo_modal_impresora: "",
            tipoAccion_modal_impresora: 0,
            impresora_modal:
            {
                c: "NO",
                m: "NO",
                y: "NO",
                k: "NO",
            },
            // Historial
            ver_modal_historial: false,
            isHistorial_Loading: false,
            historial_mttos: []
        } // return
    }, //data
    computed:
    {},
    methods:
    {
        CargarImpresoras()
        {
            this.isObtener_Loading = true;
            axios.get(this.url + "/obtener").then(res =>
            {
                this.isObtener_Loading = false;
                if (res.data.status)
                {
                    this.list_impresora = res.data.impresoras;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        AbrirModalImpresora(nuevo, model = [])
        {
            this.ver_modal_impresora = true;

            this.impresora_modal = {
                impresora_id: model.id,
                c: "NO",
                m: "NO",
                y: "NO",
                k: "NO",
            };
            this.titulo_modal_impresora = "Registrar Mantenimiento de impresora";
            this.tipoAccion_modal_impresora = 1;
        },

        CerrarModalImpresora()
        {
            this.ver_modal_impresora = false;
        },

        async GuardarMtto()
        {
            let isValid = await this.$validator.validate();
            if (!isValid) return;
            axios.post(this.url + "/guardar",
            {
                "impresora_id": this.impresora_modal.impresora_id,
                "ubicacion": this.impresora_modal.ubicacion,
                "total_hojas": this.impresora_modal.total_hojas,
                "fecha": this.impresora_modal.fecha,
                "c": this.impresora_modal.c,
                "m": this.impresora_modal.m,
                "y": this.impresora_modal.y,
                "k": this.impresora_modal.k,
                "observaciones": this.impresora_modal.observaciones,
            }).then(res =>
            {
                if (res.data.status)
                {
                    this.CargarImpresoras();
                    this.CerrarModalImpresora();
                    toastr.success("Guardado correctamente");
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        Historial(id)
        {
            this.isHistorial_Loading = true;
            this.ver_modal_historial = true;
            axios.get(this.url + "/historial/" + id).then(res =>
            {
                this.isHistorial_Loading = false;
                if (res.data.status)
                {
                    this.historial_mttos = res.data.historial;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        CerrarModalHistorial()
        {
            this.ver_modal_historial = false;
            this.historial_mttos = [];
        },

    }, // Fin metodos
    mounted()
    {
        this.CargarImpresoras();

    }
}
</script>
