<script setup>
import {Head, Link, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    workspaces: {
        type: Object,
    }
});

const projectId = usePage().props.auth.user.project_id;

</script>

<template>
    <Head title="Project"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-white dark:text-gray-700 leading-tight">Project Dashboard</h2>
        </template>

        <div class="px-2 pb-2">


            <div class="grid md:grid-cols-2 gap-2 grid-cols-1">
                <section v-if="!workspaces.length" class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow flex-grow">
                    <p class="italic text-red-400 text-xs mb-4">
                        No
                        workspaces
                        associated with this project.
                    </p>
                    <Link :href="route('workspaces.create', projectId )"
                          class="text-cyan-600 hover:text-cyan-700 text-sm">
                        Create Workspace
                    </Link>
                </section>

                <section v-else v-for="workspace in workspaces" :key="workspace.id"
                         class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow flex-grow">
                    <header>

                        <Link :href="route('workspaces.dashboard', { project: projectId, workspace: workspace.id} )">
                            <h2 class="text-lg font-medium hover:text-cyan-700 text-gray-900 dark:text-gray-100">
                                {{ workspace.name }}
                            </h2>
                        </Link>

                    </header>
                    <p v-if="workspace.location" class="text-sm mb-4 text-gray-400">{{ workspace.location }}</p>
                </section>

            </div>

        </div>
    </AuthenticatedLayout>
</template>

