<template>
<main class="main">
    <div class="card">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Procedimientos 2
        </div>
        <div class="card-body">
            <div class="accordion" id="accordionDirectorio">
                <div class="card" :key="i" v-for="(d,i) in departamentos">
                    <div class="card-header bg-dark" :id="'heading'+d.name">
                        <h5 class="mb-0">
                            <button class="text-white btn btn-link" type="button" data-toggle="collapse" :data-target="'#collapse'+d.name" aria-expanded="false" :aria-controls="'collapse'+d.name">
                                <span class="h5">{{d.path}}</span>
                            </button>
                        </h5>
                    </div>

                    <div :id="'collapse'+d.name" class="collapse" :aria-labelledby="'heading'+d.name" data-parent="#accordionDirectorio">
                        <div class="card-body">
                            <directorio-dpto :path="d.path"></directorio-dpto>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</template>
<style>
.btn-link
{
    text-decoration: none;
}
.btn-link:hover
{
    text-decoration: none;
}
</style>
<script>
const DirectorioDepto = r => require.ensure([], () => r(require('./DirectorioDepto.vue')), 'sgi');

export default
{
    data()
    {
        return {
            url: "sgi/procedimientos/",
            departamentos: [],
        };
    },
    components:
    {
        "directorio-dpto": DirectorioDepto
    },
    methods:
    {

    },
    mounted()
    {
        this.departamentos = [
        {
            name: "compras",
            path: "Compras"
        },
        {
            name: "rh",
            path: "Recursos Humanos"
        },
        {
            name: "ti",
            path: "TI"
        }, ];

    },
};
</script>
