<template>
<main class="main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <equiposcalib></equiposcalib>
            </div>
        </div>

    </div>
</main>
</template>

<script>

const EquiposCalibracion = r => require.ensure([], () => r(require('./EquiposCalib.vue')), 'calib');
const modulo_id = 8;

export default
{
    data()
    {
        return {
            isLoading: false,
            listaPermisos: [],
        }
    },
    components:
    {
        'equiposcalib': EquiposCalibracion,
    },
    methods:
    {

    },
    mounted()
    {

    }
}
</script>
