<template>
<button class="btn btn-dark mx-1" :class="positionClass" @click="$emit('click')">
    <i :class="icon" class="mr-1"></i>{{title}}
</button>
</template>

<script>
export default
{
    props:
    {
        title:
        {
            type: String,
            required: false,
        },
        icon:
        {
            type: String,
            required: false,
        },
        end:
        {
            type: Boolean,
            required: false,
            default:true,
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
