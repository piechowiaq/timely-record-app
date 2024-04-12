<script setup>
import { defineEmits, defineProps } from "vue";

defineProps(["modelValue"]);

defineEmits(["update:modelValue", "reset"]);
</script>

<template>
    <div class="mb-2 flex items-center">
        <input
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            class="h-8 border-gray-200 px-6 py-3 text-sm dark:border-gray-400 dark:bg-gray-800"
            name="search"
            placeholder="Search…"
            type="text"
        />
        <button
            class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-cyan-600"
            type="button"
            @click="$emit('reset')"
        >
            Reset
        </button>
    </div>
</template>

<style scoped></style>
