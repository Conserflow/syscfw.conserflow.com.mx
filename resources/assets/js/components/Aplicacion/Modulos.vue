<template>
<main>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    </head>

    <div class="modulos_modulo-container">
        <template v-for="modulo in arrayModulos">
            <div :key="modulo.nombre">
                <div :style="'background-color:'+modulo.color" style="background-color:red" @click="$emit('click',modulo.id,modulo.submenu,modulo.nombre, modulo.page)" class="modulos_modulo-card text-center border" :title="modulo.nombre">
                    <a>
                        <div class="" :title="modulo.nombre">
                            <p class="text-danger">{{modulo.icon}}</p>
                            <span class="text-white modulo-icon material-symbols-outlined">{{modulo.icono}}</span>
                        </div>
                    </a>
                </div>
                <p class="mt-1 text-center " v-text="modulo.nombre"></p>
            </div>
        </template>
    </div>
</main>
</template>

<style>

</style>

<script>
export default
{
    data()
    {
        return {
            id: 0,
            arrayModulos: []
        }
    },
    methods:
    {
        getRutas()
        {
            let me = this;
            axios.get('/modulos/por-usuario').then(function (response)
                {
                    me.arrayModulos = response.data;
                })
                .catch(function (error)
                {
                    console.log(error);
                });
        }
    },
    mounted()
    {
        this.getRutas();
    }
}
</script>
