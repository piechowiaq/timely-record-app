<script setup>
import {Head, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    workspace: {
        type: Object,
    },
    project: {
        type: Object
    }
})

const page = usePage();

</script>

<template>
    <Head title="Workspace"/>

    <AuthenticatedLayout :workspace="workspace">
        <template #header>
            <h2 class="text-white dark:text-gray-700 leading-tight">Dashboard</h2>
        </template>

        <div class="px-2 pb-2">
            <div class="space-y-2">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow ">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Workspace Dashboard</h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Instruction ##.
                            </p>
                        </header>
                        <div class="py-4">
                            <p>Hello from dashboard {{  }}</p>
                        </div>

                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

