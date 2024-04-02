<script setup>

import {Head, Link, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdateWorkspaceInformationForm from "@/Pages/Workspaces/Partials/UpdateWorkspaceInformationForm.vue";
import DeleteWorkspaceForm from "@/Pages/Workspaces/Partials/DeleteWorkspaceForm.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";


const props = defineProps({
    workspace: {
        type: Object,
        required: true
    },

})

const projectId = usePage().props.auth.user.project_id;


</script>

<template>
    <Head title="Workspace"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-white leading-tight">Edit Workspace</h2>
        </template>

        <div class="px-2 pb-2">
            <div class="dark:bg-gray-700 dark:text-gray-400 space-y-2 scroll-region">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow">
                    <UpdateWorkspaceInformationForm :workspace="workspace" class="max-w-xl"/>
                </div>
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-xl flex justify-between">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Workspace Registries</h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Synchronize workspace registries.
                            </p>
                        </header>


                        <Link :href="route('workspaces.index-registries', workspace.id)">
                            <SecondaryButton class="hover:border-amber-600 border-amber-600">
                                Sync Registries
                            </SecondaryButton>
                        </Link>


                    </div>
                </div>
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow">
                    <DeleteWorkspaceForm :workspace="workspace" class="max-w-xl"/>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

</template>

