<template>
<div class="btn-group" role="group">
    <div class="btn-group dropup" role="group">
        <button type="button" class="btn btn-sm btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-grip-horizontal"></i> Acciones
        </button>
        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
            <slot name="options" />
        </div>
    </div>
</div>
</template>

<script>
export default
{
    props:
    {

    },
    methods:
    {},
}
</script>

<style>
.btn-outline-dark
{
    color: var(--body-color);
}
</style>