<script setup>


const props = defineProps({
    projectUsers: {
        type: Array,
        required: true,
    },
    users: {
        type: Array,
        required: true,
    },
});


</script>

<template>

    <div>
        {{ projectUsers }} <br> {{ users }}
    </div>


</template>

<style scoped>

</style>
