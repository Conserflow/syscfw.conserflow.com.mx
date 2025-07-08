<template>
<button class="btn rounded-circle btn btn-light mx-1" :class="positionClass" @click="$router.back()">
    <i class="fas fa-arrow-left"></i>
</button>
</template>

<script>
export default
{
    props:
    {
        end:
        {
            type: Boolean,
            required: false,
            default:false,
        },
    },
    computed:
    {
        positionClass()
        {
            if (this.end) return "float-right";
        },
    }
}
</script>
