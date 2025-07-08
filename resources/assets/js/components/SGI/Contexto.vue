<template>
<main class="main">
    <div class="card" style="min-height:90vh">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Contexto de la Organización y Partes Interesadas
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <vue-element-loading :active="isObtenerFiles_loading" />
                    <ul>
                        <li :key="i" v-for="(f,i) in files">
                            <p class="doc" @click="AbrirDocumento(f)">
                                {{f.name.replace("."+f.type,"")}}
                            </p>
                        </li>
                    </ul>
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
    <!--Fin del modal DiasFestivos-->
</main>
</template>

<style>
.doc {
    font-size: 16px;
    text-transform: uppercase;
}
.doc:hover
{
    cursor: pointer;
    text-decoration: underline;
    color: blue;
}

.modal-dialog2 {
    max-width: 90%;
}

.modal-body2 {
    padding: 0px;
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
        }
    },
    mounted()
    {
        // docx <i class="fas fa-file-word"></i>
        // xlsx <i class="fas fa-file-excel"></i>
        this.ObtenerArchivos(
        {
            path: "Contexto", // path en el storage
            name: "Contexto", // Nombre a mostrar
            full_name: "Contexto", // Path del public
        });
    },
};
</script>
