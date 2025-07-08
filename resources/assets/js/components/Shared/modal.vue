<template>
<!-- Modal -->
<div :class="{'ver-mostrar' : mostrar}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
    <div :class="clase" class="modal-dialog modal-dialog-scrollable" style="transform: none">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">{{header}}</h5>
                <button @click="cerrar" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <slot name="body" />
            </div>
            <div class="modal-footer">
                <slot name="footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrrar</button>
                </slot>
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default
{
    props: ["header", "mostrar", "lg", "xl"],
    methods:
    {
        cerrar()
        {
            this.$emit("cerrar")
        }
    },
    computed:
    {
        clase()
        {
            if (this.lg) return "modal-lg";
            if (this.xl) return "modal-xl";
        }
    }
}
</script>

<style scoped>
.moda-dialog {
    transform: 0.3s ease-out;
    transform: none;
}
</style>
