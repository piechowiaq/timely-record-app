<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import {Link} from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">

        <Link :href="route('welcome')" class="justify-center items-center flex-col flex">
            <ApplicationLogo class="w-20 h-20"/>
            <p class="ml-2 text-2xl font-bold whitespace-nowrap tracking-widest text-gray-600"><span
                class="text-cyan-600 ">TIMELY</span> RECORD</p>
        </Link>


        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot/>
        </div>
    </div>
</template>
