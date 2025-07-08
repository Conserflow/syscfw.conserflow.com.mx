<template>
<main class="main">
    <div class="">
        <div class="card" style="min-height:80vh">
            <div class="card-header">
                <i class="fa fa-align-justify"></i>Reporte General de Compras
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-2 form-control-label">Proyecto</label>
                    <div class="col-md-6">
                        <v-select label="nombre_corto" multiple :options="listaProyectos" v-model="proyectos">
                        </v-select>
                    </div>
                    <button class="btn btn-success" @click="DescargarReporte()">
                        <i class="fas fa-file-excel mr-1"></i> Exportar
                    </button>
                </div>
            </div>
        </div>

    </div>
</main>
</template>

<script>
var config = require('../../Herramientas/config-vuetables-client').call(this);
export default
{
    data()
    {
        return {
            listaProyectos: [],
            proyectos:[],
        }
    },
    methods:
    {
        /**
         * Obtener todos los proyectos
         */
        ObtenrProyectos()
        {
            axios.get('generales/proyectos/asd').then(res =>
            {
                this.listaProyectos = res.data.proyectos;
            });
        },

        /*
         * Generar reporte
         */
        DescargarReporte()
        {
            const ids=this.proyectos.reduce((ids,p)=>ids+=`${p.id}&`,"");
            if (this.proyectos == null)
            {
                toastr.warning('Seleccione un proyecto');
                return;
            }
            if (this.proyectos.length == 0)
            {
                toastr.warning('Seleccione un proyecto');
                return;
            }
            window.open("compras/reporte/generalcompras/" + ids, '_blank');
        },

    },
    mounted()
    {
        this.ObtenrProyectos();
    }
}
</script>
