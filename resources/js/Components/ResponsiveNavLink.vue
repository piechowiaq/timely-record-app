<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
    disabled: {
        type: Boolean,
    },
});

const classes = computed(() => {
    if (props.disabled) {
        return "block w-full pl-3 pr-4 py-2 border-l-4 border-gray-400 dark:border-gray-400 text-left text-base font-medium text-gray-300 dark:text-gray-300 bg-gray-50 dark:bg-cyan-900/50 focus:outline-none focus:text-cyan-800 dark:focus:text-cyan-200 focus:bg-cyan-100 dark:focus:bg-cyan-900 focus:border-cyan-700 dark:focus:border-cyan-300 transition duration-150 ease-in-out";
    }
    return props.active
        ? "block w-full pl-3 pr-4 py-2 border-l-4 border-cyan-600 dark:border-cyan-600 text-left text-base font-medium text-cyan-700 dark:text-cyan-300 dark:bg-cyan-900/50 focus:outline-none focus:text-cyan-800 dark:focus:text-cyan-200 focus:bg-cyan-100 dark:focus:bg-cyan-900 focus:border-cyan-700 dark:focus:border-cyan-300 transition duration-150 ease-in-out"
        : "block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out";
});
</script>

<template>
    <Link :href="href" :class="classes" :disabled="disabled">
        <slot />
    </Link>
</template>
