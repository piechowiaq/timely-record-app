<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block text-sm text-gray-500 dark:text-gray-300">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot/></span>
    </label>
</template>
