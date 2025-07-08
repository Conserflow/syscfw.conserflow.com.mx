<template>
<div class="container">
    <div class="container">
        <div class="form-row">
            <label class="col-md-2" for="">Código</label>
            <input type="text" v-model="codigo" class="form-control col col-md-4 col-sm-10">
            <button class="btn btn-dark ml-2" @click="BuscarCodigo">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <div class="form-row mt-2">
            <label class="col-md-2" for="">Descripción</label>
            <input type="text" v-model="descripcion" class="form-control col col-md-4 col-sm-10">
            <button class="btn btn-dark ml-2" @click="BuscarDescripcion">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <div id="barcode" class="container" style="-moz-transform: scaleX(-1);
                        -webkit-transform: scaleX(-1);
                        -o-transform: scaleX(-1);
                        transform: scaleX(-1);">
            <div class="col-md-6 col-sm-12 col-lg-6 mb-3">
                <template v-if="false">
                    <bar-code @decode="onDecode" @loaded="onLoaded"></bar-code>
                </template>
            </div>
        </div>

        <div class="form-row">
            <label for="">Proyecto</label>
            <input type="text" class="form-control" disabled v-model="articulo.nombre_proyecto" />
        </div>

        <div class="form-row">
            <label for="">Artículo</label>
            <textarea rows="4" class="form-control" disabled v-model="articulo.articulo">
            </textarea>
        </div>

        <div class="form-row">
            <div class="col-md-6">
                <label for="">Cantidad Real </label>
                <input type="number" step="0.1" v-model="articulo.existencia_real" class="form-control" />
            </div>
        </div>

        <div class="form-row">
            <div class="col-md-6">
                <label for="">Observaciones </label>
                <textarea rows="4" type="text" v-model="articulo.observaciones" class="form-control" />
                </div>
        </div>

        <br>
        <button class="btn btn-dark" @click="Guardar">
            <i class="fas fa-save mr-1"></i> Guardar
        </button>
    </div>
    <br>
    <br>
    <hr />
    <br>
    <br>
    <div class="">
        <div class="form-row">
            <label for="" class="col-sm-2">Proyecto</label>
            <v-select class="col-sm-6" label="nombre_corto" :options="list_proyectos" v-model="proyecto"></v-select>
            <button class="btn btn-dark" @click="BuscarInventario">
                <i class="fas fa-search"></i>
            </button>
            <button class="btn btn-success" @click="GenerarReporte">
                <i class="fas fa-file-excel mr-1"></i> Reporte General
            </button>
        </div>
        <br>
        <!-- Tabla de inventario-->
        <v-client-table :columns="columns_inventario" :data="list_inventario" :options="options_existencias"></v-client-table>
        <!--Card body -->
    </div>
    <!-- card-->
    <!-- Modal Existencias -->
    <div class='modal fade' tabindex='-1' :class="{'mostrar' : ver_modal_existencias}" role='dialog' aria-labelledby='myModalLabel' style='display: none;' aria-hidden='true'>
        <div class='modal-dialog modal-dark modal-lg' role='document'>
            <vue-element-loading :active="isGuardar_loading" />
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title'>Artículos encontrados</h4>
                    <button type='button' class='close' @click='CerrarModalExistencias()' aria-label='Close'>
                        <span aria-hidden='true'>×</span>
                    </button>
                </div>
                <div class='modal-body'>
                    <v-client-table @row-click="SeleccionarArticulo" :columns='columns_existencias' :data='list_existencias' :options='options_existencias' ref='tbl_propuesta'>
                    </v-client-table>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-outline-dark' @click='CerrarModalExistencias()'><i class='fas fa-times'></i>&nbsp;Cerrar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</div>
</template>

<script>
export default
{
    data()
    {
        return {
            isObtenerInvenrarioLoading: true,
            isGuardar_loading: false,
            list_existencias: [],
            url: "almacen/inventariogral",
            list_proyectos: [],
            codigo: "",
            columns_existencias: ["nombre_proyecto", "articulo"],
            options_existencias:
            {
                headings:
                {
                    "nombre_proyecto": "Proyecto",
                }, // Headings,
                perPage: 10,
                perPageValues: [],
                // skin: config.skin,
                // sortIcon: config.sortIcon,
                filterByColumn: true,
                // texts: config.texts
            }, //options 
            proyecto:
            {},
            articulo:
            {

            },
            ver_modal_existencias: false,
            list_inventario: [],
            columns_inventario: ["descripcion","existencia_sistema", "existencia_registrada"],
            options_inventario:
            {
                headings:
                {
                    "descripcion": "Artículo",
                    "existencia_registrada": "Existencia Fisica",
                }, // Headings,
                perPage: 10,
                perPageValues: [],
                // skin: config.skin,
                // sortIcon: config.sortIcon,
                filterByColumn: true,
                // texts: config.texts
            }, //options 
            descripcion: "",

        }

    },
    methods:
    {

        /**
         * Obtiene los registros del inventario
         */
        BuscarInventario()
        {
            if (this.proyecto.id == null) return;
            this.isObtenerInvenrarioLoading = true;
            axios.get(this.url + "/obtener/" + this.proyecto.id).then(res =>
            {
                this.isObtenerInvenrarioLoading = false;
                if (res.data.status)
                {
                    this.list_inventario = res.data.existencias;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Inicia el QR
         */
        async onLoaded(promise)
        {

            try
            {
                await promise;
                console.error(promise);
            }
            catch (error)
            {
                console.error(error);
                let errors = {
                    NotAllowedError: "Permita el acceso a la camara",
                    NotFoundError: "No existe camara en este dispositivo",
                    NotSupportedError: "No es suguro la activacion de la camara (HTTPS, localhost)",
                    NotReadableError: "La camra ya esta en uso",
                    OverconstrainedError: "camara no soportada",
                    StreamApiNotSupportedError: "Stream API no soportado para este navegador",
                };
                let ms = errors[error.name];
                if (ms == null) ms = error;
                toastr.error(ms);
            }
        },

        /**
         * Busca el códgo ingresado
         */
        BuscarCodigo()
        {
            if (this.codigo == null)
            {
                toastr.warning("Ingrese el código");
                return;
            }
            if (this.codigo.length <= 3)
            {
                toastr.warning("Ingrese un código válido");
                return;
            }

            axios.get(this.url + "/buscar/" + this.codigo).then(res =>
            {
                if (res.data.status)
                {
                    this.ver_modal_existencias = true; // mostrar mod
                    this.list_existencias = res.data.existencias;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            }).catch(x =>
            {
                toastr.error("Error. Reporte al administrador");
                console.error(x);
            });

        },

        /**
         * Cerrar modal de existencias
         */
        CerrarModalExistencias()
        {
            this.ver_modal_existencias = false;
        },
        /**
         * Lee el Código Ingresado y busca el artículo correspondiente
         */
        onDecode(codigo)
        {
            if (this.proyecto == null)
            {
                toastr.warning("Seleccione un proyecto");
                return;
            }

            if (this.proyecto.id == null)
            {
                toastr.warning("Seleccione un proyecto");
                return;
            }
            let data = this.proyecto.id + "&" + codigo;
            axios.get(this.url + "/buscar/" + data).then(res =>
            {
                if (res.data.status)
                {
                    this.articulo = res.data.articulo;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            }).catch(x =>
            {
                toastr.error("Error. Reporte al administrador");
                console.error(x);
            });
        },

        /**
         * Obtener todos los pryectos
         */
        ObtenerProyectos()
        {
            axios.get("calidad/inspecciones/obtenerproyectos").then(res =>
            {
                if (res.data.status)
                {
                    this.list_proyectos = res.data.proyectos;
                    this.list_proyectos.push(
                    {
                        id: 0,
                        nombre_corto: "TODOS LOS PROYECTOS"
                    });
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Selecciona el artículo
         */
        SeleccionarArticulo(row)
        {
            this.articulo = row.row
            this.articulo.existencia_real = 0.0;
            this.ver_modal_existencias = false;
        },

        /**
         * Guardar el registro de inventario
         */
        Guardar()
        {
            if (this.articulo.articulo_id == null)
            {
                toastr.warning("Seleccione un artículo");
                return;
            }
            if (this.articulo.existencia_real < 0)
            {
                toastr.error("Ingrese una cantidad real válida");
                return;
            }
            this.isGuardar_loading = true;
            let data = new FormData();
            data.append("articulo_id", this.articulo.articulo_id);
            data.append("proyecto_id", this.articulo.proyecto_id);
            data.append("existecia_sistema", this.articulo.existencia_sistema);
            data.append("existencia_real", this.articulo.existencia_real);
            data.append("observaciones", this.articulo.observaciones);
            axios.post(this.url + "/guardarinv", data).then(res =>
            {
                this.isGuardar_loading = false;
                if (res.data.status)
                {
                    toastr.success("Guardado correctamente");
                    this.articulo = {};
                    this.codigo = "MCF ";
                    this.BuscarInventario();
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Buscar los artículos por descripción
         */
        BuscarDescripcion()
        {
            if (this.descripcion.length <= 3)
            {
                toastr.warning("Ingrese un artículo");
                return;
            }
            axios.get(this.url + "/buscarpordesc/" + this.descripcion).then(res =>
            {
                if (res.data.status)
                {
                    this.ver_modal_existencias = true;
                    this.list_existencias = res.data.existencias;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Generar report de excel
         */
        GenerarReporte()
        {
            // Buscar por proyecto
            window.open(this.url + "/reporte/0", "_blank");
        }

    },
    mounted()
    {
        this.ObtenerProyectos();
    }
}
</script>
