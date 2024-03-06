<script setup>
import {Head, Link, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    workspaces: {
        type: Object,
    },

});

const canManageProject = usePage().props.auth.canManageProject;

const projectId = usePage().props.auth.user.project_id;


const getWorkspaceBorderColor = (workspace) => {
    if (workspace.upToDateRegistriesMetrics === 100) return 'border-green-500';
    if (workspace.upToDateRegistriesMetrics > 90) return 'border-green-300';
    if (workspace.upToDateRegistriesMetrics >= 80) return 'border-yellow-300';
    return 'border-red-300';
};

</script>

<template>
    <Head title="Project"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-white dark:text-gray-700 leading-tight">Project Dashboard</h2>
        </template>
        {{ workspaces }}
        <div class="px-2 pb-2">

            <div v-if="!workspaces.length">
                <section
                    class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow flex-grow">

                    <p class="italic text-red-400 text-xs mb-4">
                        No
                        workspaces
                        associated with this project or with your credentials.
                    </p>


                    <Link v-if="canManageProject"
                          :href="route('workspaces.create', projectId )"
                          class="text-cyan-600 hover:text-cyan-700 text-sm">
                        Create Workspace
                    </Link>
                </section>
            </div>
            <div v-else class="grid md:grid-cols-2 gap-2 grid-cols-1">
                <section v-for="workspace in workspaces" :key="workspace.id"
                         class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow flex-grow justify-between flex">
                    <header>
                        <Link :href="route('workspaces.dashboard', { project: projectId, workspace: workspace.id} )">
                            <h2 class="text-lg font-medium hover:text-cyan-700 text-gray-900 dark:text-gray-100">
                                {{ workspace.name }}
                            </h2>
                        </Link>
                        <p v-if="workspace.location" class="text-sm mb-4 text-gray-400">{{ workspace.location }}</p>
                    </header>
                    <section class="grid grid-cols-2 gap-4 text-center">
                        <div :class="`border-2 p-2 flex flex-col ${getWorkspaceBorderColor(workspace)}`">
                            <header class="text-gray-600 text-sm">Registries</header>
                            <p class="font-medium text-2xl text-gray-500 mt-auto">{{
                                    workspace.upToDateRegistriesMetrics
                                }}%</p>


                        </div>
                        <div class="border-2 p-2 bg-gray-50 border-red-50 flex flex-col">
                            <header class="text-gray-200 text-sm">Trainings</header>
                            <p class="font-medium text-2xl text-gray-200  mt-auto">0%</p>
                        </div>
                    </section>


                </section>

            </div>


        </div>
    </AuthenticatedLayout>
</template>

