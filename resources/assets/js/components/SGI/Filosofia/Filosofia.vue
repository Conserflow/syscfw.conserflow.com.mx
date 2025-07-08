<template>
<main class="main">
    <div class="">
        <div class="card">

            <div class="card-body">
                <!-- mision, vision, valores -->
                <section class="mision_vision text-center">
                    <div class="">
                        <img class="img-filosofia" src="img/Filosofia/Filosofia.png" alt="">
                        <!-- <img class="img-filosofia img-2" src="img/Filosofia/VISION.png" alt="">
                        <img class="img-filosofia img-3" src="img/Filosofia/VALORES.png" alt=""> -->
                    </div>
                </section>
                <div class="spacer"></div>
                <div class="row">
                    <div class="col text-center">
                            <img class="img-fluid" src="/img/Filosofia/Politica-objetivos.webp" alt="Politica Integral">
                    </div>
                </div>
                <div class="spacer"></div>
                <section class="resultados">

                    <div class="accordion" id="accordionExample">
                        <div class="card">
                            <div class="card-header bg-white" id="headingOne">
                                <h2 class="mb-0">
                                    <button class="btn-informe btn btn-link btn-block" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        INFORME DE LOGRO DE OBJETIVOS
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                <div class="card-body">
                                    <iframe class="informe border rounded" src="docs/SGI/Informe Logro de Objetivos Integrales.pdf#toolbar=0&navpanes=0&scrollbar=0" frameborder="0"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="container politica text-center">
                    <div class="spacer"></div>
                    <img class="img-sombra" src="/img/Filosofia/Sombra.png" alt="Sombra">
                </section>
                <div class="spacer"></div>

                <section class="objetivos text-center mx-auto">

                </section>
            </div>
        </div>
    </div>
</main>
</template>

<style scoped>
.spacer {
    height: 5rem;
}

.container-valores {
    display: flex;
    max-width: 100vw;
    justify-content: center;
    flex-wrap: wrap;
}

.container-valores img {
    height: 18rem;
}

.img-2 {
    margin-top: 6rem;
}

.img-3 {
    margin-top: 12rem;
}

.img-sombra {
    height: 10rem;
}

.calidad {
    color: #D59300;
}

.seguridad {
    color: #EA5B2A;
}

.ambiente {
    color: #47AD4C;
}

.salud {
    color: #36709D;
}

.img-conser {
    width: 5rem;
    background-image: url("/img/conserflow.png");
    display: inline-flex;
    background-size: cover;
    height: 1.5rem;
}

.img-politica {
    width: 25rem;
}

.objetivos_titulo {
    width: 28rem;
}

.objetivos {
    width: 70%;
}

.img-iconos {
    margin-top: 1rem;
    height: 10rem;
}

.img-sombra {
    height: 8rem;
}

.informe {
    width: 100%;
    min-height: 100vh;
}
.btn-informe
{
    font-size: 1rem;
    font-weight: bold;
    text-align: center;
    color: #0070c0;
}
@media only screen and (max-width: 910px) {
    .img-2 {
        margin-top: 0rem;
    }

    .img-3 {
        margin-top: 0rem;
    }

    .img-sombra {
        height: 6rem;
    }

    .objetivos {
        width: 100%;
    }

    .objetivos {
        width: 100%;
    }

    .img-iconos {
        height: 10rem;
    }

    .img-sombra {
        height: 8rem;
    }

}
</style>

<script>
export default
{
    data()
    {
        return {};
    },
    methods:
    {},
    mounted()
    {},
};
</script>
