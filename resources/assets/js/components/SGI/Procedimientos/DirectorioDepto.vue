<template>
<main>
    <div class="card-body1">
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
                    </ol>
                </nav>
                <vue-element-loading :active="isObtenerFiles_loading" />
                <ul id="directory">
                    <li :key="i" v-for="(f,i) in files">
                        <template v-if="f.file">
                            <div class="card folder" @click="AbrirPDF(f)">
                                <i class="fas fa-file-pdf text-danger icon"></i>
                                <span class="description">{{f.name}}</span>
                            </div>
                        </template>
                        <template v-else>
                            <div class="card folder" @click="ObtenerArchivos(f)">
                                <i class="fas fa-folder icon"></i>
                                <span class="description">{{f.name}}</span>
                            </div>
                        </template>

                    </li>
                </ul>
            </div>

            <div v-show="ver_pdf" class="col col-lg-6 col-md-12 col-sm-12">
                <button class="btn btn-sm btn-dark float-sm-right" @click="ver_pdf=false">
                    <i class="fas fa-times"></i>
                </button>
                <p class="h6 text-center">{{pdf_nombre}}</p>
                <br>
                <div>
                    <embed style="width:100%;height:80vh" :src="dir_pdf" />
                </div>
            </div>
        </div>
    </div>
</main>
</template>

<style>
/* dir */
#directory {
    list-style-type: none;
}

#directory li {
    width: 250px;
    float: left;
    margin-right: 1rem
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
</style>

<script>
const DirectorioDepto = r => require.ensure([], () => r(require('./DirectorioDepto.vue')), 'sgi');

export default
{
    props: ["path"],
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
        };
    },
    components:
    {
        "directorio-dpto": DirectorioDepto
    },
    methods:
    {
        /**
         * Obtener todos los archivos y directorios de la ruta ingresada
         */
        ObtenerArchivos(file)
        {
            let path = file.path;
            let name = file.name;
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
            let ruta = "Procedimientos/"; // Ruta inicial
            let aux_historial = [];
            // Obtener todas las rutas anteriores hasta llegar a la seleccionada
            for (let i = 0; i < index; i++)
            {
                ruta += this.historial[i] + "/";
                aux_historial.push(this.historial[i]);
            }
            ruta += this.historial[index]; // Agregar la seleccionada
            aux_historial.push(this.historial[index]); // Actualizar historial hasta la seleccionada
            // Obtener
            this.ObtenerArchivos(
            {
                path: ruta,
                name: this.historial[index]
            });
            this.historial = aux_historial;
        },

        /**
         * Mostrar el PDf seleccionado
         */
        AbrirPDF(file)
        {
            this.ver_pdf = true;
            this.dir_pdf = "docs/SGI/" + file.path + "#toolbar=0&navpanes=0&scrollbar=0";
            this.pdf_nombre = file.full_name;
            //docs/sgi/filosofia/contrato.pdf#toolbar=0&navpanes=0&scrollbar=0
        }
    },
    mounted()
    {
        this.ObtenerArchivos(
        {
            path: "Procedimientos/"+this.path, // Depto
            name: this.path
        });
    },
};
</script>
