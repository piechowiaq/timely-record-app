<script setup>
import { onMounted, ref } from "vue";

defineProps({
    modelValue: {
        type: [String, Number],
        required: true,
    },
});

defineEmits(["update:modelValue"]);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute("autofocus")) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-cyan-600 dark:focus:ring-cyan-600"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
    />
</template>
