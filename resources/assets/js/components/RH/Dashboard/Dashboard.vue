<template>
<main class="main">
    <div>
        <div class="row">
            <div class="col">
                <resguardos ref="resguardos"></resguardos>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <aniversario-empleados ref="aniversario"></aniversario-empleados>
            </div>
        </div>
    </div>
</main>
</template>

<script>
const AniversarioEmpleados = r => require.ensure([], () => r(require('./AniversarioEmpleados.vue')), 'rh');
const Resguardo = r => require.ensure([], () => r(require('../Empleados/Resguardos.vue')), 'rh');

export default
{
    data()
    {
        return {
            isLoading: false,
            listaPermisos: [],
            widgets:
            {
                ver_resguardo: false,
            }
        }
    },
    components:
    {
        'resguardos': Resguardo,
        AniversarioEmpleados,
    },
    methods:
    {},
    mounted()
    {}
}
</script>
