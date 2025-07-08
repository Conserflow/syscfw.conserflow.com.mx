<template>
<main class="main">
    <div class="card" style="min-height:90vh">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Seguridad Salud y Medio Ambiente
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <vue-element-loading :active="isObtenerFiles_loading" />
                    <ul id="directory">
                        <li :key="i" v-for="(f,i) in files">
                            <div class="card folder" @click="AbrirDocumento(f)" data-toggle="tooltip" data-placement="top" :title="f.full_name" style="margin-bottom:8px">
                                <i :class="ObtenerIcono(f.type)"></i>
                                <span class="description">{{f.name}}</span>
                            </div>
                        </li>
                    </ul>
                    <br>
                    <hr class="mx-5">
                    <br>
                    <p class="h3 text-center text-blue font-weight-bold">DIRECTORIO DE EMERGENCIAS</p>
                    <br>
                    <div class="container px-5">
                        <table class="text-center table table-bordered table-dir" width="80%">
                            <tr>
                                <td class="table-dir_header">DEPENDENCIA</td>
                                <td class="table-dir_header">TELÉFONO</td>
                            </tr>
                            <tr v-for="(t,i) in telefonos" :key="i">
                                <td>{{t.nombre}}</td>
                                <td>{{t.tel}}</td>
                            </tr>
                        </table>
                        <br>
                        <p class="text-right">Código: DI-CSF-009 Emisión: 29.JUL.23</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!--Inicio del modal-->
    <div v-if="ver_modal_documento" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_documento}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog2 modal-dark " role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <p class="modal-title h4 text-center" style="overflow:scroll">{{pdf_nombre}}</p>
                        <button type="button" class="close text-white" @click="CerrarModalDocumento()" aria-label="Close">
                            <span style="color:white" aria-hidden="true">X</span>
                        </button>
                    </div>
                    <div class="modal-body modal-body2" style="height:80vh">
                        <template v-if="type=='xlsx'">
                            <iframe :src="dir_pdf2" frameborder="1" width="90%" height="100%"> </iframe>
                        </template>
                        <template v-else>
                            <embed style="width:100%;height:100vh" :src="dir_pdf2" />
                        </template>

                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal -->
</main>
</template>

<style>
/* dir */
#directory {
    list-style-type: none;
}

.folder {
    cursor: pointer;
    height: 3.5rem;
    font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif;
    font-size: 13px;
    border-radius: 5px;
}

.icon {
    position: absolute;
    top: 1rem;
    color: #5f6368;
    left: 1rem;
    font-size: 1.5rem;

}

.description {
    position: absolute;
    top: 1.3rem;
    left: 3rem;
}

/* bread */
.link-historial {
    cursor: pointer;
}

.link-historial:hover {
    text-decoration: underline !important;
}

.modal-dialog2 {
    max-width: 90%;
}

.modal-body2 {
    padding: 0px;
}

.text-blue {
    color: #4270c4;
}

.table-dir tr {
    line-height: .5rem;
}

.table-dir_header
{
    font-weight: bold;
    color: white;
    background-color: #0070c0;
}
</style>

<script>
export default
{
    data()
    {
        return {
            url: "sgi/procedimientos/",
            files: [],
            isObtenerFiles_loading: false,
            historial: [],
            ver_pdf: false,
            pdf_nombre: "",
            // Modal Documento
            ver_modal_documento: false,
            tituloModal: "",
            dir_pdf2: "",
            type: "",
            telefonos: [],
        };
    },
    methods:
    {
        /**
         * Obtener todos los archivos y directorios de la ruta ingresada
         */
        ObtenerArchivos(file)
        {
            let path = file.path;
            let name = file.full_name;
            this.historial.push(name);
            this.isObtenerFiles_loading = true;
            axios.post(this.url + "directorios",
            {
                path
            }).then((res) =>
            {
                this.isObtenerFiles_loading = false;
                if (res.data.status)
                {
                    this.files = res.data.files;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Mostrar el Documento seleccionado
         */
        AbrirDocumento(file)
        {
            this.type = file.type;
            this.ver_pdf = true;
            this.dir_pdf2 = "docs/SGI/" + file.path+"#toolbar=0&navpanes=0&scrollbar=0";
            this.pdf_nombre = file.full_name.replace("." + file.type, ""); // Quitar extension
            this.ver_modal_documento = true;
            if (this.type == "xlsx")
            {
                let url = "https://syscfw.conserflow.com.mx/" + this.dir_pdf2;
                this.dir_pdf2 = "https://view.officeapps.live.com/op/embed.aspx?src=" + url;
            }
        },

        /**
         * Cerrar modal para ver documento
         */
        CerrarModalDocumento()
        {
            this.ver_modal_documento = false;
        },

        /**
         * Obtener el icono del documento
         */
        ObtenerIcono(type)
        {
            let asd = {
                pdf: "fas fa-file-pdf text-danger icon",
                docx: "fas fa-file-word text-primary icon",
                xlsx: "fas fa-file-excel text-success icon",
                jpg: "fas fa-image text-info icon",
            }
            return asd[type];
        },

        /**
         * Crear telefonos del directorio
         */
        CrearDirectorioTel()
        {
            this.telefonos = [
            {
                nombre: "Atención A Emergencias Nacional",
                tel: "911"
            },
            {
                nombre: "Dirección General De Protección Civil Del Estado",
                tel: "222 246 27 50"
            },
            {
                nombre: "Cruz Roja Tehuacán",
                tel: "238 382 0026"
            },
            {
                nombre: "Bomberos Tehuacán",
                tel: "238 383 2225"
            },
            {
                nombre: "Policía Municipal Tehuacán",
                tel: "238 382 0116"
            },
            {
                nombre: "Tránsito Municipal Tehuacán",
                tel: "238 382 1196"
            },
            {
                nombre: "Protección Civil Tehuacán",
                tel: "238 383 2225"
            },
            {
                nombre: "Hospital General Tehuacán",
                tel: "238 382 6535"
            },
            {
                nombre: "Dirección De Seguridad Pública Tehuacán",
                tel: "238 3711277"
            },
            {
                nombre: "Presidencia Municipal Santiago Miahuatlán",
                tel: "238 371 0601"
            },
            {
                nombre: "Centro De Orientación Para Atención De Emergencias Ambientales (COATEA)",
                tel: "55 54 49 63 00 Ext. 16129"
            },
            {
                nombre: "Secretaría de Medio Ambiente y Recursos Naturales (SEMARNAT)",
                tel: "Conmutador 55 54 90 09 00 (29518, 29527)"
            },
            {
                nombre: "Secretaría de medio ambiente, desarrollo sustentable y ordenamiento territorial (SMADSOT)",
                tel: "222 273 68 00"
            }, ];
        },
    },
    mounted()
    {
        // docx <i class="fas fa-file-word"></i>
        // xlsx <i class="fas fa-file-excel"></i>
        this.ObtenerArchivos(
        {
            path: "Seguridad", // path en el storage
            name: "Seguridad", // Nombre a mostrar
            full_name: "Seguridad", // Path del public
        });
        this.CrearDirectorioTel();
    },
};
</script>
