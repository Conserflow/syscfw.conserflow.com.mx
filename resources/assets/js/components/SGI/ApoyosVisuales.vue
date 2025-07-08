<template>
<main class="main">
    <div class="card" style="min-height:90vh">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Apoyos Visuales
        </div>
        <div class="card-body">
            <div class="accordion" id="accordionExample">
                <div class="card" v-for="(nombre, index) in files" :key="index">
                    <div class="card-header" :id="'heading' + index">
                        <h2 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse" :data-target="'#collapse' + index" aria-expanded="true" :aria-controls="'collapse' + index">
                                {{ obtenerNombreArchivo(nombre) }}
                            </button>
                        </h2>
                    </div>
                    <div :id="'collapse' + index" class="collapse" :aria-labelledby="'heading' + index" data-parent="#accordionExample">
                        <div class="card-body">
                            <div v-if="esPDF(nombre)" class="text-center">
                                <embed :src="`/img/Visuales/${nombre}`" type="application/pdf" width="100%" height="600px" />
                            </div>
                            <div v-else class="text-center">
                                <img :src="`/img/Visuales/${nombre}`" class="img-fluid" :alt="nombre">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
</template>

<script>
export default
{
    data()
    {
        return {
            files: [],
        };
    },
    methods:
    {
        /**
         * Obtener todos los archivos y directorios de la ruta ingresada
         */
        cargar()
        {
            this.files = [
		"AV-CSF-003 Como se Integra Nuestra Política Integral.jpg",
                "AV-CSF-006 Día Mundial de la Calidad.jpg",
                "AV-CSF-007 Reglamento Interior de Trabajo.jpg",
                "AV-CSF-008 Política de Prevención de Riesgos Psicosociales.jpg",
                "AV-CSF-009 Resultados de la Aplicación del Cuestionario de Identificación de Factores Psicosociales.jpg",
                "AV-CSF-010 Control de Acceso.jpg",
                "AV-CSF-011 Buzón de Quejas, Sugerencias, Felicitación en Conserflow.jpg",
                "AV-CSF-012 Salidas No Conformes.jpg",
                "AV-CSF-014 Reglamento Interno de Seguridad, Salud y Medio Ambiente.jpg",
                "AV-CSF-016 Funciones de la Comisión de Seguridad e Higiene.jpg",
                "AV-CSF-018 Funciones de la Brigada Multifuncional.jpg",
                "AV-CSF-019 Investigación de Accidentes.jpg",
                "AV-CSF-020 Plan Atención a Emergencias en Conserflow.jpg",
                "AV-CSF-021 Plan de Emergencia Ambiental.jpg",
                "AV-CSF-022 Teléfonos de Emergencia.jpg",
                "AV-CSF-024 Gestión de Residuos.jpg",
                "AV-CSF-025 NOM-009-STPS-2011 Condiciones de Seguridad para Realizar Trabajos en Altura.jpg",
                "AV-CSF-026 NOM-017-STPS-2008 Uso, Cuidado y Mantenimiento del EPP.jpg",
                "AV-CSF-027 NOM-018-STPS-2015 Sistema Armonizado para la Identificación y Comunicación de Peligros y Riesgos  en los Centros de Trabajo.jpg",
                "AV-CSF-028 NOM-019-STPS-2011-Contitución, Integración, Organización y Funcionamiento de las Comisiones de Seguridad e Higiene.jpg",
                "AV-CSF-029 NOM-026-STPS-2008 Colores y Señales de Seguridad.jpg",
                "AV-CSF-030 NOM-052-SEMARNAT-2005 Caracteristicas, Procedimiento de Identificación, Clasificación y los Listados de los Residuos Peligrosos.jpg",
                "AV-CSF-031 Hojas de Seguridad.jpg",
                "AV-CSF-032 Seguridad en el Uso de Escaleras.jpg",
                "AV-CSF-033 Cuidado de las manos.jpg",
                "AV-CSF-034 Día Internacional de la Eliminación de la Violencia Contra la Mujer.jpg",
                "AV-CSF-035 Orden y Limpieza.jpg",
                "AV-CSF-037 La Seguridad Industrial es Responsabilidad de Todos.jpg",
                "AV-CSF-038 Estres Termico por Calor.jpg",
                "AV-CSF-039 Riesgos Electricos.jpg",
                "AV-CSF-040 Prevención de Caidas.jpg",
                "AV-CSF-041 Quemaduras.jpg",
                "AV-CSF-042 Que Hacer en Caso de Intoxicación.jpg",
                "AV-CSF-043 Seguridad de Izaje.jpg",
                "AV-CSF-044 Día Internacional de los Derechos Humanos.jpg",
                "AV-CSF-045 Cilindros de gas comprimido.jpg",
                "AV-CSF-046 Cuidado de mis Ojos.jpg",
                "AV-CSF-047 Prevención de Incendios.jpg",
                "AV-CSF-048 La Ergonomia.jpg",
                "AV-CSF-049 Protejase en su Trabajo.jpg",
                "AV-CSF-050 Protección Respiratoria.jpg",
                "AV-CSF-051 Trabajos de Oxicorte.jpg",
                "AV-CSF-052 La Biosfera (Flora y Fauna en Conserflow).jpg",
                "AV-CSF-053 Tecnicas Basicas de Cargas Manuales.jpg",
                "AV-CSF-054 Agotamiento por Calor.jpg",
                "AV-CSF-055 Riesgo de Hacer Bromas.jpg",
                "AV-CSF-056 Higiene en los Alimentos.jpg",
                "AV-CSF-057 Día Internacional de la No Violencia.jpg",
                "AV-CSF-058 Contaminación del Suelo.jpg",
                "AV-CSF-063 Diabetes Mellitus.jpg",
                "AV-CSF-065 RCP-(Reanimación-Cardiopulmonar).jpg",
                "AV-CSF-067 Cultura del Agua.jpg",
                "AV-CSF-069 Contaminación por Plastico.jpg",
                "AV-CSF-070 Que hacer en una convulsión.jpg",
                "AV-CSF-071 Pausas-Activas.jpg",
                'AV-CSF-004 Procedimientos Directriz del Sistema de Gestión Integral.pdf',
                'AV-CSF-013 La Comunicación en Conserflow.jpg',
                'AV-CSF-020 Plan Atención a Emergencias en Conserflow.jpg',
                'AV-CSF-023 Trabajo con Orden y Limpieza.jpg',
                'AV-CSF-036 Uso del Lavaojos.jpg',
                'AV-CSF-059 Día de la No Violencia Contra las Mujeres y Niñas.jpg',
                'AV-CSF-060 Maniobra de Heimlich.jpg',
                'AV-CSF-061 Heridas.jpg',
                'AV-CSF-062 EPOC (Enfermedad Pulmonar Obstructuva Cronica).jpg',
                'AV-CSF-064 Prevencion-y Manejo de Picaduras y Mordeduras de Animales Ponzoñozos.jpg',
                'AV-CSF-066 La importancia de la Vacunación.jpg',
                'AV-CSF-068 Que es un Traumatimo Craneoncefálico.jpg',
                'AV-CSF-072 Ahorro Energético.jpg',
                'AV-CSF-073 Contexto de la Organización y Partes Interesadas.jpg',
                'AV-CSF-074 Preparados y Organizados Evitamos Desastres.jpg',
                'AV-CSF-075 Recomendaciones a seguir en caso de un sismo.jpg',
                'AV-CSF-076-Como funciona un arnés.jpg',
                'AV-CSF-077-Gracias por tu participación.jpg',
                'AV-CSF-078 Manejo de Extintores.pdf',
                'AV-CSF-079 Que hacer en caso de un derrame en el suelo.jpg',
                'AV-CSF-080 Socorrismo y Primeros Auxilios.jpg',
                'AV-CSF-081 Hipertensión.jpg',
            ]
        },
        obtenerNombreArchivo: function (archivo)
        {
            // Obtener el nombre del archivo sin la extensión y en mayúsculas
            let nombre = archivo.split('.').slice(0, -1).join('.');
            return nombre.toUpperCase();
        },
        esPDF: function (archivo)
        {
            // Verificar si el archivo es un PDF
            return archivo.toLowerCase().endsWith('.pdf');
        }
    },
    mounted()
    {
        this.cargar();
    },
};
</script>
