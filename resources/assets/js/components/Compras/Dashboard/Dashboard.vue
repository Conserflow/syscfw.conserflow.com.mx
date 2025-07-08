<template>
<main class="main">
    <div class="container-fluid">
        <vue-element-loading :active="isLoading" />
        <div class="row">
            <div class="col-md-12" v-show="widgets.autorizarequisicion">
                <autorizarequisicion ref="autorizarequisicion"></autorizarequisicion>
            </div>
        </div>
    </div>
</main>
</template>

<script>
const Requisiciones = r => require.ensure([], () => r(require('./Requisiciones.vue')), 'compras');
const modulo_id = 5;

export default
{
    data()
    {
        return {
            isLoading: false,
            listaPermisos: [],
            widgets:
            {
                autorizarequisicion: true,
                servicios: true,
                precios2: false,
            }
        }
    },
    components:
    {
        'autorizarequisicion': Requisiciones,

    },
    methods:
    {
        getListaPermisos()
        {
            var id = 25;
            this.isLoading = true;
            let me = this;
            axios.post('/permisosdashboard/porusuariomodulo',
                {
                    modulo_id: modulo_id
                }).then(response =>
                {
                    me.listaPermisos = response.data;
                    me.isLoading = false;

                    if (me.listaPermisos.indexOf('autorizarequisicion') >= 0)
                    {
                        me.widgets.autorizarequisicion = true;
                        var childautorizarequisicion = this.$refs.autorizarequisicion;
                    }
                })
                .catch(function (error)
                {
                    console.log(error);
                });
        }
    },
    mounted()
    {
        this.getListaPermisos();
    }
}
</script>
