<template>
<button type="button" class="dropdown-item btn-sm" @click="$emit('click')">
    <i :class="icon" class="mr-1"></i> {{title}}
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
            required: true,
        },
        icon:
        {
            type: String,
            required: true,
        },

    },
    methods:
    {},
}
</script>
