<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full pl-3 pr-4 py-3 text-left text-base font-medium text-cyan-600 bg-white focus:outline-none transition duration-150 ease-in-out'
        : 'block w-full pl-3 pr-4 py-3 text-left text-base font-medium text-white hover:text-cyan-600 hover:bg-white focus:outline-none transition duration-150 ease-in-out'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
