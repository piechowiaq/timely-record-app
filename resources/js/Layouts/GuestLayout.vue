<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 dark:bg-gray-900 sm:justify-center sm:pt-0"
    >
        <Link
            :href="route('welcome')"
            class="flex flex-col items-center justify-center"
        >
            <ApplicationLogo class="h-20 w-20" />
            <p
                class="ml-2 whitespace-nowrap text-2xl font-bold tracking-widest text-gray-600"
            >
                <span class="text-cyan-600">TIMELY</span> RECORD
            </p>
        </Link>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md dark:bg-gray-800 sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
