<script setup>
import {computed} from 'vue';
import {Link} from '@inertiajs/vue3';

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
    iconName: {
        type: String
    }
});

const iconClasses = computed(() => [
    'fa-solid',
    props.iconName,
    props.disabled ? 'text-gray-300' : (props.active ? 'text-cyan-700' : 'text-cyan-600'),
    'px-2',
    ...(!props.disabled ? ['dark:text-gray-400', 'group-hover:text-cyan-700', 'dark:group-hover:text-gray-300'] : [])

]);

const classes = computed(() => {
    if (props.disabled) {
        return 'flex items-center group block w-full pl-3 pr-4 py-2 text-left text-base font-medium text-sm text-gray-300 focus:outline-none transition duration-150 ease-in-out';
    }
    return props.active
        ? 'flex items-center group block w-full pl-3 pr-4 py-2 text-left text-base font-medium text-sm text-gray-700 bg-gray-100 focus:outline-none transition duration-150 ease-in-out'
        : 'flex items-center group block w-full pl-3 pr-4 py-2 text-left text-base font-medium text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition duration-150 ease-in-out'
});
</script>

<template>
    <Link :href="href" :class="classes" :disabled="disabled">
        <i :class="iconClasses"></i>
        <span class="px-2">
            <slot/>
        </span>
    </Link>
</template>
