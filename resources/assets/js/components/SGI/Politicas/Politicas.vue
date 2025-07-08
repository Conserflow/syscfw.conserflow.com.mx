<template>
<main class="main">
    <div class="card" style="min-height: 80vh;">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Políticas
        </div>
        <div class="card-body">
            <div class="row">
                <ul>
                    <li style="--accent-color:#0B374D" @click="abrirPolitica('puntualidad')">
                        <div class="title">
                            Política De Puntualidad, Asistencia y Permisos</div>
                        <div class="icon"> Entra Y Conoce Más <i class="fas fa-chevron-right"></i></div>
                    </li>
                    <li style="--accent-color:#1B516C" @click="abrirPolitica('vacaciones')">
                        <div class="title">Política De Vacaciones</div>
                        <div class="icon"> Entra Y Conoce Más <i class="fas fa-chevron-right"></i></div>
                    </li>
                    <li style="--accent-color:#2F6B89" @click="abrirPolitica('vestimenta')">
                        <div class="title">Política De Vestimenta</div>
                        <div class="icon"> Entra Y Conoce Más <i class="fas fa-chevron-right"></i></div>
                    </li>
                    <li style="--accent-color:#3D89AF" @click="abrirPolitica('papel')">
                        <div class="title">Política De Cero Papel</div>
                        <div class="icon"> Entra Y Conoce Más <i class="fas fa-chevron-right"></i></div>
                    </li>
                    <li style="--accent-color:#4BA3D0" @click="abrirPolitica('seguridad-informacion')">
                        <div class="title">Politica De Seguridad De La Información</div>
                        <div class="icon"> Entra Y Conoce Más <i class="fas fa-chevron-right"></i></div>
                    </li>
                    <li style="--accent-color:#5fadd5" @click="abrirPolitica('reembolsos-gastos')">
                        <div class="title">Politica de Reembolsos de gastos</div>
                        <div class="icon"> Entra Y Conoce Más <i class="fas fa-chevron-right"></i></div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!--Inicio del modal Documentos-->
    <div v-if="ver_modal_documento" class="modal fade" tabindex="-1" :class="{'mostrar' : ver_modal_documento}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog2 modal-dark" role="document">
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
    <!--Fin del modal Documentos-->
</main>
</template>

<style scoped>
.modal-dialog2 {
    max-width: 90%;
}

.title {
    text-transform: uppercase;
}

.icon {
    text-transform: uppercase;
}

ul {
    --col-gap: 1rem;
    --flapH: 5rem;
    margin-inline: auto;
    display: flex;
    flex-wrap: wrap;
    gap: var(--col-gap);
    padding-inline: calc(var(--col-gap) / 2);
    justify-content: center;
    align-items: flex-start;
    list-style: none;
}

ul li {
    width: 15rem;
    display: grid;
    grid-template:
        "role"
        "icon"
        "title"
        "descr";
    cursor: pointer;
    align-items: flex-start;
    gap: 1rem;
    padding-block-end: calc(var(--flapH) + 1rem);
    text-align: center;
    background: var(--accent-color);
    background-image: linear-gradient(rgba(0, 0, 0, 0.6) var(--roleH),
            rgba(0, 0, 0, 0.4) calc(var(--roleH) + 0.5rem),
            rgba(0, 0, 0, 0) calc(var(--roleH) + 0.5rem + 5rem));
    clip-path: polygon(calc(var(--col-gap) / -2 - 5px) 0,
            calc(100% + var(--col-gap) / 2 + 5px) 0,
            calc(100% + var(--col-gap) / 2 + 5px) calc(100% - var(--flapH)),
            50% 100%,
            calc(var(--col-gap) / -2 - 5px) calc(100% - var(--flapH)));
}

ul li .icon,
ul li .title,
ul li .descr {
    padding-inline: 1rem;
    color: white;
    text-shadow: 0 0 0.5rem rgba(0, 0, 0, 0.5);
}

ul li .title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-top: 2rem;
}

ul li:hover {
    transform: scale(1.1);
    transition: .5s;
}

ul li .descr {
    font-size: 0.9rem;
}
</style>

<script>
export default
{
    data()
    {
        return {
            ver_modal_documento: false,
            pdf_nombre: "",
            dir_pdf2: "",
        }
    },
    methods:
    {
        /**
         * Mostrar el PDf seleccionado
         */
        abrirPolitica(file)
        {
            const nombres = {
                puntualidad: "POLÍTICA DE PUNTUALIDAD, ASISTENCIA Y PERMISOS",
                vacaciones: "POLÍTICA DE VACACIONES",
                vestimenta: "POLÍTICA DE VESTIMENTA",
                papel: "POLÍTICA DE CERO PAPEL",
                "seguridad-informacion": "POLÍTICA DE SEGURIDAD DE LA INFORMACIÓN",
                "reembolsos-gastos":"POLITICA DE REEMBOLSOS DE GASTOS"
            };
            this.type = file.type;
            this.ver_pdf = true;
            this.dir_pdf2 = "docs/SGI/Politicas/" + file + ".pdf#toolbar=0&navpanes=0&scrollbar=0";
            this.pdf_nombre = nombres[file];
            this.ver_modal_documento = true;
        },
        CerrarModalDocumento()
        {
            this.ver_modal_documento = false;
        }
    }
}
</script>
