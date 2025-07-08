<template>
<main class="main">
    <div class="card" style="min-height:90vh">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Normativas
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <template v-for="(h,i) in historial">
                                <li class="breadcrumb-item" :key="i">
                                    <template v-if="i==(historial.length-1)">
                                        <a class="font-weight-bold text-gray">{{h}}</a>
                                    </template>
                                    <template v-else>
                                        <a class="text-primary font-weight-bold link-historial" @click="Cambiar(i)">{{h}}</a>
                                    </template>
                                </li>
                            </template>
                            <button v-show="historial.length>1" class="btn btn-dark btn-sm float-sm-right" @click="Cambiar(historial.length-2)">
                                <i class="fas fa-arrow-left"></i>
                            </button>
                        </ol>
                    </nav>
                    <vue-element-loading :active="isObtenerFiles_loading" />
                    <ul id="directory">
                        <li :key="i" v-for="(f,i) in files">
                            <template v-if="f.file">
                                <template v-if="f.type=='pdf'">
                                    <div class="card folder" @click="AbrirPDF(f)" data-toggle="tooltip" data-placement="top" :title="f.full_name">
                                        <i class="fas fa-file-pdf text-danger icon"></i>
                                        <span class="description">{{f.name}}</span>
                                    </div>
                                </template>

                                <template v-else-if="f.type=='docx'">
                                    <div @click="AbrirPDF(f)" class="card folder" data-toggle="tooltip" data-placement="top" :title="f.full_name">
                                        <i class="fas fa-file-word text-primary icon"></i>
                                        <span class="description">{{f.name}}</span>
                                    </div>
                                </template>
                                <template v-else-if="f.type=='xlsx'">
                                    <div @click="AbrirPDF(f)" class="card folder" data-toggle="tooltip" data-placement="top" :title="f.full_name">
                                        <i class="fas fa-file-excel text-success icon"></i>
                                        <span class="description">{{f.name}}</span>
                                    </div>
                                </template>
                            </template>
                            <template v-else>
                                <div class="card folder" @click="ObtenerArchivos(f)" data-toggle="tooltip" data-placement="top" :title="f.full_name">
                                    <i class="fas fa-folder icon"></i>
                                    <span class="description">{{f.name}}</span>
                                </div>
                            </template>

                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
    <!--Inicio del modal DiasFestivos-->
    <div v-if="ver_modal_documento" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_documento}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog2 modal-dark " role="document">
            <div class="modal-content">
                <div>
                    <div class="modal-header">
                        <p class="modal-title h4 text-center">{{pdf_nombre}}</p>
                        <button type="button" class="close text-white" @click="CerrarModalDocumento()" aria-label="Close">
                            <span style="color:white" aria-hidden="true">X</span>
                        </button>
                    </div>
                    <div class="modal-body modal-body2">
                        <embed style="width:100%;height:100vh" :src="dir_pdf2" />
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
/* dir */
#directory {
    list-style-type: none;
}

#directory li {
    /* width: 250px;
    float: left;
    margin-right: 1rem */
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
            dir_pdf: "",
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
         * Cambiar el directorio al index seleccionado
         */
        Cambiar(index)
        {
            let ruta = "";
            let aux_historial = [];
            for (let i = 0; i < index; i++)
            {
                ruta += this.historial[i] + "/";
                aux_historial.push(this.historial[i]);
            }
            ruta += this.historial[index];
            aux_historial.push(this.historial[index]);
            this.ver_pdf = false;
            this.ObtenerArchivos(
            {
                path: ruta,
                name: this.historial[index],
                full_name: this.historial[index], //?
            });
            this.historial = aux_historial;
        },

        /**
         * Mostrar el PDf seleccionado
         */
        AbrirPDF(file)
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
         * 
         */
        AbrirModal()
        {
            this.ver_modal_documento = true;
        },

        /**
         * Cerrar modal para ver documento
         */
        CerrarModalDocumento()
        {
            this.ver_modal_documento = false;
        }
    },
    mounted()
    {
        this.ObtenerArchivos(
        {
            path: "Normativas",
            name: "Normativas",
            full_name: "Normativas",
        });
    },
};
</script>
