<template>
<main class="main">
    <div class="card" style="min-height:90vh">
        <div class="card-header">
            <i class="fa fa-align-justify"></i>Certificaciones
        </div>
        <div class="card-body text-center mx-auto1">
            <div class="row">
                <div class="col">
                    <p class="mb-1 mt-2 h5"> <br> Sistemas de Gestión Ambiental</p>
                    <img class="border rounded px-2 my-2" @click="openModal('img/certificaciones/Certificado-Conserflow-ISO-14001.webp','Ambiental')" src="img/certificaciones/Certificado-Conserflow-ISO-14001.webp" alt="Certificado-Conserflow-ISO-14001" height="400">
                    <p>
                        Diseño, desarrollo, fabricación de equipos de medición, control regulación de
                        líquidos y gases, ensamble de gabinetes de control, para la industria de
                        hidrocarburos, desde la comercialización de materiales, componentes y refacciones,
                        hasta el desarrollo de proyectos integrales
                    </p>
                </div>
                <div class="col">
                    <p class="mb-1 mt-2 h5"><br>Sistemas de Gestión de la Calidad</p>
                    <img class="border rounded px-2 my-2" @click="openModal('img/certificaciones/Certificado-Conserflow-ISO-9001.webp','Calidad')" src="img/certificaciones/Certificado-Conserflow-ISO-9001.webp" alt="Certificado-Conserflow-ISO-14001" height="400">
                    <p>
                        Diseño, desarrollo, fabricación de equipos de medición, control regulación de
                        líquidos y gases, ensamble de gabinetes de control, para la industria de
                        hidrocarburos, desde la comercialización de materiales, componentes y refacciones,
                        hasta el desarrollo de proyectos integrales
                    </p>
                </div>
                <div class="col">
                    <p class="mb-1 mt-2 h5">Sociedad Americana de Ingenieros Mecánicos (ASME)</p>
                    <img class="border rounded px-2 my-2" @click="openModal('img/certificaciones/Certificado-ASME-2022.webp','Calidad')" src="img/certificaciones/Certificado-ASME-2022.webp" alt="Certificado-Conserflow-ISO-14001" height="400">
                    <p>
                        Fabricación de recipientes a presión en la ubicación mencionada y en sitios de campo controlados por la ubicación mencionada.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div id="myModal" ref="myModal" class="modal1">
        <span class="mt-4 close1" @click="closeModal">&times;</span>
        <img class="modal-content1" id="imgModal" ref="imgModal" height="600">
        <div id="modal_desc" ref="modal_desc"></div>
    </div>
</main>
</template>

<script>
export default
{
    data()
    {
        return {
            url: "sgi/procedimientos/",
        };
    },
    methods:
    {
        /*
         * Abrir el modal con la imagen seleccionada
         */
        openModal(src, desc)
        {
            this.$refs.myModal.style.display = "block";
            this.$refs.imgModal.src = src;
            this.$refs.modal_desc.innerHTML = desc;
        },
        closeModal()
        {
            this.$refs.myModal.style.display = "none";
        }
    },
    mounted()
    {},
};
</script>

<style scoped>
img
{
    cursor: pointer;
}
.modal1 {
    display: none;
    position: fixed;
    z-index: 3;
    padding-top: 100px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0, 0, 0);
    background-color: rgba(0, 0, 0, 0.9);
}

.modal-content1 {
    margin: auto;
    display: block;
    width: auto;
}

#caption1 {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 700px;
    text-align: center;
    color: #ccc;
    padding: 10px 0;
    height: 150px;
}

#animation {
    -webkit-animation-name: zoom;
    -webkit-animation-duration: 0.6s;
    animation-name: zoom;
    animation-duration: 0.6s;
}

#imgModal {
    max-width: 90vw;
}

@-webkit-keyframes zoom {
    from {
        -webkit-transform: scale(0)
    }

    to {
        -webkit-transform: scale(1)
    }
}

@keyframes zoom {
    from {
        transform: scale(0)
    }

    to {
        transform: scale(1)
    }
}

.close1 {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
}

.close1:hover,
.close1:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
}

@media only screen and (max-width: 700px) {
    .modal-content {
        width: 100%;
    }
}
</style>
